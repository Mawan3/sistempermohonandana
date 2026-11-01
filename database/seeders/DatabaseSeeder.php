<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ormawa;
use App\Models\Periode;
use App\Models\Proposal;
use App\Models\Lpj;
use App\Models\BuktiKegiatan;
use App\Models\Pencairan;
use App\Models\Notifikasi;
use App\Models\Riwayat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Master Ormawa UM Kudus
        $this->call([
            OrmawaSeeder::class,
        ]);

        $himaSisfo = Ormawa::where('singkatan', 'Hima Sisfo')->first();
        $himaIlkom = Ormawa::where('singkatan', 'Hima Ilkom')->first();
        $imm       = Ormawa::where('singkatan', 'IMM')->first();
        $hw        = Ormawa::where('singkatan', 'HW')->first();

        // 2. Data Periode Anggaran Aktif (Plafon Rp 7.000.000, Batas LPJ 7 Hari)
        $periode = Periode::updateOrCreate(
            ['nama' => 'Periode Anggaran 2026/2027'],
            [
                'tanggal_mulai'   => '2026-08-01',
                'tanggal_selesai' => '2027-07-31',
                'batas_anggaran'  => 7000000,
                'batas_hari_lpj'  => 7,
                'is_aktif'        => true,
            ]
        );

        // 3. Akun Pengguna (Sesuai Role Pengelola Sistem)
        // Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@umkudus.ac.id'],
            [
                'name'     => 'Administrator Kampus',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // Staf Kemahasiswaan
        $kemahasiswaan = User::updateOrCreate(
            ['email' => 'kemahasiswaan@umkudus.ac.id'],
            [
                'name'     => 'Staf Kemahasiswaan UM Kudus',
                'password' => Hash::make('password'),
                'role'     => 'staf_kemahasiswaan',
            ]
        );

        // Penanggung Jawab LPJ (PJ LPJ)
        $pjLpj = User::updateOrCreate(
            ['email' => 'pjlpj@umkudus.ac.id'],
            [
                'name'     => 'Penanggung Jawab LPJ Kemahasiswaan',
                'password' => Hash::make('password'),
                'role'     => 'pj_lpj',
            ]
        );

        // Staf Keuangan
        $keuangan = User::updateOrCreate(
            ['email' => 'keuangan@umkudus.ac.id'],
            [
                'name'     => 'Bagian Keuangan UM Kudus',
                'password' => Hash::make('password'),
                'role'     => 'staf_keuangan',
            ]
        );

        // Pengurus Hima Sisfo (Pengaju Utama)
        $userSisfo = User::updateOrCreate(
            ['email' => 'himasisfo@umkudus.ac.id'],
            [
                'name'       => 'Pengurus Hima Sisfo',
                'password'   => Hash::make('password'),
                'role'       => 'pengurus',
                'ormawa_id'  => $himaSisfo?->id,
            ]
        );

        // Pengurus Hima Ilkom
        $userIlkom = User::updateOrCreate(
            ['email' => 'himailkom@umkudus.ac.id'],
            [
                'name'       => 'Pengurus Hima Ilkom',
                'password'   => Hash::make('password'),
                'role'       => 'pengurus',
                'ormawa_id'  => $himaIlkom?->id,
            ]
        );

        // Pengurus IMM
        $userImm = User::updateOrCreate(
            ['email' => 'imm@umkudus.ac.id'],
            [
                'name'       => 'Pengurus IMM Kudus',
                'password'   => Hash::make('password'),
                'role'       => 'pengurus',
                'ormawa_id'  => $imm?->id,
            ]
        );

        // Pengurus HW
        $userHw = User::updateOrCreate(
            ['email' => 'hw@umkudus.ac.id'],
            [
                'name'       => 'Pengurus HW UM Kudus',
                'password'   => Hash::make('password'),
                'role'       => 'pengurus',
                'ormawa_id'  => $hw?->id,
            ]
        );

        // 4. Data Proposal Contoh (Mencakup Semua Status & Alur)
        if ($himaSisfo && $userSisfo) {
            // Proposal 1: Diajukan (Menunggu Review Kemahasiswaan)
            $p1 = Proposal::updateOrCreate(
                ['judul_kegiatan' => 'Seminar Nasional Smart City & IoT 2026'],
                [
                    'ormawa_id'         => $himaSisfo->id,
                    'user_id'           => $userSisfo->id,
                    'periode_id'        => $periode->id,
                    'deskripsi'         => 'Seminar nasional mengundang pakar IoT dan AI untuk mahasiswa se-Karesidenan Pati.',
                    'tanggal_kegiatan'  => '2026-11-20',
                    'lokasi'            => 'Auditorium Universitas Muhammadiyah Kudus',
                    'anggaran_diajukan' => 3500000,
                    'status'            => 'diajukan',
                    'file_proposal'     => 'proposals/sample_proposal_seminar.pdf',
                ]
            );

            // Proposal 2: Perlu Revisi (Ada Catatan & File Koreksi Kemahasiswaan)
            $p2 = Proposal::updateOrCreate(
                ['judul_kegiatan' => 'Lomba UI/UX Design Antar Mahasiswa'],
                [
                    'ormawa_id'         => $himaSisfo->id,
                    'user_id'           => $userSisfo->id,
                    'periode_id'        => $periode->id,
                    'deskripsi'         => 'Kompetisi desain antarmuka aplikasi mobile tingkat universitas.',
                    'tanggal_kegiatan'  => '2026-12-05',
                    'lokasi'            => 'Laboratorium Komputer FST Kampus 1',
                    'anggaran_diajukan' => 2200000,
                    'status'            => 'perlu_revisi',
                    'catatan_reviewer'  => 'Mohon rincian honor juri dan plakat juara diperjelas sesuai standar kampus.',
                    'file_proposal'     => 'proposals/sample_proposal_uiux.pdf',
                    'file_koreksi'      => 'koreksi/catatan_koreksi_uiux.pdf',
                ]
            );

            // Proposal 3: Disetujui Kemahasiswaan (Menunggu Validasi Keuangan)
            $p3 = Proposal::updateOrCreate(
                ['judul_kegiatan' => 'Pelatihan Web Development Framework Laravel'],
                [
                    'ormawa_id'         => $himaIlkom?->id ?? $himaSisfo->id,
                    'user_id'           => $userIlkom?->id ?? $userSisfo->id,
                    'periode_id'        => $periode->id,
                    'deskripsi'         => 'Pelatihan intensif 2 hari pembuatan web RESTful API dengan Laravel 12.',
                    'tanggal_kegiatan'  => '2026-11-28',
                    'lokasi'            => 'Lab Multimedia Kampus 2',
                    'anggaran_diajukan' => 2500000,
                    'status'            => 'disetujui_kemahasiswaan',
                    'file_proposal'     => 'proposals/sample_proposal_laravel.pdf',
                ]
            );

            // Proposal 4: Dana Cair (Telah Divalidasi Keuangan + Ada Bukti Transfer + Siap Buat LPJ)
            $p4 = Proposal::updateOrCreate(
                ['judul_kegiatan' => 'Bakti Sosial & Santunan Ramadhan'],
                [
                    'ormawa_id'         => $imm?->id ?? $himaSisfo->id,
                    'user_id'           => $userImm?->id ?? $userSisfo->id,
                    'periode_id'        => $periode->id,
                    'deskripsi'         => 'Pemberian paket sembako dan santunan anak yatim di wilayah sekitar Kudus.',
                    'tanggal_kegiatan'  => '2026-10-15',
                    'lokasi'            => 'Desa Prambatan Lor, Kudus',
                    'anggaran_diajukan' => 2800000,
                    'status'            => 'dana_cair',
                    'file_proposal'     => 'proposals/sample_proposal_baksos.pdf',
                ]
            );

            // Pencairan untuk Proposal 4
            Pencairan::updateOrCreate(
                ['proposal_id' => $p4->id],
                [
                    'diproses_oleh'     => $keuangan->id,
                    'jumlah_dicairkan'  => 2800000,
                    'status'            => 'selesai',
                    'tanggal_pencairan' => '2026-10-10',
                    'catatan'           => 'Dana berhasil ditransfer ke rekening BSI pengurus ormawa.',
                    'bukti_transfer'    => 'bukti_transfer/sample_transfer_baksos.png',
                ]
            );

            // Proposal 5: Sudah LPJ (LPJ Menunggu Review PJ LPJ)
            $p5 = Proposal::updateOrCreate(
                ['judul_kegiatan' => 'Kemah Bakti Hizbul Wathan 2026'],
                [
                    'ormawa_id'         => $hw?->id ?? $himaSisfo->id,
                    'user_id'           => $userHw?->id ?? $userSisfo->id,
                    'periode_id'        => $periode->id,
                    'deskripsi'         => 'Kegiatan perkemahan dan penanaman pohon di lereng Gunung Muria.',
                    'tanggal_kegiatan'  => '2026-10-01',
                    'lokasi'            => 'Bumi Perkemahan Kajar, Dawe, Kudus',
                    'anggaran_diajukan' => 3100000,
                    'status'            => 'dana_cair',
                    'file_proposal'     => 'proposals/sample_proposal_hw.pdf',
                ]
            );

            // Pencairan untuk Proposal 5
            Pencairan::updateOrCreate(
                ['proposal_id' => $p5->id],
                [
                    'diproses_oleh'     => $keuangan->id,
                    'jumlah_dicairkan'  => 3100000,
                    'status'            => 'selesai',
                    'tanggal_pencairan' => '2026-09-25',
                    'catatan'           => 'Pencairan tahap penuh via Bank Jateng Syariah.',
                    'bukti_transfer'    => 'bukti_transfer/sample_transfer_hw.png',
                ]
            );

            // Data LPJ untuk Proposal 5
            $lpj = Lpj::updateOrCreate(
                ['proposal_id' => $p5->id],
                [
                    'user_id'            => $userHw?->id ?? $userSisfo->id,
                    'rincian_penggunaan' => "1. Konsumsi peserta & panitia (3 hari): Rp 1.500.000\n2. Sewa tenda & perlengkapan kemah: Rp 900.000\n3. Bibit pohon penghijauan: Rp 450.000\n4. Transportasi & P3K: Rp 250.000\nTotal Pengeluaran: Rp 3.100.000",
                    'total_realisasi'    => 3100000,
                    'status'             => 'menunggu_review',
                    'tanggal_kirim'      => now()->subDays(2),
                    'file_lpj'           => 'bukti_lpj/sample_lpj_hw.pdf',
                ]
            );

            BuktiKegiatan::updateOrCreate(
                ['lpj_id' => $lpj->id, 'nama_file' => 'Foto Kegiatan Kemah Bakti'],
                [
                    'jenis'     => 'foto',
                    'path_file' => 'bukti_lpj/foto_kegiatan_kemah.jpg',
                ]
            );

            BuktiKegiatan::updateOrCreate(
                ['lpj_id' => $lpj->id, 'nama_file' => 'Kuitansi & Nota Pembelian'],
                [
                    'jenis'     => 'nota',
                    'path_file' => 'bukti_lpj/nota_pembelian.pdf',
                ]
            );

            // 5. Data Notifikasi Awal
            Notifikasi::updateOrCreate(
                ['judul' => 'Proposal Perlu Revisi'],
                [
                    'user_id'     => $userSisfo->id,
                    'ormawa_id'   => $himaSisfo->id,
                    'proposal_id' => $p2->id,
                    'pesan'       => 'Proposal "Lomba UI/UX Design Antar Mahasiswa" membutuhkan perbaikan. Silakan unduh file koreksi dan unggah proposal revisi.',
                    'tipe'        => 'warning',
                    'is_dibaca'   => false,
                ]
            );

            Notifikasi::updateOrCreate(
                ['judul' => 'Dana Proposal Telah Dicairkan'],
                [
                    'user_id'     => $userImm?->id ?? $userSisfo->id,
                    'ormawa_id'   => $imm?->id ?? $himaSisfo->id,
                    'proposal_id' => $p4->id,
                    'pesan'       => 'Dana kegiatan "Bakti Sosial & Santunan Ramadhan" sebesar Rp 2.800.000 telah dicairkan oleh Keuangan. Bukti transfer telah tersedia.',
                    'tipe'        => 'success',
                    'is_dibaca'   => false,
                ]
            );

            Notifikasi::updateOrCreate(
                ['judul' => 'Proposal Disetujui Kemahasiswaan'],
                [
                    'user_id'     => $userIlkom?->id ?? $userSisfo->id,
                    'ormawa_id'   => $himaIlkom?->id ?? $himaSisfo->id,
                    'proposal_id' => $p3->id,
                    'pesan'       => 'Proposal "Pelatihan Web Development Framework Laravel" telah disetujui Kemahasiswaan dan diteruskan ke Keuangan.',
                    'tipe'        => 'info',
                    'is_dibaca'   => true,
                ]
            );

            // 6. Data Riwayat Aktivitas Periode Aktif
            Riwayat::updateOrCreate(
                ['aktivitas' => 'Pengajuan Proposal Baru', 'proposal_id' => $p1->id],
                [
                    'ormawa_id'   => $himaSisfo->id,
                    'user_id'     => $userSisfo->id,
                    'periode_id'  => $periode->id,
                    'keterangan'  => 'Mengajukan proposal "Seminar Nasional Smart City & IoT 2026" dengan anggaran Rp 3.500.000.',
                ]
            );

            Riwayat::updateOrCreate(
                ['aktivitas' => 'Koreksi Proposal oleh Kemahasiswaan', 'proposal_id' => $p2->id],
                [
                    'ormawa_id'   => $himaSisfo->id,
                    'user_id'     => $kemahasiswaan->id,
                    'periode_id'  => $periode->id,
                    'keterangan'  => 'Kemahasiswaan mengirim catatan revisi dan file koreksi untuk proposal "Lomba UI/UX Design".',
                ]
            );

            Riwayat::updateOrCreate(
                ['aktivitas' => 'Pencairan Dana oleh Keuangan', 'proposal_id' => $p4->id],
                [
                    'ormawa_id'   => $imm?->id ?? $himaSisfo->id,
                    'user_id'     => $keuangan->id,
                    'periode_id'  => $periode->id,
                    'keterangan'  => 'Bagian Keuangan mencairkan dana sebesar Rp 2.800.000 dan mengunggah bukti transfer.',
                ]
            );

            Riwayat::updateOrCreate(
                ['aktivitas' => 'Pengajuan LPJ Kegiatan', 'proposal_id' => $p5->id],
                [
                    'ormawa_id'   => $hw?->id ?? $himaSisfo->id,
                    'user_id'     => $userHw?->id ?? $userSisfo->id,
                    'periode_id'  => $periode->id,
                    'keterangan'  => 'Ormawa HW menyerahkan LPJ realisasi dana sebesar Rp 3.100.000 berserta lampiran bukti foto dan nota.',
                ]
            );
        }
    }
}
