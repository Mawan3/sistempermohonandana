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
                'nama_ormawa' => 'Himpunan Mahasiswa Sistem Informasi',
                'singkatan'   => 'Hima Sisfo',
                'ketua'       => 'M. Ridwan Kurniawan',
                'pembina'     => 'Tri Listyorini, M.Kom.',
                'jenis'       => 'ormawa',
                'aktif'       => true,
            ],
            [
                'nama_ormawa' => 'Himpunan Mahasiswa Ilmu Komputer',
                'singkatan'   => 'Hima Ilkom',
                'ketua'       => 'Ahmad Danial Fikri',
                'pembina'     => 'Supriyono, M.Cs.',
                'jenis'       => 'ormawa',
                'aktif'       => true,
            ],
            [
                'nama_ormawa' => 'Ikatan Mahasiswa Muhammadiyah',
                'singkatan'   => 'IMM',
                'ketua'       => 'Fatkhul Anam',
                'pembina'     => 'Dr. Ns. Sugiarto, M.Kep.',
                'jenis'       => 'ortom',
                'aktif'       => true,
            ],
            [
                'nama_ormawa' => 'Kafilah Penuntun Hizbul Wathan',
                'singkatan'   => 'HW',
                'ketua'       => 'Dimas Wahyu Pratama',
                'pembina'     => 'M. Noor Hidayat, M.Pd.',
                'jenis'       => 'ortom',
                'aktif'       => true,
            ],
        ];

        foreach ($data as $item) {
            Ormawa::updateOrCreate(
                ['singkatan' => $item['singkatan']],
                $item
            );
        }
    }
}
