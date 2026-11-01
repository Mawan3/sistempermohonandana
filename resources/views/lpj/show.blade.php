{{-- resources/views/lpj/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail LPJ: ' . ($lpj->proposal->judul_kegiatan ?? 'Kegiatan'))
@section('page-title', 'Detail Laporan Pertanggungjawaban (LPJ)')

@section('content')

{{-- Tombol Navigasi --}}
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('lpj.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar LPJ
    </a>
    <a href="{{ route('proposals.show', $lpj->proposal) }}" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-file-earmark-text me-1"></i> Lihat Data Proposal Awal
    </a>
</div>

{{-- Catatan Evaluasi Jika Perlu Perbaikan --}}
@if($lpj->status === 'perlu_perbaikan')
<div class="alert alert-warning border-warning border-opacity-50 shadow-sm d-flex align-items-start gap-3 mb-4">
    <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
    <div>
        <h6 class="fw-bold mb-1">Catatan Evaluasi PJ LPJ Kemahasiswaan</h6>
        <p class="mb-0">{{ $lpj->catatan_reviewer ?? 'Harap melengkapi berkas lampiran kuitansi belanja sesuai petunjuk.' }}</p>
    </div>
</div>
@endif

<div class="row g-4">
    {{-- Kolom Kiri: Rincian LPJ & Berkas --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-primary"><i class="bi bi-journal-text me-2"></i>Rincian Laporan Pertanggungjawaban</h6>
                <span class="badge bg-{{ $lpj->badge_status }} fs-6 px-3 py-2">{{ $lpj->label_status }}</span>
            </div>
            <div class="card-body">
                <h4 class="fw-bold text-dark mb-3">{{ $lpj->proposal->judul_kegiatan ?? '-' }}</h4>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Organisasi Mahasiswa (Ormawa)</small>
                        <span class="fw-semibold text-dark">{{ $lpj->proposal->ormawa->nama_ormawa ?? '-' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Tanggal Penyerahan LPJ</small>
                        <span class="fw-semibold text-dark">{{ $lpj->tanggal_kirim ? $lpj->tanggal_kirim->format('d F Y H:i') : '-' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Dana yang Dicairkan Sebelumnya</small>
                        <span class="fw-bold text-dark">Rp {{ number_format($lpj->proposal->anggaran_diajukan, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Total Realisasi Pengeluaran</small>
                        <span class="fs-5 fw-bold text-success">Rp {{ number_format($lpj->total_realisasi, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Perbandingan Realisasi vs Pencairan --}}
                <div class="p-3 bg-light rounded-3 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="small fw-semibold text-muted">Tingkat Penyerapan Dana:</span>
                        @php
                            $persenLpj = $lpj->proposal->anggaran_diajukan > 0 ? min(100, round(($lpj->total_realisasi / $lpj->proposal->anggaran_diajukan) * 100)) : 100;
                        @endphp
                        <span class="small fw-bold text-dark">{{ $persenLpj }}%</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $persenLpj }}%;"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold text-dark">Rincian Penggunaan Dana & Ketercapaian:</h6>
                    <div class="p-3 bg-light rounded-3 text-secondary" style="white-space: pre-line;">
                        {{ $lpj->rincian_penggunaan }}
                    </div>
                </div>

                {{-- Unduh Dokumen LPJ --}}
                <div class="border-top pt-3 mb-4">
                    <h6 class="fw-semibold text-dark mb-2"><i class="bi bi-file-earmark-pdf me-1"></i>Berkas Utama LPJ:</h6>
                    @if($lpj->file_lpj)
                        <div class="p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-2"></i>
                                <div>
                                    <span class="fw-semibold text-dark d-block">{{ basename($lpj->file_lpj) }}</span>
                                    <small class="text-muted">Laporan Pertanggungjawaban Resmi</small>
                                </div>
                            </div>
                            <a href="{{ route('lpj.download', $lpj) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-download me-1"></i> Unduh Berkas LPJ
                            </a>
                        </div>
                    @else
                        <div class="alert alert-secondary py-2 small mb-0">Belum ada file LPJ yang diunggah.</div>
                    @endif
                </div>

                {{-- Lampiran Bukti Kegiatan --}}
                <div class="border-top pt-3">
                    <h6 class="fw-semibold text-dark mb-3"><i class="bi bi-images me-1"></i>Lampiran Foto Kegiatan & Kuitansi ({{ $lpj->buktiKegiatan->count() }}):</h6>
                    
                    @if($lpj->buktiKegiatan->count() > 0)
                        <div class="row g-3">
                            @foreach($lpj->buktiKegiatan as $bukti)
                            <div class="col-sm-6 col-md-4">
                                <div class="card h-100 border shadow-sm">
                                    <div class="card-body p-2 text-center">
                                        @if(in_array(strtolower(pathinfo($bukti->path_file, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                            <a href="{{ asset('storage/' . $bukti->path_file) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $bukti->path_file) }}" class="img-fluid rounded mb-2" style="max-height: 120px; object-fit: cover;" alt="Bukti">
                                            </a>
                                        @else
                                            <div class="py-3 text-muted">
                                                <i class="bi bi-file-earmark-text fs-1 text-primary"></i>
                                            </div>
                                        @endif
                                        <div class="small fw-semibold text-truncate" title="{{ $bukti->nama_file }}">{{ $bukti->nama_file }}</div>
                                        <span class="badge bg-light text-muted border text-uppercase" style="font-size:0.65rem;">{{ $bukti->jenis }}</span>
                                    </div>
                                    <div class="card-footer bg-white border-top p-2 text-center">
                                        <a href="{{ asset('storage/' . $bukti->path_file) }}" target="_blank" class="btn btn-outline-secondary btn-sm py-0 px-2" style="font-size:0.75rem;">
                                            <i class="bi bi-eye"></i> Buka File
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-secondary py-2 small mb-0">Tidak ada lampiran foto/kuitansi tambahan.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Evaluasi Reviewer --}}
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-semibold text-dark"><i class="bi bi-person-check me-2"></i>Status Penilaian PJ LPJ</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted d-block">Status Saat Ini:</small>
                    <span class="badge bg-{{ $lpj->badge_status }} fs-6 px-3 py-2 mt-1">{{ $lpj->label_status }}</span>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Reviewer Penilai:</small>
                    <span class="fw-semibold text-dark">{{ $lpj->reviewer->name ?? 'PJ LPJ Kemahasiswaan' }}</span>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block">Tanggal Review:</small>
                    <span class="fw-semibold text-dark">{{ $lpj->tanggal_review ? $lpj->tanggal_review->format('d M Y H:i') : 'Menunggu antrean review' }}</span>
                </div>

                @if($lpj->catatan_reviewer)
                <div class="alert alert-warning py-2 small mb-0">
                    <strong>Catatan Review:</strong><br>
                    {{ $lpj->catatan_reviewer }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
