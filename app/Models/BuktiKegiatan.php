<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiKegiatan extends Model
{
    use HasFactory;

    protected $table = 'bukti_kegiatan';

    protected $guarded = [];

    public function lpj()
    {
        return $this->belongsTo(Lpj::class, 'lpj_id');
    }
}
