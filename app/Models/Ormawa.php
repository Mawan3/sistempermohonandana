<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ormawa extends Model
{
    use HasFactory;

    protected $table = 'ormawa';

    protected $guarded = [];

    // Accessor: nama agar kompatibel dengan $ormawa->nama dan $ormawa->nama_ormawa
    public function getNamaAttribute()
    {
        return $this->attributes['nama_ormawa'] ?? $this->attributes['nama'] ?? '';
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'ormawa_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'ormawa_id');
    }
}
