{{-- resources/views/kemahasiswaan/verifikasi/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Verifikasi: ' . $proposal->judul_kegiatan)
@section('page-title', 'Verifikasi Proposal Kemahasiswaan')

@section('content')

{{-- Tombol Kembali --}}
<div class="mb-3">
    <a href="{{ route('kemahasiswaan.verifikasi.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Antrean Verifikasi
    </a>
</div>

<div class="row g-4">
    {{-- Kolom Kiri: Detail Proposal & Dokumen Mahasiswa --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-primary"><i class="bi bi-file-earmark-check me-2"></i>Berkas & Informasi Pengajuan</h6>
                <span class="badge bg-{{ $proposal->badge_status }} fs-6 px-3 py-2">{{ $proposal->label_status }}</span>
            </div>
            <div class="card-body">
                <h4 class="fw-bold text-dark mb-3">{{ $proposal->judul_kegiatan }}</h4>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Organisasi Mahasiswa (Ormawa)</small>
                        <span class="fw-semibold text-dark">{{ $proposal->ormawa->nama_ormawa ?? '-' }} ({{ $proposal->ormawa->singkatan ?? '-' }})</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Tanggal Kegiatan</small>
                        <span class="fw-semibold text-dark"><i class="bi bi-calendar3 me-1"></i>{{ $proposal->tanggal_kegiatan?->format('d F Y') ?? '-' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Lokasi Kegiatan</small>
                        <span class="fw-semibold text-dark"><i class="bi bi-geo-alt me-1"></i>{{ $proposal->lokasi ?? '-' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Anggaran yang Diajukan</small>
                        <span class="fs-5 fw-bold text-primary">Rp {{ number_format($proposal->anggaran_diajukan, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold text-dark">Rincian Kegiatan:</h6>
                    <div class="p-3 bg-light rounded-3 text-secondary" style="white-space: pre-line;">
                        {{ $proposal->deskripsi }}
                    </div>
                </div>

                {{-- Area Berkas Proposal Mahasiswa (Lihat & Unduh) --}}
                <div class="border-top pt-3">
                    <h6 class="fw-semibold text-dark mb-3"><i class="bi bi-paperclip me-1"></i>Dokumen Proposal Mahasiswa:</h6>
                    
                    @if($proposal->file_proposal)
                        <div class="p-3 border rounded-3 bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-2"></i>
                                <div>
                                    <div class="fw-semibold text-dark">{{ basename($proposal->file_proposal) }}</div>
                                    <small class="text-muted">File proposal resmi yang diunggah oleh mahasiswa</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                {{-- Tombol Preview PDF --}}
                                <a href="{{ route('kemahasiswaan.verifikasi.preview', $proposal) }}" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i> Preview PDF (Pop-up/Tab)
                                </a>
                                {{-- Tombol Download --}}
                                <a href="{{ route('kemahasiswaan.verifikasi.download', $proposal) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-download me-1"></i> Unduh File
                                </a>
                            </div>
                        </div>

                        {{-- Inline PDF Viewer --}}
                        @if(Str::endsWith(strtolower($proposal->file_proposal), '.pdf'))
                        <div class="card border mb-3">
                            <div class="card-header bg-white py-2 small fw-semibold text-muted d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-file-text me-1"></i>Pratinjau Lembar Dokumen Proposal:</span>
                                <a href="{{ route('kemahasiswaan.verifikasi.preview', $proposal) }}" target="_blank" class="small text-decoration-none">Buka Tab Penuh <i class="bi bi-box-arrow-up-right"></i></a>
                            </div>
                            <div class="card-body p-0">
                                <iframe src="{{ route('kemahasiswaan.verifikasi.preview', $proposal) }}" style="width:100%; height:450px; border:none;" title="Preview Proposal"></iframe>
                            </div>
                        </div>
                        @endif

                    @else
                        <div class="alert alert-warning py-2 small">
                            <i class="bi bi-exclamation-triangle me-1"></i> Mahasiswa belum mengunggah file berkas proposal.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Panel Keputusan Verifikasi Kemahasiswaan --}}
    <div class="col-lg-4">
        {{-- Card 1: Keputusan ACC --}}
        <div class="card shadow-sm border-0 mb-4 border-top border-success border-4">
            <div class="card-body">
                <h6 class="fw-bold text-success mb-2"><i class="bi bi-check-circle-fill me-1"></i>Setujui / ACC Proposal</h6>
                <p class="small text-muted mb-3">Jika dokumen dan kegiatan telah memenuhi syarat, setujui proposal agar diteruskan ke Bagian Keuangan untuk validasi anggaran.</p>
                
                <form action="{{ route('kemahasiswaan.verifikasi.acc', $proposal) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui proposal ini untuk diteruskan ke Keuangan?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success w-100 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i> ACC & Teruskan ke Keuangan
                    </button>
                </form>
            </div>
        </div>

        {{-- Card 2: Permintaan Revisi & Unggah Berkas Koreksi --}}
        <div class="card shadow-sm border-0 border-top border-warning border-4 mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-warning mb-2"><i class="bi bi-pencil-square me-1"></i>Minta Revisi Proposal</h6>
                <p class="small text-muted mb-3">Berikan catatan perbaikan dan lampirkan berkas coretan koreksi (opsional) untuk dipelajari oleh Ormawa.</p>
                
                <form action="{{ route('kemahasiswaan.verifikasi.revisi', $proposal) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    {{-- Catatan Reviewer --}}
                    <div class="mb-3">
                        <label for="catatan_reviewer" class="form-label small fw-semibold">Catatan Koreksi & Perbaikan <span class="text-danger">*</span></label>
                        <textarea name="catatan_reviewer" id="catatan_reviewer" rows="4" class="form-control form-control-sm @error('catatan_reviewer') is-invalid @enderror" placeholder="Tuliskan bagian mana saja yang perlu diperbaiki (anggaran, lokasi, jadwal, dll)..." required>{{ old('catatan_reviewer', $proposal->catatan_reviewer) }}</textarea>
                        @error('catatan_reviewer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Input Unggah File Koreksi (Fitur Baru) --}}
                    <div class="mb-3">
                        <label for="file_koreksi" class="form-label small fw-semibold">Unggah File Koreksi (PDF/Word/Gambar)</label>
                        <input type="file" name="file_koreksi" id="file_koreksi" class="form-control form-control-sm @error('file_koreksi') is-invalid @enderror" accept=".pdf,.doc,.docx,image/*">
                        <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Lampirkan berkas catatan, scanned review, atau PDF bercoretan.</small>
                        @error('file_koreksi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if($proposal->file_koreksi)
                            <div class="mt-2 small text-muted">
                                File koreksi sebelumnya: 
                                <a href="{{ route('proposals.download-koreksi', $proposal) }}" class="text-danger text-decoration-none fw-semibold">
                                    <i class="bi bi-file-earmark-arrow-down"></i> Unduh File
                                </a>
                            </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-semibold text-dark">
                        <i class="bi bi-send me-1"></i> Kirim Catatan & Berkas Revisi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
