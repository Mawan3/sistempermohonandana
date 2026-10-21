<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ormawa extends Model
{
    use HasFactory;

    protected $table = 'ormawa';

    protected $fillable = [
        'nama_ormawa',
        'singkatan',
        'ketua',
        'pembina',
        'jenis',
        'aktif',
    ];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    // Relasi: satu ormawa punya banyak proposal
    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'ormawa_id');
    }
}
