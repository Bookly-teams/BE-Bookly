<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bagian extends Model
{
    use HasFactory;

    protected $guarded = ['bagian_id'];

    public function buku()
    {
<<<<<<< HEAD
        return $this->belongsTo(Buku::class, 'buku_id', 'id');
=======
        return $this->belongsTo(Buku::class);
>>>>>>> 6de89285048dc809fa77af3558c89973c2e07806
    }
}
