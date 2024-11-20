<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $rules = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
            'nama_pengguna' => 'required|string',
            'nama_lengkap' => 'required|string',
            'confirm_password' => 'required|string|min:8|same:password',
        ]);

        $rules['password'] = bcrypt($rules['password']);

        $user = User::create($rules);

        return response([
            'user' => $user,
            'token' => $user->createToken('secret')->plainTextToken
        ]);
    }

    public function login(Request $request)
    {
        $rules = $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);

        if(!Auth::attempt($rules))
        {
            return response([
                'message' => 'Invalid Credentials'
            ], 403);
        }

        return response([
            'user' => auth()->user(),
            'token' => auth()->user()->createToken('secret')->plainTextToken
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response([
            'message' => 'Logged out'
        ], 200);
    }

    public function user()
    {
        return response([
            'user' => auth()->user()
        ], 200);
    }
    // Fungsi untuk mengupdate profil pengguna
    public function updateProfile(Request $request, $id)
    {
        // Cari user berdasarkan ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        // Validasi data input
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'nullable|string|max:255',
            'nama_pengguna' => 'nullable|string|max:255|unique:users,nama_pengguna,' . $id,
            'foto_pengguna' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Foto maksimal 2MB
            'current_password' => 'nullable|string|min:8',
            'new_password' => 'nullable|string|min:8|confirmed', // Password baru dengan konfirmasi
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update nama lengkap jika disediakan
        if ($request->has('nama_lengkap')) {
            $user->nama_lengkap = $request->nama_lengkap;
        }

        // Update nama pengguna jika disediakan
        if ($request->has('nama_pengguna')) {
            $user->nama_pengguna = $request->nama_pengguna;
        }

        // Update foto profil jika disediakan
        if ($request->hasFile('foto_pengguna')) {
            // Hapus foto lama jika bukan default
            if ($user->foto_pengguna && $user->foto_pengguna !== 'img/default_profile.png') {
                Storage::delete($user->foto_pengguna);
            }

            // Simpan foto baru
            $filePath = $request->file('foto_pengguna')->store('foto_pengguna');
            $user->foto_pengguna = $filePath;
        }

        // Ganti password jika disediakan
        if ($request->has('current_password') && $request->has('new_password')) {
            // Verifikasi password saat ini
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => 'Password saat ini salah'], 401);
            }

            // Validasi password baru harus dikonfirmasi
            if ($request->new_password !== $request->new_password_confirmation) {
                return response()->json(['message' => 'Konfirmasi password baru tidak sesuai'], 422);
            }

            // Update password baru
            $user->password = Hash::make($request->new_password);
        }

        // Simpan perubahan
        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diubah',
            'user' => $user,
        ], 200);
    }
}
