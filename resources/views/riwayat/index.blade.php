{{-- resources/views/riwayat/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Riwayat Aktivitas 1 Periode - SI Proma UM Kudus')
@section('page-title', 'Riwayat Pengajuan 1 Periode Aktif')

@section('content')

{{-- Info 1 Periode Aktif Penuh & Ringkasan --}}
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card shadow-sm border-0 h-100 bg-primary text-white p-3 rounded-3">
        <small class="text-white-50 text-uppercase fw-semibold" style="letter-spacing:0.05em;">Periode Anggaran Aktif</small>
        <h5 class="fw-bold mt-1 mb-1">{{ $periodeAktif?->nama ?? 'Periode 2026/2027' }}</h5>
        <small class="text-white-50">
            @if($periodeAktif && $periodeAktif->tanggal_mulai && $periodeAktif->tanggal_selesai)
                {{ \Carbon\Carbon::parse($periodeAktif->tanggal_mulai)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($periodeAktif->tanggal_selesai)->format('d M Y') }}
            @else
                Awal Periode &ndash; Akhir Periode
            @endif
        </small>
        <div class="mt-3 pt-2 border-top border-white border-opacity-25 small">
            Ormawa: <strong>{{ $ormawaAktif?->nama_ormawa ?? 'Hima Sisfo' }}</strong>
        </div>
    </div>
</div>
    <div class="col-md-8">
        <div class="row g-3 h-100">
            <div class="col-sm-4">
                <div class="card shadow-sm border-0 h-100 p-3">
                    <span class="text-muted small text-uppercase">Total Aktivitas</span>
                    <h3 class="fw-bold text-dark mt-2 mb-0">{{ $totalAktivitas ?? 0 }}</h3>
                    <small class="text-muted">Aksi tercatat</small>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card shadow-sm border-0 h-100 p-3">
                    <span class="text-muted small text-uppercase">Proposal Masuk</span>
                    <h3 class="fw-bold text-primary mt-2 mb-0">{{ $totalProposalPeriode ?? 0 }}</h3>
                    <small class="text-muted">Kegiatan di periode ini</small>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card shadow-sm border-0 h-100 p-3">
                    <span class="text-muted small text-uppercase">Dana Terserap</span>
                    <h4 class="fw-bold text-success mt-2 mb-0" style="font-size:1.15rem;">Rp {{ number_format($totalDanaCairPeriode ?? 0, 0, ',', '.') }}</h4>
                    <small class="text-muted">Dari pagu Rp 7.000.000</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Timeline Seluruh Riwayat --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold text-primary"><i class="bi bi-clock-history me-2"></i>Log Jejak Audit & Riwayat Selama 1 Periode Penuh</h6>
        <span class="badge bg-light text-dark border">Urut Waktu Terbaru</span>
    </div>
    <div class="card-body p-4">
        @if($riwayats->count() > 0)
        <div class="position-relative ps-4" style="border-left: 2px solid #e2e8f0; margin-left: 10px;">
            @foreach($riwayats as $r)
            <div class="mb-4 position-relative">
                {{-- Bullet Icon --}}
                <div class="position-absolute" style="left: -31px; top: 0;">
                    <div class="rounded-circle bg-white border border-2 border-primary d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;">
                        <div class="rounded-circle bg-primary" style="width: 8px; height: 8px;"></div>
                    </div>
                </div>

                {{-- Konten Riwayat --}}
                <div class="bg-light p-3 rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-1">
                        <span class="fw-bold text-dark">{{ $r->aktivitas }}</span>
                        <small class="text-muted"><i class="bi bi-calendar-event me-1"></i>{{ $r->created_at->format('d F Y, H:i') }} WIB</small>
                    </div>

                    <p class="mb-2 text-secondary small">{{ $r->keterangan }}</p>

                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        @if($r->proposal)
                            <a href="{{ route('proposals.show', $r->proposal) }}" class="small text-decoration-none fw-semibold">
                                <i class="bi bi-link-45deg"></i> {{ $r->proposal->judul_kegiatan }}
                            </a>
                        @endif
                        @if($r->user)
                            <small class="text-muted"><i class="bi bi-person me-1"></i>Oleh: {{ $r->user->name }}</small>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center text-muted py-5">
            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-muted"></i>
            Belum ada riwayat aktivitas yang tercatat pada periode anggaran aktif ini.
        </div>
        @endif
    </div>
    @if($riwayats->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $riwayats->links() }}
    </div>
    @endif
</div>

@endsection
