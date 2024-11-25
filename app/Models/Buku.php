<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Buku extends Model
{
    use HasFactory;

<<<<<<< HEAD
    protected $guarded = ['buku_id'];
=======
    protected $guarded = ['book_id'];
>>>>>>> 6de89285048dc809fa77af3558c89973c2e07806

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bagian()
    {
        return $this->hasMany(Bagian::class);
    }
<<<<<<< HEAD

    public function perpustakaan()
    {
        return $this->hasMany(Perpustakaan::class);
    }
=======
>>>>>>> 6de89285048dc809fa77af3558c89973c2e07806
}
