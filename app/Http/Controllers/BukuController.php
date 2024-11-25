<?php

namespace App\Http\Controllers;

use App\Models\Buku;
<<<<<<< HEAD
use App\Models\Bagian;
=======
use App\Models\Bagian; 
>>>>>>> 6de89285048dc809fa77af3558c89973c2e07806
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
<<<<<<< HEAD
    {
        // Ambil data dari model Bagian dengan relasi buku dan user
        $bagian = Bagian::with(['buku' => function ($query) {
            $query->select('id', 'cover', 'judul', 'deskripsi', 'user_id')
                ->with(['user' => function ($query) {
                    $query->select('id', 'nama_pengguna');
                }]);
        }])
            ->select('buku_id', DB::raw('COUNT(*) as total_bagian'))
            ->groupBy('buku_id')
            ->get()
            ->map(function ($item) {
                return [
                    'cover' => $item->buku->cover,
                    'judul' => $item->buku->judul,
                    'deskripsi' => $item->buku->deskripsi,
                    'nama_pengguna' => $item->buku->user->nama_pengguna,
                    'total_bagian' => $item->total_bagian
                ];
            });

        return response()->json([
            'bagian' => $bagian
        ], 200);
    }
=======
{
    // Ambil data dari model Bagian dengan relasi buku dan user
    $bagian = Bagian::with(['buku' => function($query) {
        $query->select('id', 'cover', 'judul', 'deskripsi', 'user_id')
              ->with(['user' => function($query) {
                  $query->select('id', 'nama_pengguna');
              }]);
    }])
    ->select('buku_id', DB::raw('COUNT(*) as total_bagian'))
    ->groupBy('buku_id')
    ->get()
    ->map(function($item) {
        return [
            'cover' => $item->buku->cover,
            'judul' => $item->buku->judul,
            'deskripsi' => $item->buku->deskripsi,
            'nama_pengguna' => $item->buku->user->nama_pengguna,
            'total_bagian' => $item->total_bagian
        ];
    });

    return response()->json([
        'bagian' => $bagian
    ], 200);
}
>>>>>>> 6de89285048dc809fa77af3558c89973c2e07806
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
            'cover' => 'required|file|max:2048',
            'judul' => 'required|max:255|string',
            'deskripsi' => 'required|max:255|string',
        ]);

        if ($request->file('cover')) {
            $validatedData['cover'] = $request->file('cover')->store('cover');
        }

        $validatedData['user_id'] = Auth::user()->id;

        return response([
            'message' => 'Buku berhasil ditambahkan',
            'buku' => Buku::create($validatedData),
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Buku $buku)
    {
        // Ambil semua bagian yang terkait dengan buku ini
        $bagian = $buku->bagian; // Asumsi bahwa relasi sudah didefinisikan di model Buku

        return response()->json([
            'buku' => $buku,
            'bagian' => $bagian
        ], 200);
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
                'message' => 'Buku tidak ditemukan'
            ], 403);
        }

        if ($buku->user_id != auth()->user()->id) {
            return response([
                'message' => 'Anda tidak berhak mengubah buku ini'
            ], 403);
        }

        $firstData = [
            'cover' => 'image|file|max:2048',
            'judul' => 'required|max:255|string',
            'deskripsi' => 'required|max:255|string',
        ];

        $validatedData = $request->validate($firstData);

        if ($request->file('cover')) {
            Storage::delete($buku->cover);
            $validatedData['cover'] = $request->file('cover')->store('covers');
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
                'message' => 'Buku tidak ditemukan'
            ], 404); // Mengubah status code menjadi 404 Not Found
        }

        // Cek apakah user yang sedang login adalah pemilik buku
        if ($book->user_id != auth()->user()->id) {
            return response()->json([
                'message' => 'Anda tidak berhak mengubah buku ini'
            ], 403);
        }

        // Cek apakah ada bagian yang terkait dengan buku
        if ($book->bagian()->count() > 0) {
            // Jika ada, hapus bagian terlebih dahulu
            $book->bagian()->delete();
        }

        // Hapus buku
        $book->delete();

        return response()->json([
            'message' => 'Buku berhasil dihapus',
        ], 200);
    }
}
