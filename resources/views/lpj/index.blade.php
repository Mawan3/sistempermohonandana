{{-- resources/views/lpj/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Pengajuan & Riwayat LPJ - SI Proma UM Kudus')
@section('page-title', 'Laporan Pertanggungjawaban (LPJ)')

@section('content')

{{-- Bagian 1: Proposal yang Menunggu Pengajuan LPJ --}}
@if(isset($proposalsSiapLpj) && $proposalsSiapLpj->count() > 0)
<div class="card shadow-sm border-0 border-start border-success border-4 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="fw-bold text-success mb-1"><i class="bi bi-bell-fill me-2"></i>Proposal Siap Dibuatkan LPJ</h6>
                <p class="text-muted small mb-0">Dana kegiatan berikut telah dicairkan oleh Keuangan. Harap segera menyerahkan LPJ dalam waktu 7 hari kerja.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul Kegiatan</th>
                        <th>Ormawa</th>
                        <th>Dana Dicairkan</th>
                        <th>Bukti Transfer</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($proposalsSiapLpj as $siap)
                    <tr>
                        <td class="fw-semibold">{{ $siap->judul_kegiatan }}</td>
                        <td><span class="badge bg-secondary">{{ $siap->ormawa->singkatan ?? '-' }}</span></td>
                        <td class="fw-bold text-success">Rp {{ number_format($siap->anggaran_diajukan, 0, ',', '.') }}</td>
                        <td>
                            @if($siap->pencairan && $siap->pencairan->bukti_transfer)
                                <a href="{{ route('keuangan.validasi.bukti-transfer', $siap) }}" target="_blank" class="badge bg-light text-success border border-success border-opacity-25 text-decoration-none">
                                    <i class="bi bi-receipt"></i> Bukti Transfer
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">Tersedia di Kas</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('lpj.create', ['proposal_id' => $siap->id]) }}" class="btn btn-sm btn-success">
                                <i class="bi bi-journal-plus me-1"></i> Buat LPJ Sekarang
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Bagian 2: Daftar LPJ yang Telah Diajukan --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1 fw-bold text-primary"><i class="bi bi-journal-check me-2"></i>Daftar Laporan Pertanggungjawaban (LPJ)</h5>
            <small class="text-muted">Pantau status verifikasi LPJ oleh Penanggung Jawab LPJ Kemahasiswaan.</small>
        </div>
        <a href="{{ route('lpj.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Form Pengajuan LPJ
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Judul Proposal</th>
                        <th>Ormawa</th>
                        <th>Total Realisasi</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Dokumen & Bukti</th>
                        <th>Status Review</th>
                        <th class="text-center" style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lpjs as $index => $lpj)
                    <tr>
                        <td class="text-center text-muted">{{ $lpjs->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ route('lpj.show', $lpj) }}" class="fw-semibold text-dark text-decoration-none">
                                {{ $lpj->proposal->judul_kegiatan ?? '-' }}
                            </a>
                            <small class="d-block text-muted">Pengaju: {{ $lpj->user->name ?? 'Pengurus' }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $lpj->proposal->ormawa->singkatan ?? '-' }}</span></td>
                        <td class="fw-bold text-dark">Rp {{ number_format($lpj->total_realisasi, 0, ',', '.') }}</td>
                        <td>{{ $lpj->tanggal_kirim ? $lpj->tanggal_kirim->format('d M Y H:i') : '-' }}</td>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                @if($lpj->file_lpj)
                                    <a href="{{ route('lpj.download', $lpj) }}" class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 text-decoration-none">
                                        <i class="bi bi-file-earmark-pdf"></i> Dokumen LPJ
                                    </a>
                                @endif
                                <span class="badge bg-light text-muted border">
                                    <i class="bi bi-paperclip"></i> {{ $lpj->buktiKegiatan->count() }} Lampiran
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $lpj->badge_status }}">{{ $lpj->label_status }}</span>
                            @if($lpj->catatan_reviewer)
                                <small class="d-block text-danger mt-1"><i class="bi bi-info-circle"></i> Ada catatan revisi</small>
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('lpj.show', $lpj) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-journal-x fs-1 d-block mb-2 text-muted"></i>
                            Belum ada Laporan Pertanggungjawaban (LPJ) yang diajukan.
                            <div class="mt-2">
                                <a href="{{ route('lpj.create') }}" class="btn btn-sm btn-primary">Ajukan LPJ Kegiatan</a>
                            </div>
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
