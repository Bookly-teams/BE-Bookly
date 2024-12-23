<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BagianController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PerpustakaanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/buku/{id}/add-to-library', [BukuController::class, 'addToLibrary']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user/profile', [UserController::class, 'showProfile']);

    Route::post('user/{id}/update-profile', [AuthController::class, 'updateProfile']);

    Route::get('/buku', [BukuController::class, 'index']);
    Route::post('/buku', [BukuController::class, 'store']);
    Route::get('/buku/{id}', [BukuController::class, 'show']);
    Route::post('/buku/{buku}', [BukuController::class, 'update']);
    Route::get('/buku/{buku}', [BukuController::class, 'show']);
    Route::put('/buku/{buku}', [BukuController::class, 'update']);
    Route::delete('/buku/{buku}', [BukuController::class, 'destroy']);
    Route::get('/buku/{buku_id}/bagian/{id_bagian}/baca', [BukuController::class, 'baca']);
    Route::get('/search/lbu', [BukuController::class, 'lihatBukuUser']);

    Route::post('/search', [BukuController::class, 'search']);

    Route::get('/buku/{buku_id}/bagian', [BagianController::class, 'index']);
    Route::post('/buku/{buku_id}/bagian', [BagianController::class, 'store']);
    Route::get('/bagian/{id}', [BagianController::class, 'show']);
    Route::post('/buku/{buku_id}/bagian/{bagian_id}', [BagianController::class, 'update']);
    Route::delete('/buku/{buku_id}/bagian/{bagian}', [BagianController::class, 'destroy']);

    Route::post('user/{id}/update-profile', [AuthController::class, 'updateProfile']);

    Route::get('/perpustakaan', [PerpustakaanController::class, 'index']);
    Route::post('/perpustakaan/tambah', [PerpustakaanController::class, 'store']);
    Route::delete('/perpustakaan/{id}', [PerpustakaanController::class, 'destroy']);
});
