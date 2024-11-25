<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // Menampilkan profil pengguna beserta buku dan bagian
    public function showProfile()
    {
        try {
            // Ambil data pengguna yang sedang login
            $user = Auth::user();

            // Validasi jika pengguna tidak terautentikasi
            if (!$user) {
                return response()->json([
                    'message' => 'User not authenticated',
                ], 401);
            }

            // Ambil informasi buku dan bagian terkait pengguna secara manual
            $buku = Buku::where('user_id', $user->id)->with('bagian')->get();

            // Validasi jika pengguna tidak memiliki buku
            if ($buku->isEmpty()) {
                return response()->json([
                    'message' => 'No books found for this user',
                ], 404);
            }

            // Menghitung jumlah karya berdasarkan jumlah buku
            $jumlah_karya = $buku->count();

            // Persiapkan data yang akan ditampilkan
            $data = [
                'foto_pengguna' => $user->foto_pengguna,
                'nama_lengkap' => $user->nama_lengkap,
                'nama_pengguna' => $user->nama_pengguna,
                'jumlah_karya' => $jumlah_karya,
                'created_at' => $user->created_at,
                'buku' => $buku->map(function ($book) {
                    return [
                        'cover' => $book->cover,
                        'judul' => $book->judul,
                        'jumlah_bagian' => $book->bagian()->count(),
                        'deskripsi' => $book->deskripsi,
                    ];
                }),
            ];

            // Kembalikan respons JSON
            return response()->json($data, 200);
        } catch (\Exception $e) {
            // Tangani error yang tidak terduga
            return response()->json([
                'message' => 'An error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
