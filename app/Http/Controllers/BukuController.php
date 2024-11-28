<?php

namespace App\Http\Controllers;

use App\Models\Bagian;
use App\Models\Buku;
use App\Models\Perpustakaan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil data dari model Buku dengan relasi bagian dan user
        $buku = Buku::with(['bagian', 'user' => function ($query) {
            $query->select('id', 'nama_pengguna');
        }])
            ->select('id', 'cover', 'judul', 'deskripsi', 'user_id')
            ->get()
            ->map(function ($item) {
                return [
                    'cover' => $item->cover,
                    'judul' => $item->judul,
                    'deskripsi' => $item->deskripsi,
                    'nama_pengguna' => $item->user->nama_pengguna,
                    'total_bagian' => $item->bagian->count(),
                ];
            });

        return response()->json([
            'bagian' => $buku,
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
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cover' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'judul' => 'required|max:255|string',
            'deskripsi' => 'required|string',
        ]);

        if ($request->file('cover')) {
            $validatedData['cover'] = $request->file('cover')->store('cover');
        }

        $validatedData['user_id'] = Auth::user()->id;

        $buku = Buku::create($validatedData);

        $user = Auth::user();
        $user->karya++;
        $user->save();

        return response([
            'message' => 'Buku berhasil ditambahkan',
            'buku' => $buku,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $buku = Buku::with(['bagian' => function ($query) {
            $query->select('id', 'buku_id', 'judul_bagian', 'tanggal_publikasi');
        }])->find($id);

        if (!$buku) {
            return response()->json([
                'message' => 'Buku tidak valid',
            ], 400);
        }

        // Pastikan user sudah login
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        // Tambahkan buku_id dan user_id ke dalam model Perpustakaan
        Perpustakaan::create([
            'buku_id' => $buku->id,
            'user_id' => $user->id,
        ]);

        // Format data untuk respons
        $response = [
            'buku' => [
                'cover' => $buku->cover,
                'judul' => $buku->judul,
                'bagian' => $buku->bagian->map(function ($bagian) {
                    return [
                        'judul_bagian' => $bagian->judul_bagian,
                        'tanggal_publikasi' => $bagian->tanggal_publikasi,
                    ];
                }),
            ],
        ];

        return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Buku $buku)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $buku = Buku::find($id);

        if (!$buku) {
            return response([
                'message' => 'Buku tidak ditemukan',
            ], 403);
        }

        if ($buku->user_id != auth()->user()->id) {
            return response([
                'message' => 'Anda tidak berhak mengubah buku ini',
            ], 403);
        }

        $firstData = [
            'cover' => 'image|file|max:2048',
            'judul' => 'max:255|string',
            'deskripsi' => 'max:255|string',
        ];

        $validatedData = $request->validate($firstData);

        if ($request->file('cover')) {
            Storage::delete($buku->cover);
            $validatedData['cover'] = $request->file('cover')->store('cover');
        }

        return response([
            'message' => 'Buku berhasil diupdate',
            'buku' => Buku::where('id', $buku->id)->update($validatedData),
        ], 200);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->get('q');

        if (!$searchTerm) {
            return response()->json([
                'message' => 'Harap masukkan kata kunci pencarian',
            ], 400);
        }

        $searchResults = Buku::where('judul', 'like', "%{$searchTerm}%")
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'bukus' => $searchResults,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Buku $buku)
    {
        // Temukan buku berdasarkan ID
        $book = Buku::find($buku->id);

        // Cek apakah buku ada
        if (!$book) {
            return response()->json([
                'message' => 'Buku tidak ditemukan',
            ], 404); // Mengubah status code menjadi 404 Not Found
        }

        // Cek apakah user yang sedang login adalah pemilik buku
        if ($book->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Anda tidak berhak mengubah buku ini',
            ], 403);
        }

        // Cek apakah ada bagian yang terkait dengan buku
        if ($book->bagian()->count() > 0) {
            // Jika ada, hapus bagian terlebih dahulu
            $book->bagian()->delete();
        }

        // Hapus buku
        $book->delete();

        // Kurangi kolom karya pada model user
        $user = Auth::user();
        $user->karya--;
        $user->save();

        return response()->json([
            'message' => 'Buku berhasil dihapus',
        ], 200);
    }

    public function baca($bukuId, $bagianId)
    {
        // Ambil buku berdasarkan ID
        $buku = Buku::find($bukuId);

        // Cek apakah buku ditemukan
        if (!$buku) {
            return response()->json([
                'message' => 'Buku tidak ditemukan',
            ], 404);
        }

        // Ambil bagian berdasarkan ID dan pastikan bagian tersebut terkait dengan buku
        $bagian = $buku->bagian()->find($bagianId);

        // Cek apakah bagian ditemukan
        if (!$bagian) {
            return response()->json([
                'message' => 'Bagian tidak ditemukan',
            ], 404);
        }

        // Kembalikan respons dengan judul buku, judul bagian, dan isi bagian
        return response()->json([
            'judul_buku' => $buku->judul,
            'judul_bagian' => $bagian->judul_bagian,
            'isi' => $bagian->isi, // Pastikan 'isi' adalah nama kolom di tabel Bagian
        ], 200);
    }
}
