{{-- resources/views/kemahasiswaan/review_lpj/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Pemeriksaan LPJ: ' . ($lpj->proposal->judul_kegiatan ?? 'Kegiatan'))
@section('page-title', 'Pemeriksaan & Review LPJ')

@section('content')

{{-- Tombol Kembali --}}
<div class="mb-3">
    <a href="{{ route('kemahasiswaan.review-lpj.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Antrean LPJ
    </a>
</div>

<div class="row g-4">
    {{-- Kolom Kiri: Detail Laporan, Bukti Kuitansi & Foto --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-primary"><i class="bi bi-journal-text me-2"></i>Rincian Laporan LPJ Ormawa</h6>
                <span class="badge bg-{{ $lpj->badge_status }} fs-6 px-3 py-2">{{ $lpj->label_status }}</span>
            </div>
            <div class="card-body">
                <h4 class="fw-bold text-dark mb-3">{{ $lpj->proposal->judul_kegiatan ?? '-' }}</h4>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Ormawa Penyelenggara</small>
                        <span class="fw-semibold text-dark">{{ $lpj->proposal->ormawa->nama_ormawa ?? '-' }} ({{ $lpj->proposal->ormawa->singkatan ?? '-' }})</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Pengurus Penanggung Jawab</small>
                        <span class="fw-semibold text-dark">{{ $lpj->user->name ?? 'Pengurus Ormawa' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Dana Dicairkan Keuangan</small>
                        <span class="fw-bold text-dark">Rp {{ number_format($lpj->proposal->anggaran_diajukan ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Total Realisasi Pengeluaran</small>
                        <span class="fs-5 fw-bold text-success">Rp {{ number_format($lpj->total_realisasi, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold text-dark">Rincian Penggunaan Dana & Hasil Kegiatan:</h6>
                    <div class="p-3 bg-light rounded-3 text-secondary" style="white-space: pre-line;">
                        {{ $lpj->rincian_penggunaan }}
                    </div>
                </div>

                {{-- Unduh Dokumen LPJ --}}
                @if($lpj->file_lpj)
                <div class="border-top pt-3 mb-4">
                    <div class="p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-2"></i>
                            <div>
                                <span class="fw-semibold text-dark d-block">{{ basename($lpj->file_lpj) }}</span>
                                <small class="text-muted">Berkas Laporan Pertanggungjawaban Lengkap</small>
                            </div>
                        </div>
                        <a href="{{ route('lpj.download', $lpj) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-download me-1"></i> Unduh Berkas LPJ
                        </a>
                    </div>
                </div>
                @endif

                {{-- Lampiran Bukti Foto & Kuitansi --}}
                <div class="border-top pt-3">
                    <h6 class="fw-semibold text-dark mb-3"><i class="bi bi-images me-1"></i>Lampiran Bukti Kegiatan & Nota ({{ $lpj->buktiKegiatan->count() }}):</h6>
                    
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

    {{-- Kolom Kanan: Panel Penilaian PJ LPJ --}}
    <div class="col-lg-4">
        {{-- Card ACC LPJ --}}
        <div class="card shadow-sm border-0 border-top border-success border-4 mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-success mb-2"><i class="bi bi-check-circle-fill me-1"></i>Setujui / ACC LPJ</h6>
                <p class="small text-muted mb-3">Jika bukti penggunaan dana telah sesuai, nota lengkap, dan kegiatan telah selesai terlaksana, setujui LPJ ini untuk menyelesaikan seluruh siklus permohonan dana.</p>
                
                <form action="{{ route('kemahasiswaan.review-lpj.acc', $lpj) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui LPJ ini?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success w-100 fw-semibold">
                        <i class="bi bi-patch-check-fill me-1"></i> ACC & Selesaikan LPJ
                    </button>
                </form>
            </div>
        </div>

        {{-- Card Permintaan Perbaikan LPJ --}}
        <div class="card shadow-sm border-0 border-top border-warning border-4">
            <div class="card-body">
                <h6 class="fw-bold text-warning mb-2"><i class="bi bi-pencil-square me-1"></i>Minta Perbaikan LPJ</h6>
                <p class="small text-muted mb-3">Kirimkan catatan evaluasi kepada Ormawa jika bukti nota tidak lengkap atau format pelaporan belum sesuai.</p>
                
                <form action="{{ route('kemahasiswaan.review-lpj.perbaikan', $lpj) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="catatan_reviewer" class="form-label small fw-semibold">Catatan Evaluasi / Kekurangan <span class="text-danger">*</span></label>
                        <textarea name="catatan_reviewer" id="catatan_reviewer" rows="4" class="form-control form-control-sm @error('catatan_reviewer') is-invalid @enderror" placeholder="Sebutkan bukti kuitansi atau bagian SPJ yang kurang lengkap..." required>{{ old('catatan_reviewer', $lpj->catatan_reviewer) }}</textarea>
                        @error('catatan_reviewer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-semibold text-dark">
                        <i class="bi bi-send me-1"></i> Kirim Catatan Perbaikan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
