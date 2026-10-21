<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $fillable = [
        'ormawa_id',
        'user_id',
        'judul_kegiatan',
        'deskripsi',
        'tanggal_kegiatan',
        'lokasi',
        'anggaran_diajukan',
        'status',
        'catatan_reviewer',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'anggaran_diajukan' => 'decimal:2',
    ];

    // Relasi: proposal milik satu ormawa
    public function ormawa()
    {
        return $this->belongsTo(Ormawa::class, 'ormawa_id');
    }

    // Relasi: proposal dibuat oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Label status dalam Bahasa Indonesia
    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'Draft',
            'diajukan'  => 'Diajukan',
            'direvisi'  => 'Perlu Revisi',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => ucfirst($this->status),
        };
    }

    // Badge warna untuk status
    public function getBadgeStatusAttribute(): string
    {
        return match ($this->status) {
            'draft'     => 'secondary',
            'diajukan'  => 'primary',
            'direvisi'  => 'warning',
            'disetujui' => 'success',
            'ditolak'   => 'danger',
            default     => 'secondary',
        };
    }
}
