{{-- resources/views/ormawa/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Data Master Ormawa - Universitas Muhammadiyah Kudus')
@section('page-title', 'Master Data Organisasi Mahasiswa')

@section('content')

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-primary"><i class="bi bi-building me-2"></i>Data Organisasi Mahasiswa (Ormawa & Ortom)</h5>
                <p class="text-muted small mb-0">Daftar lembaga kemahasiswaan aktif di lingkungan Universitas Muhammadiyah Kudus.</p>
            </div>
            <span class="badge bg-primary rounded-pill px-3 py-2">UM Kudus</span>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach($ormawas as $o)
    <div class="col-md-6 col-xl-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge {{ $o->jenis === 'ortom' ? 'bg-success' : 'bg-primary' }} text-uppercase" style="font-size:0.65rem;">
                        {{ $o->jenis === 'ortom' ? 'Ortom Muhammadiyah' : 'Ormawa Kampus' }}
                    </span>
                    <span class="badge bg-light text-success border"><i class="bi bi-check-circle-fill"></i> Aktif</span>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ $o->singkatan }}</h5>
                <p class="text-muted small mb-3">{{ $o->nama_ormawa }}</p>

                <div class="pt-2 border-top small">
                    <div class="mb-1"><span class="text-muted">Ketua:</span> <strong class="text-dark">{{ $o->ketua ?? '-' }}</strong></div>
                    <div><span class="text-muted">Pembina:</span> <strong class="text-dark">{{ $o->pembina ?? '-' }}</strong></div>
                </div>
            </div>
            <div class="card-footer bg-white border-top p-2 text-center">
                <a href="{{ route('switch.ormawa', $o->id) }}" class="btn btn-sm btn-outline-primary w-100" style="font-size:0.75rem;">
                    <i class="bi bi-person-badge me-1"></i> Buka Sebagai Ormawa Ini
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3">
        <h6 class="mb-0 fw-semibold text-primary"><i class="bi bi-table me-2"></i>Tabel Master Data Ormawa</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Nama Ormawa</th>
                        <th>Singkatan</th>
                        <th>Jenis Lembaga</th>
                        <th>Ketua Umum</th>
                        <th>Dosen Pembina</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ormawas as $index => $item)
                    <tr>
                        <td class="text-center text-muted">{{ $index + 1 }}</td>
                        <td class="fw-semibold text-dark">{{ $item->nama_ormawa }}</td>
                        <td><span class="badge bg-secondary">{{ $item->singkatan }}</span></td>
                        <td>
                            <span class="badge {{ $item->jenis === 'ortom' ? 'bg-success' : 'bg-primary' }}">
                                {{ $item->jenis === 'ortom' ? 'Ortom' : 'Ormawa' }}
                            </span>
                        </td>
                        <td>{{ $item->ketua ?? '-' }}</td>
                        <td>{{ $item->pembina ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Aktif</span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data Ormawa</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
