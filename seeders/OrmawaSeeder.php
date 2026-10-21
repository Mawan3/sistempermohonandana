<?php

namespace Database\Seeders;

use App\Models\Ormawa;
use Illuminate\Database\Seeder;

class OrmawaSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_ormawa' => 'Badan Eksekutif Mahasiswa',
                'singkatan'   => 'BEM',
                'ketua'       => 'Ahmad Fauzi',
                'pembina'     => 'Dr. Siti Maryam, M.Pd.',
                'jenis'       => 'ormawa',
                'aktif'       => true,
            ],
            [
                'nama_ormawa' => 'Dewan Perwakilan Mahasiswa',
                'singkatan'   => 'DPM',
                'ketua'       => 'Rizky Pratama',
                'pembina'     => 'Budi Santoso, S.Kom., M.T.',
                'jenis'       => 'ormawa',
                'aktif'       => true,
            ],
            [
                'nama_ormawa' => 'Himpunan Mahasiswa Sistem Informasi',
                'singkatan'   => 'HMSI',
                'ketua'       => 'Dewi Rahmawati',
                'pembina'     => 'Agus Nugroho, S.T., M.Kom.',
                'jenis'       => 'ormawa',
                'aktif'       => true,
            ],
            [
                'nama_ormawa' => 'Unit Kegiatan Mahasiswa Seni Budaya',
                'singkatan'   => 'UKM Senbud',
                'ketua'       => 'Fajar Hidayat',
                'pembina'     => 'Endang Lestari, M.Sn.',
                'jenis'       => 'ortom',
                'aktif'       => true,
            ],
        ];

        foreach ($data as $item) {
            Ormawa::firstOrCreate(
                ['nama_ormawa' => $item['nama_ormawa']],
                $item
            );
        }
    }
}
