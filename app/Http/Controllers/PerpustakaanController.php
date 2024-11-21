<?php

namespace App\Http\Controllers;

use App\Models\Buku;

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
}
