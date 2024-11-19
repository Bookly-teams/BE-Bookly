<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BagianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'bagians' => Bagian::orderBy('created_at', 'asc')->get() // Mengurutkan berdasarkan created_at secara descending
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $buku_id)
    {
        // Cek apakah buku ada
        $buku = Buku::findOrFail($buku_id);

        // Validasi input
        $validatedData = $request->validate([
            'judul_bagian' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        // Pastikan user adalah pemilik buku
        if ($buku->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk menambah bagian pada buku ini'
            ], 403);
        }

        // Siapkan data
        $validatedData['tanggal_publikasi'] = now();
        $validatedData['user_id'] = Auth::id();
        $validatedData['buku_id'] = $buku_id;

        // Buat bagian
        $bagian = Bagian::create($validatedData);

        return response()->json([
            'message' => 'Bagian berhasil ditambahkan',
            'bagian' => $bagian,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Bagian $bagian)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bagian $bagian)
    {
    //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $buku_id, $bagian_id)
    {
        // Cek apakah buku ada
        $buku = Buku::findOrFail($buku_id);

        // Cek apakah bagian ada dan milik buku yang sesuai
        $bagian = Bagian::where('id', $bagian_id)
            ->where('buku_id', $buku_id)
            ->firstOrFail();

        // Validasi input
        $validatedData = $request->validate([
            'judul_bagian' => 'sometimes|string|max:255',
            'isi' => 'sometimes|string',
        ]);

        // Pastikan user adalah pemilik buku
        if ($buku->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk mengupdate bagian pada buku ini'
            ], 403);
        }

        // Siapkan data yang akan diupdate (filter data yang ada)
        $updateData = array_filter($validatedData, function($value) {
            return $value !== null;
        });

        // Tambahkan updated_at
        $updateData['updated_at'] = now();

        // Update bagian
        $bagian->update($updateData);

        // Refresh data untuk mendapatkan data terbaru
        $bagian->refresh();

        return response()->json([
            'message' => 'Bagian berhasil diupdate',
            'bagian' => $bagian,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $buku_id, $bagian_id)
    {
        // Cek apakah buku ada
        $buku = Buku::findOrFail($buku_id);

        // Cek apakah bagian ada dan milik buku yang sesuai
        $bagian = Bagian::where('id', $bagian_id)
            ->where('buku_id', $buku_id)
            ->firstOrFail();

        // Pastikan user adalah pemilik buku
        if ($buku->user_id !== Auth::id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk menghapus bagian pada buku ini'
            ], 403);
        }

        // Simpan informasi bagian sebelum dihapus (opsional)
        $deletedBagian = $bagian;

        // Hapus bagian
        $bagian->delete();

        return response()->json([
            'message' => 'Bagian berhasil dihapus',
            'bagian' => $deletedBagian,
        ], 200);
    }
}
