<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $table = 'proposals';

    protected $fillable = [
        'ormawa_id',
        'periode_id',
        'user_id',
        'judul_kegiatan',
        'deskripsi',
        'tanggal_kegiatan',
        'tanggal_mulai_kegiatan',
        'tanggal_selesai_kegiatan',
        'lokasi',
        'anggaran_diajukan',
        'total_anggaran',
        'status',
        'catatan_reviewer',
        'file_proposal',
        'file_koreksi',
    ];

    protected $casts = [
        'tanggal_kegiatan'         => 'date',
        'tanggal_mulai_kegiatan'   => 'date',
        'tanggal_selesai_kegiatan' => 'date',
        'anggaran_diajukan'        => 'decimal:2',
        'total_anggaran'           => 'decimal:2',
    ];

    // Relasi ke Ormawa
    public function ormawa()
    {
        return $this->belongsTo(Ormawa::class, 'ormawa_id');
    }

    // Relasi ke Pengguna Pengaju
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Periode
    public function periode()
    {
        return $this->belongsTo(Periode::class, 'periode_id');
    }

    // Relasi ke LPJ
    public function lpj()
    {
        return $this->hasOne(Lpj::class, 'proposal_id');
    }

    // Relasi ke Pencairan Dana
    public function pencairan()
    {
        return $this->hasOne(Pencairan::class, 'proposal_id');
    }

    // Relasi ke Riwayat Aktivitas
    public function riwayat()
    {
        return $this->hasMany(Riwayat::class, 'proposal_id')->latest();
    }

    // Relasi ke Notifikasi
    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'proposal_id');
    }

    // Accessor: fleksibel total_anggaran / anggaran_diajukan
    public function getTotalAnggaranAttribute()
    {
        return $this->attributes['total_anggaran'] ?? $this->attributes['anggaran_diajukan'] ?? 0;
    }

    // Accessor: tanggal mulai kegiatan
    public function getTanggalMulaiKegiatanAttribute()
    {
        return $this->attributes['tanggal_mulai_kegiatan'] ?? $this->tanggal_kegiatan ?? null;
    }

    // Label status bahasa Indonesia
    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'draft'                     => 'Draft',
            'diajukan'                  => 'Menunggu Review',
            'perlu_revisi', 'direvisi'  => 'Perlu Revisi',
            'disetujui_kemahasiswaan'   => 'Disetujui Kemahasiswaan',
            'dana_cair'                 => 'Dana Cair',
            'ditolak'                   => 'Ditolak',
            'selesai'                   => 'Selesai',
            default                     => ucfirst(str_replace('_', ' ', $this->status ?? 'Draft')),
        };
    }

    // Warna badge status
    public function getBadgeStatusAttribute(): string
    {
        return match ($this->status) {
            'draft'                     => 'secondary',
            'diajukan'                  => 'warning',
            'perlu_revisi', 'direvisi'  => 'danger',
            'disetujui_kemahasiswaan'   => 'primary',
            'dana_cair'                 => 'success',
            'ditolak'                   => 'secondary',
            'selesai'                   => 'dark',
            default                     => 'info',
        };
    }
}
