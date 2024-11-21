<?php

namespace App\Http\Controllers;

use App\Models\Perpustakaan;
use Illuminate\Support\Facades\Auth;

class PerpustakaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Cek apakah user ada
        if (!$user) {
            return response()->json([
                'error' => 'Pengguna tidak terautentikasi'
            ], 401);
        }

        // Ambil data perpustakaan milik user dengan relasi buku
        $perpustakaan = Perpustakaan::where('user_id', $user->id)
            ->with('buku') // Pastikan relasi 'buku' ada di model
            ->get();

        // Transformasi data perpustakaan untuk direspons
        $perpustakaanData = $perpustakaan->map(function ($item) {
            return [
                'id' => $item->id,
                'buku_id' => $item->buku_id,
                'judul' => optional($item->buku)->judul ?? 'Judul Tidak Tersedia',
                'cover' => optional($item->buku)->cover
                    ? url('storage/' . $item->buku->cover)
                    : 'Cover Tidak Tersedia',
            ];
        });

        // Jika tidak ada data
        if ($perpustakaanData->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada buku di perpustakaan',
                'total_buku' => 0,
                'perpustakaan' => []
            ]);
        }

        return response()->json([
            'total_buku' => $perpustakaanData->count(),
            'perpustakaan' => $perpustakaanData
        ]);
    }
}
