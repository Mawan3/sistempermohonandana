<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ormawa extends Model
{
    use HasFactory;

    // Paksa Laravel menggunakan tabel 'ormawa' (tanpa huruf 's' di belakang)
    protected $table = 'ormawa';

    // Izinkan semua kolom diisi data (Mass Assignment)
    protected $guarded = [];
}