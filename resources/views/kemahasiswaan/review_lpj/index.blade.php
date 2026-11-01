{{-- resources/views/kemahasiswaan/review_lpj/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Review LPJ Masuk - PJ LPJ Kemahasiswaan')
@section('page-title', 'Review Laporan Pertanggungjawaban (LPJ)')

@section('content')

{{-- Header & Filter --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-md-6">
                <h5 class="fw-bold mb-1 text-purple" style="color:#654ea3;"><i class="bi bi-clipboard2-check me-2"></i>Daftar LPJ Menunggu Pemeriksaan</h5>
                <p class="text-muted small mb-0">Periksa realisasi penggunaan dana, nota belanja, serta laporan kegiatan dari Ormawa.</p>
            </div>
            <div class="col-md-6 d-flex justify-content-md-end gap-2">
                <form action="{{ route('kemahasiswaan.review-lpj.index') }}" method="GET" class="d-flex gap-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status Review</option>
                        <option value="menunggu_review" {{ request('status') === 'menunggu_review' ? 'selected' : '' }}>Menunggu Review</option>
                        <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="perlu_perbaikan" {{ request('status') === 'perlu_perbaikan' ? 'selected' : '' }}>Perlu Perbaikan</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Tabel LPJ --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Judul Proposal</th>
                        <th>Ormawa</th>
                        <th>Dana Dicairkan</th>
                        <th>Realisasi LPJ</th>
                        <th>Tanggal Kirim</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lpjs as $index => $lpj)
                    <tr>
                        <td class="text-center text-muted">{{ $lpjs->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $lpj->proposal->judul_kegiatan ?? '-' }}</div>
                            <small class="text-muted">Diajukan: {{ $lpj->user->name ?? 'Pengurus' }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $lpj->proposal->ormawa->singkatan ?? '-' }}</span></td>
                        <td>Rp {{ number_format($lpj->proposal->anggaran_diajukan ?? 0, 0, ',', '.') }}</td>
                        <td class="fw-bold text-success">Rp {{ number_format($lpj->total_realisasi, 0, ',', '.') }}</td>
                        <td>{{ $lpj->tanggal_kirim ? $lpj->tanggal_kirim->format('d M Y') : '-' }}</td>
                        <td><span class="badge bg-{{ $lpj->badge_status }}">{{ $lpj->label_status }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('kemahasiswaan.review-lpj.show', $lpj) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-search me-1"></i> Periksa LPJ
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-clipboard-x fs-1 d-block mb-2 text-muted"></i>
                            Tidak ada LPJ yang perlu di-review saat ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($lpjs->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $lpjs->links() }}
    </div>
    @endif
</div>

@endsection
