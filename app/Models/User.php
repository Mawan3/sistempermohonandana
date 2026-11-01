<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'ormawa_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function ormawa()
    {
        return $this->belongsTo(Ormawa::class, 'ormawa_id');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'user_id');
    }

    public function getLabelRoleAttribute(): string
    {
        return match ($this->role) {
            'admin'               => 'Administrator',
            'staf_kemahasiswaan'  => 'Staf Kemahasiswaan',
            'pj_lpj'              => 'PJ LPJ Kemahasiswaan',
            'staf_keuangan'       => 'Staf Keuangan',
            'pengurus'            => 'Pengurus Ormawa',
            default               => ucfirst(str_replace('_', ' ', $this->role ?? 'Pengurus')),
        };
    }

    public function getBadgeRoleAttribute(): string
    {
        return match ($this->role) {
            'admin'               => 'danger',
            'staf_kemahasiswaan'  => 'primary',
            'pj_lpj'              => 'purple',
            'staf_keuangan'       => 'success',
            'pengurus'            => 'warning',
            default               => 'secondary',
        };
    }
}
