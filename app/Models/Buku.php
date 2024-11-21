<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Buku extends Model
{
    use HasFactory;

    protected $guarded = ['buku_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bagian()
    {
        return $this->hasMany(Bagian::class);
    }

    public function perpustakaan()
    {
        return $this->hasMany(Perpustakaan::class);
    }
}
