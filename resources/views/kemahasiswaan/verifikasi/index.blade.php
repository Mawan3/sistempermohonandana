{{-- resources/views/kemahasiswaan/verifikasi/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Verifikasi Proposal Ormawa - Kemahasiswaan UM Kudus')
@section('page-title', 'Verifikasi Proposal Kemahasiswaan')

@section('content')

{{-- Header & Filter --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-md-6">
                <h5 class="fw-bold mb-1 text-primary"><i class="bi bi-patch-check me-2"></i>Daftar Proposal Masuk</h5>
                <p class="text-muted small mb-0">Verifikasi kelayakan kegiatan, anggaran, serta kelengkapan dokumen proposal mahasiswa.</p>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end gap-2">
                <form action="{{ route('kemahasiswaan.verifikasi.index') }}" method="GET" class="d-flex gap-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status Proposal</option>
                        <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Menunggu Review</option>
                        <option value="perlu_revisi" {{ request('status') === 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                        <option value="disetujui_kemahasiswaan" {{ request('status') === 'disetujui_kemahasiswaan' ? 'selected' : '' }}>Disetujui Kemahasiswaan</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Proposal --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Judul Kegiatan</th>
                        <th>Ormawa</th>
                        <th>Tanggal Pelaksanaan</th>
                        <th>Anggaran Diajukan</th>
                        <th>Dokumen Mahasiswa</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proposals as $index => $proposal)
                    <tr>
                        <td class="text-center text-muted">{{ $proposals->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $proposal->judul_kegiatan }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $proposal->lokasi ?? 'UM Kudus' }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $proposal->ormawa->singkatan ?? '-' }}</span></td>
                        <td>{{ $proposal->tanggal_kegiatan?->format('d M Y') ?? '-' }}</td>
                        <td class="fw-bold text-dark">Rp {{ number_format($proposal->anggaran_diajukan, 0, ',', '.') }}</td>
                        <td>
                            @if($proposal->file_proposal)
                                <div class="d-flex gap-1">
                                    <a href="{{ route('kemahasiswaan.verifikasi.preview', $proposal) }}" target="_blank" class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 text-decoration-none" title="Pratinjau PDF">
                                        <i class="bi bi-eye"></i> Preview
                                    </a>
                                    <a href="{{ route('kemahasiswaan.verifikasi.download', $proposal) }}" class="badge bg-light text-muted border text-decoration-none" title="Unduh Proposal">
                                        <i class="bi bi-download"></i> Unduh
                                    </a>
                                </div>
                            @else
                                <span class="badge bg-light text-muted border">Tidak Ada Berkas</span>
                            @endif
                        </td>
                        <td><span class="badge bg-{{ $proposal->badge_status }}">{{ $proposal->label_status }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('kemahasiswaan.verifikasi.show', $proposal) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-search me-1"></i> Periksa / Verifikasi
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                            Tidak ada proposal yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($proposals->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $proposals->links() }}
    </div>
    @endif
</div>

@endsection
