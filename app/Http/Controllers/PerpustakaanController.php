<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Perpustakaan;
use Illuminate\Http\Request;

class PerpustakaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil ID user dari request
        $userId = auth()->id();

        // Mengambil semua buku yang telah dibaca oleh user, diurutkan berdasarkan waktu terakhir dibaca
        $books = Buku::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get(['judul', 'cover']);

        // Menghitung total histori yang telah dibaca oleh user
        $totalReadHistory = $books->count();

        // Mengembalikan response dengan data buku dan total histori
        return response([
            'message' => 'Data buku berhasil diambil',
            'data' => $books,
            'total_read_history' => $totalReadHistory,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'buku_id' => 'required|exists:bukus,id',
        ]);

        $buku_id = $request->buku_id;

        // Cek apakah buku sudah pernah ditambahkan sebelumnya
        $existingRecord = Perpustakaan::where('buku_id', $buku_id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Buku sudah ada di perpustakaan Anda',
            ], 400);
        }

        $perpustakaan = Perpustakaan::create([
            'buku_id' => $buku_id,
            'user_id' => auth()->id(),
        ]);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan',
            'data' => $perpustakaan,
        ], 201);
    }

    public function destroy($id)
    {
        $perpustakaan = Perpustakaan::where('buku_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$perpustakaan) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan di perpustakaan Anda',
            ], 404);
        }

        $perpustakaan->delete();

        return response()->json([
            'message' => 'Buku berhasil dihapus dari perpustakaan',
        ], 200);
    }
}
