<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lpj extends Model
{
    use HasFactory;

    protected $table = 'lpj';

    protected $guarded = [];

    protected $casts = [
        'tanggal_kirim'   => 'datetime',
        'tanggal_review'  => 'datetime',
        'total_realisasi' => 'decimal:2',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'proposal_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function buktiKegiatan()
    {
        return $this->hasMany(BuktiKegiatan::class, 'lpj_id');
    }

    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'draft'            => 'Draft',
            'menunggu_review'  => 'Menunggu Review PJ LPJ',
            'disetujui'        => 'Disetujui',
            'perlu_perbaikan'  => 'Perlu Perbaikan',
            default            => ucfirst(str_replace('_', ' ', $this->status ?? 'Draft')),
        };
    }

    public function getBadgeStatusAttribute(): string
    {
        return match ($this->status) {
            'draft'            => 'secondary',
            'menunggu_review'  => 'warning',
            'disetujui'        => 'success',
            'perlu_perbaikan'  => 'danger',
            default            => 'info',
        };
    }
}
