{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@php
    $role = auth()->user()->role ?? 'pengurus';
    $user = auth()->user();
@endphp

@section('title', 'Dashboard - SI Proma UM Kudus')
@section('page-title', 'Dashboard Sistem')

@section('content')

{{-- Alert Periode Anggaran Aktif --}}
@if(isset($periodeAktif) && $periodeAktif)
<div class="alert alert-light border border-primary border-opacity-25 shadow-sm d-flex justify-content-between align-items-center mb-4 py-2 px-3 rounded-3">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-calendar2-check-fill text-primary fs-5"></i>
        <div>
            <span class="fw-semibold text-dark">{{ $periodeAktif->nama }}</span>
            <span class="text-muted small ms-2">({{ \Illuminate\Support\Carbon::parse($periodeAktif->tanggal_mulai)->format('d M Y') }} &ndash; {{ \Illuminate\Support\Carbon::parse($periodeAktif->tanggal_selesai)->format('d M Y') }})</span>
        </div>
    </div>
    <span class="badge bg-primary rounded-pill px-3 py-1">Plafon: Rp {{ number_format($periodeAktif->batas_anggaran, 0, ',', '.') }} / Ormawa</span>
</div>
@endif

{{-- ============================================================
     POV 1: PENGURUS KAMPUS - STAF KEMAHASISWAAN (Verifikasi)
     ============================================================ --}}
@if($role === 'staf_kemahasiswaan')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
            <div class="icon"><i class="bi bi-inbox-fill"></i></div>
            <div>
                <div class="label">Total Proposal Masuk</div>
                <div class="value">{{ $proposalMasuk ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);">
            <div class="icon"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="label">Menunggu Verifikasi</div>
                <div class="value">{{ $menungguVerifikasi ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="icon"><i class="bi bi-check2-circle"></i></div>
            <div>
                <div class="label">Disetujui Bulan Ini</div>
                <div class="value">{{ $selesaiBulanIni ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold text-primary"><i class="bi bi-list-task me-2"></i>Antrean Proposal Menunggu Verifikasi</h6>
        <a href="{{ route('kemahasiswaan.verifikasi.index') }}" class="btn btn-sm btn-outline-primary">Buka Antrean Verifikasi</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul Kegiatan</th>
                        <th>Ormawa</th>
                        <th>Anggaran Diajukan</th>
                        <th>Dokumen</th>
                        <th>Tanggal Masuk</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($antreanProposal ?? [] as $p)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $p->judul_kegiatan }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $p->lokasi ?? 'UM Kudus' }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $p->ormawa->singkatan ?? '-' }}</span></td>
                        <td class="fw-bold text-dark">Rp {{ number_format($p->anggaran_diajukan, 0, ',', '.') }}</td>
                        <td>
                            @if($p->file_proposal)
                                <a href="{{ route('kemahasiswaan.verifikasi.preview', $p) }}" target="_blank" class="badge bg-info text-dark text-decoration-none">
                                    <i class="bi bi-file-earmark-pdf"></i> Preview PDF
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">Tidak Ada Berkas</span>
                            @endif
                        </td>
                        <td>{{ \Illuminate\Support\Carbon::parse($p->created_at)->format('d M Y') }}</td>
                        <td class="text-center">
                            <a href="{{ route('kemahasiswaan.verifikasi.show', $p) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-search me-1"></i> Verifikasi
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada antrean proposal yang perlu diverifikasi saat ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ============================================================
     POV 1: PENGURUS KAMPUS - PENANGGUNG JAWAB LPJ (PJ LPJ)
     ============================================================ --}}
@elseif($role === 'pj_lpj')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #654ea3 0%, #eaafc8 100%);">
            <div class="icon"><i class="bi bi-journal-text"></i></div>
            <div>
                <div class="label">Total LPJ Masuk</div>
                <div class="value">{{ $lpjMasuk ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);">
            <div class="icon"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="label">Menunggu Review</div>
                <div class="value">{{ $lpjMenunggu ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="icon"><i class="bi bi-check2-all"></i></div>
            <div>
                <div class="label">LPJ Disetujui (Bulan Ini)</div>
                <div class="value">{{ $lpjDisetujui ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold text-primary"><i class="bi bi-clipboard2-check me-2"></i>Antrean LPJ Menunggu Review</h6>
        <a href="{{ route('kemahasiswaan.review-lpj.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua LPJ</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kegiatan</th>
                        <th>Ormawa</th>
                        <th>Total Realisasi</th>
                        <th>Dokumen & Lampiran</th>
                        <th>Tanggal Kirim</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($antreanLpj ?? [] as $lpj)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $lpj->proposal->judul_kegiatan ?? '-' }}</div>
                            <small class="text-muted">Diajukan oleh: {{ $lpj->user->name ?? 'Pengurus' }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $lpj->proposal->ormawa->singkatan ?? '-' }}</span></td>
                        <td class="fw-bold text-dark">Rp {{ number_format($lpj->total_realisasi, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-light text-dark border me-1">
                                <i class="bi bi-camera"></i> {{ $lpj->buktiKegiatan->where('jenis', 'foto')->count() }} Foto
                            </span>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-receipt"></i> {{ $lpj->buktiKegiatan->where('jenis', 'nota')->count() }} Nota
                            </span>
                        </td>
                        <td>{{ \Illuminate\Support\Carbon::parse($lpj->tanggal_kirim)->format('d M Y') }}</td>
                        <td class="text-center">
                            <a href="{{ route('kemahasiswaan.review-lpj.show', $lpj) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-search me-1"></i> Review LPJ
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada LPJ yang sedang menunggu review.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ============================================================
     POV 1: PENGURUS KAMPUS - BAGIAN KEUANGAN (Validasi & Pencairan)
     ============================================================ --}}
@elseif($role === 'staf_keuangan')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);">
            <div class="icon"><i class="bi bi-hourglass"></i></div>
            <div>
                <div class="label">Menunggu Validasi</div>
                <div class="value">{{ $menungguValidasi ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="icon"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="label">Dana Cair Bulan Ini</div>
                <div class="value" style="font-size:1.25rem;">Rp {{ number_format($danaCairBulanIni ?? 0, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
            <div class="icon"><i class="bi bi-building"></i></div>
            <div>
                <div class="label">Ormawa Aktif</div>
                <div class="value">{{ $totalOrmawa ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);">
            <div class="icon"><i class="bi bi-shield-check"></i></div>
            <div>
                <div class="label">Plafon Per Ormawa</div>
                <div class="value" style="font-size:1.25rem;">Rp 7.000.000</div>
            </div>
        </div>
    </div>
</div>

{{-- Ringkasan Saldo Tiap Ormawa & Proposal Perlu Validasi --}}
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-success"><i class="bi bi-wallet2 me-2"></i>Monitoring Saldo Anggaran Tiap Ormawa</h6>
                <span class="badge bg-light text-muted border">Plafon: Rp 7.000.000</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ormawa</th>
                                <th class="text-end">Dana Terpakai</th>
                                <th class="text-end">Sisa Plafon</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($saldoOrmawa ?? [] as $s)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $s['singkatan'] }}</div>
                                    <small class="text-muted">{{ $s['nama'] }}</small>
                                    <div class="progress mt-1" style="height: 6px;">
                                        <div class="progress-bar {{ $s['persentase'] >= 90 ? 'bg-danger' : ($s['persentase'] >= 60 ? 'bg-warning' : 'bg-success') }}"
                                             role="progressbar" style="width: {{ $s['persentase'] }}%;"></div>
                                    </div>
                                </td>
                                <td class="text-end text-danger fw-semibold">
                                    Rp {{ number_format($s['terpakai'], 0, ',', '.') }}
                                    <small class="d-block text-muted">({{ $s['persentase'] }}%)</small>
                                </td>
                                <td class="text-end text-success fw-bold">
                                    Rp {{ number_format($s['sisa'], 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data Ormawa</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-primary"><i class="bi bi-cash-coin me-2"></i>Proposal Siap Validasi Pencairan</h6>
                <a href="{{ route('keuangan.validasi.index') }}" class="btn btn-sm btn-outline-primary">Buka Validasi</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Judul Kegiatan</th>
                                <th>Ormawa</th>
                                <th>Nominal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proposalBelumValidasi ?? [] as $p)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ Str::limit($p->judul_kegiatan, 30) }}</div>
                                    <small class="text-muted">{{ \Illuminate\Support\Carbon::parse($p->created_at)->format('d M Y') }}</small>
                                </td>
                                <td><span class="badge bg-secondary">{{ $p->ormawa->singkatan ?? '-' }}</span></td>
                                <td class="fw-bold text-dark">Rp {{ number_format($p->anggaran_diajukan, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('keuangan.validasi.show', $p) }}" class="btn btn-sm btn-success">
                                        <i class="bi bi-cash me-1"></i> Cairkan
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Semua proposal yang disetujui telah divalidasi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     POV 2: PENGURUS ORMAWA (Organisasi Mahasiswa)
     ============================================================ --}}
@elseif($role === 'pengurus')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
            <div class="icon"><i class="bi bi-send-fill"></i></div>
            <div>
                <div class="label">Proposal Diajukan</div>
                <div class="value">{{ $proposalDiajukan ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);">
            <div class="icon"><i class="bi bi-pencil-square"></i></div>
            <div>
                <div class="label">Perlu Revisi</div>
                <div class="value">{{ $perluRevisi ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="icon"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="label">Disetujui / Cair</div>
                <div class="value">{{ $disetujui ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);">
            <div class="icon"><i class="bi bi-exclamation-octagon-fill"></i></div>
            <div>
                <div class="label">LPJ Jatuh Tempo</div>
                <div class="value">{{ $lpjJatuhTempo ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Info Pagu Anggaran Ormawa --}}
<div class="card shadow-sm border-0 mb-4 bg-light">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <span class="text-muted small">Ormawa Anda:</span>
                <h5 class="fw-bold mb-0 text-primary">{{ $ormawa->nama_ormawa ?? ($user->ormawa->nama_ormawa ?? 'Organisasi Mahasiswa') }}</h5>
            </div>
            <div class="col-md-5">
                <span class="text-muted small">Penggunaan Plafon Anggaran (Rp {{ number_format($plafon ?? 7000000, 0, ',', '.') }}):</span>
                <div class="progress mt-1" style="height: 10px;">
                    <div class="progress-bar {{ ($persenPlafon ?? 0) >= 80 ? 'bg-danger' : 'bg-success' }}" style="width: {{ $persenPlafon ?? 0 }}%;"></div>
                </div>
            </div>
            <div class="col-md-3 text-md-end mt-2 mt-md-0">
                <span class="text-muted small d-block">Sisa Plafon Periode Ini:</span>
                <span class="fs-5 fw-bold text-success">Rp {{ number_format($sisaPlafon ?? 7000000, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold text-primary"><i class="bi bi-file-earmark-text me-2"></i>Daftar Proposal Kegiatan Ormawa Saya</h6>
        <a href="{{ route('proposals.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Ajukan Proposal Baru
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul Kegiatan</th>
                        <th>Nominal Diajukan</th>
                        <th>Status Alur</th>
                        <th>Status LPJ</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proposals ?? [] as $p)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $p->judul_kegiatan }}</div>
                            <small class="text-muted">{{ $p->tanggal_kegiatan ? \Illuminate\Support\Carbon::parse($p->tanggal_kegiatan)->format('d M Y') : '-' }}</small>
                        </td>
                        <td class="fw-semibold">Rp {{ number_format($p->anggaran_diajukan, 0, ',', '.') }}</td>
                        <td><span class="badge bg-{{ $p->badge_status }}">{{ $p->label_status }}</span></td>
                        <td>
                            @if($p->lpj)
                                <span class="badge bg-{{ $p->lpj->badge_status }}">{{ $p->lpj->label_status }}</span>
                            @elseif($p->status === 'dana_cair')
                                <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-circle me-1"></i>Wajib Upload LPJ</span>
                            @else
                                <span class="badge bg-light text-muted border">Belum Waktunya</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('proposals.show', $p) }}" class="btn btn-outline-primary" title="Lihat Detail Proposal & Upload LPJ">
                                    <i class="bi bi-eye"></i> Detail & Upload LPJ
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            Belum ada proposal yang diajukan oleh Ormawa Anda.
                            <div class="mt-2">
                                <a href="{{ route('proposals.create') }}" class="btn btn-sm btn-outline-primary">Mulai Ajukan Proposal</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ============================================================
     POV: ADMINISTRATOR
     Hanya menampilkan data manajemen akun pengguna sistem.
     Administrator TIDAK memiliki akses ke data proposal, LPJ,
     Ormawa operasional, atau modul keuangan apapun.
     ============================================================ --}}
@elseif($role === 'admin')

{{-- Banner Peran Administrator --}}
<div class="alert alert-primary border-0 shadow-sm d-flex align-items-center gap-3 mb-4 py-3 px-4 rounded-3">
    <i class="bi bi-shield-lock-fill fs-3"></i>
    <div>
        <div class="fw-bold">Panel Administrator — Manajemen Akun Pengguna</div>
        <div class="small text-white-50">Anda hanya dapat membuat dan mengelola akun pengguna sistem. Akses ke modul proposal, LPJ, dan keuangan dikelola oleh masing-masing petugas.</div>
    </div>
</div>

{{-- Statistik Akun Pengguna --}}
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
            <div class="icon"><i class="bi bi-people-fill"></i></div>
            <div>
                <div class="label">Total Pengguna</div>
                <div class="value">{{ $totalUsers ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #654ea3 0%, #eaafc8 100%);">
            <div class="icon"><i class="bi bi-shield-lock"></i></div>
            <div>
                <div class="label">Administrator</div>
                <div class="value">{{ $totalAdmin ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="icon"><i class="bi bi-building-check"></i></div>
            <div>
                <div class="label">Pengurus Kampus</div>
                <div class="value">{{ $totalKampus ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);">
            <div class="icon"><i class="bi bi-mortarboard-fill"></i></div>
            <div>
                <div class="label">Pengurus Ormawa</div>
                <div class="value">{{ $totalOrmawaUser ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Pengguna Terbaru --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold text-primary">
            <i class="bi bi-person-lines-fill me-2"></i>Daftar Akun Pengguna Terbaru
        </h6>
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-people me-1"></i>Kelola Semua Akun
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama Pengguna</th>
                        <th>Email</th>
                        <th>Role / Peran</th>
                        <th>Ormawa</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($penggunaTerbaru ?? [] as $u)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $u->name }}</div>
                        </td>
                        <td class="text-muted small">{{ $u->email }}</td>
                        <td>
                            <span class="badge
                                @if($u->role === 'admin') bg-dark
                                @elseif($u->role === 'pengurus') bg-warning text-dark
                                @elseif($u->role === 'staf_kemahasiswaan') bg-primary
                                @elseif($u->role === 'pj_lpj') bg-info text-dark
                                @elseif($u->role === 'staf_keuangan') bg-success
                                @else bg-secondary @endif">
                                {{ $u->label_role ?? ucfirst($u->role) }}
                            </span>
                        </td>
                        <td>{{ $u->ormawa->singkatan ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-secondary" title="Ubah Role">
                                <i class="bi bi-person-gear"></i> Ubah Role
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pengguna terdaftar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection
