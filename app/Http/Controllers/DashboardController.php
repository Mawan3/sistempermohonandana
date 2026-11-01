<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Lpj;
use App\Models\Ormawa;
use App\Models\Periode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman Dashboard berdasarkan Autentikasi Pengguna
     * Dibedakan menjadi 2 POV Utama: Pengurus Kampus & Pengurus Ormawa
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role ?? 'pengurus';

        // Deteksi kolom periode aktif secara fleksibel dan aman dari error database
        $periodeAktif = null;
        if (Schema::hasColumn('periodes', 'is_aktif')) {
            $periodeAktif = Periode::where('is_aktif', true)->first();
        } elseif (Schema::hasColumn('periodes', 'status')) {
            $periodeAktif = Periode::where('status', 'aktif')->first();
        }

        // Jika tidak ditemukan status aktif, ambil data periode paling baru
        if (!$periodeAktif) {
            $periodeAktif = Periode::latest()->first();
        }

        // ============================================================
        // 1. TAMPILAN POV: PENGURUS ORMAWA (Organisasi Mahasiswa)
        // ============================================================
        if ($role === 'pengurus') {
            $ormawaId = $user->ormawa_id;
            $ormawa = $user->ormawa ?? Ormawa::find($ormawaId);

            // Isolasi data ketat: hanya proposal milik Ormawa akun ini
            $queryOrmawa = Proposal::where('ormawa_id', $ormawaId);

            $proposalDiajukan = (clone $queryOrmawa)->count();
            $perluRevisi      = (clone $queryOrmawa)->where('status', 'perlu_revisi')->count();
            $disetujui        = (clone $queryOrmawa)->whereIn('status', ['disetujui_kemahasiswaan', 'dana_cair', 'selesai'])->count();

            // LPJ yang perlu diajukan (dana_cair namun belum ada lpj)
            $lpjJatuhTempo = (clone $queryOrmawa)
                ->where('status', 'dana_cair')
                ->whereDoesntHave('lpj')
                ->count();

            // Daftar proposal ormawa ini
            $proposals = (clone $queryOrmawa)
                ->with(['lpj', 'periode'])
                ->latest()
                ->take(8)
                ->get();

            // Hitung akumulasi pagu anggaran periode aktif
            $danaTerpakai = Proposal::where('ormawa_id', $ormawaId)
                ->whereIn('status', ['dana_cair', 'selesai'])
                ->when($periodeAktif, fn($q) => $q->where('periode_id', $periodeAktif->id))
                ->sum('anggaran_diajukan');

            $plafon = $periodeAktif ? ($periodeAktif->batas_anggaran ?? 7000000) : 7000000;
            $sisaPlafon = max(0, $plafon - $danaTerpakai);
            $persenPlafon = $plafon > 0 ? min(100, round(($danaTerpakai / $plafon) * 100)) : 0;

            return view('dashboard', compact(
                'user',
                'role',
                'ormawa',
                'proposalDiajukan',
                'perluRevisi',
                'disetujui',
                'lpjJatuhTempo',
                'proposals',
                'danaTerpakai',
                'sisaPlafon',
                'persenPlafon',
                'plafon',
                'periodeAktif'
            ));
        }

        // ============================================================
        // 2. TAMPILAN POV: PENGURUS KAMPUS (Kemahasiswaan, PJ LPJ, Keuangan)
        // ============================================================
        // 2A. STAF KEMAHASISWAAN (Verifikasi Proposal)
        if ($role === 'staf_kemahasiswaan') {
            $proposalMasuk = Proposal::whereIn('status', ['diajukan', 'perlu_revisi'])->count();
            $menungguVerifikasi = Proposal::where('status', 'diajukan')->count();
            $selesaiBulanIni = Proposal::where('status', 'disetujui_kemahasiswaan')
                ->whereMonth('updated_at', now()->month)
                ->count();

            $antreanProposal = Proposal::with(['ormawa', 'user'])
                ->where('status', 'diajukan')
                ->latest()
                ->take(6)
                ->get();

            return view('dashboard', compact('user', 'role', 'proposalMasuk', 'menungguVerifikasi', 'selesaiBulanIni', 'antreanProposal', 'periodeAktif'));
        }

        // 2B. PENANGGUNG JAWAB LPJ (PJ LPJ)
        if ($role === 'pj_lpj') {
            $lpjMasuk = Lpj::count();
            $lpjMenunggu = Lpj::where('status', 'menunggu_review')->count();
            $lpjDisetujui = Lpj::where('status', 'disetujui')
                ->whereMonth('updated_at', now()->month)
                ->count();

            $antreanLpj = Lpj::with(['proposal.ormawa', 'user', 'buktiKegiatan'])
                ->where('status', 'menunggu_review')
                ->latest()
                ->take(6)
                ->get();

            return view('dashboard', compact('user', 'role', 'lpjMasuk', 'lpjMenunggu', 'lpjDisetujui', 'antreanLpj', 'periodeAktif'));
        }

        // 2C. BAGIAN KEUANGAN (Validasi Anggaran & Plafon)
        if ($role === 'staf_keuangan') {
            $menungguValidasi = Proposal::where('status', 'disetujui_kemahasiswaan')->count();
            $danaCairBulanIni = Proposal::where('status', 'dana_cair')
                ->whereMonth('updated_at', now()->month)
                ->sum('anggaran_diajukan');

            $totalOrmawa = Ormawa::where('aktif', true)->count();
            $proposalBelumValidasi = Proposal::with(['ormawa', 'periode'])
                ->where('status', 'disetujui_kemahasiswaan')
                ->latest()
                ->take(6)
                ->get();

            // Saldo dan penyerapan pagu seluruh ormawa
            $saldoOrmawa = Ormawa::where('aktif', true)->get()->map(function ($ormawa) use ($periodeAktif) {
                $terpakai = Proposal::where('ormawa_id', $ormawa->id)
                    ->whereIn('status', ['dana_cair', 'selesai'])
                    ->when($periodeAktif, fn($q) => $q->where('periode_id', $periodeAktif->id))
                    ->sum('anggaran_diajukan');

                $plafon = $periodeAktif ? ($periodeAktif->batas_anggaran ?? 7000000) : 7000000;
                $sisa = max(0, $plafon - $terpakai);
                $persentase = $plafon > 0 ? min(100, round(($terpakai / $plafon) * 100)) : 0;

                return [
                    'id'         => $ormawa->id,
                    'nama'       => $ormawa->nama_ormawa,
                    'singkatan'  => $ormawa->singkatan,
                    'plafon'     => $plafon,
                    'terpakai'   => $terpakai,
                    'sisa'       => $sisa,
                    'persentase' => $persentase,
                ];
            });

            return view('dashboard', compact(
                'user',
                'role',
                'menungguValidasi',
                'danaCairBulanIni',
                'totalOrmawa',
                'proposalBelumValidasi',
                'saldoOrmawa',
                'periodeAktif'
            ));
        }

        // ============================================================
        // 3. TAMPILAN POV: ADMINISTRATOR
        //    Hanya tampilkan data statistik akun pengguna — TIDAK ada
        //    data proposal, ormawa operasional, atau modul lainnya.
        // ============================================================
        $totalUsers          = User::count();
        $totalAdmin          = User::where('role', 'admin')->count();
        $totalKampus         = User::whereIn('role', ['staf_kemahasiswaan', 'pj_lpj', 'staf_keuangan'])->count();
        $totalOrmawaUser     = User::where('role', 'pengurus')->count();
        $penggunaTerbaru     = User::with('ormawa')->latest()->take(8)->get();

        return view('dashboard', compact(
            'user',
            'role',
            'totalUsers',
            'totalAdmin',
            'totalKampus',
            'totalOrmawaUser',
            'penggunaTerbaru',
            'periodeAktif'
        ));
    }
}