<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perpustakaan extends Model
{
    use HasFactory;

<<<<<<< HEAD
    protected $table = 'perpustakaans';
    protected $fillable = ['id', 'buku_id', 'user_id'];

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id');
    }

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
=======
    public function buku()
    {
        return $this->belongsTo(Buku::class);
>>>>>>> 6de89285048dc809fa77af3558c89973c2e07806
    }
}
