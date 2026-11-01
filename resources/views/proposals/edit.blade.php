{{-- resources/views/proposals/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Proposal: ' . $proposal->judul_kegiatan)
@section('page-title', 'Edit Proposal Kegiatan')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-pencil-square me-2"></i>Perbarui Data Proposal</h5>
                <a href="{{ route('proposals.show', $proposal) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Detail
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('proposals.update', $proposal) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="judul_kegiatan" class="form-label fw-semibold">Judul Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" name="judul_kegiatan" id="judul_kegiatan" class="form-control @error('judul_kegiatan') is-invalid @enderror" value="{{ old('judul_kegiatan', $proposal->judul_kegiatan) }}" required>
                        @error('judul_kegiatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label fw-semibold">Deskripsi & Tujuan Kegiatan <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" required>{{ old('deskripsi', $proposal->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="tanggal_kegiatan" class="form-label fw-semibold">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" class="form-control @error('tanggal_kegiatan') is-invalid @enderror" value="{{ old('tanggal_kegiatan', $proposal->tanggal_kegiatan?->toDateString()) }}" required>
                            @error('tanggal_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="lokasi" class="form-label fw-semibold">Lokasi Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" id="lokasi" class="form-control @error('lokasi') is-invalid @enderror" value="{{ old('lokasi', $proposal->lokasi) }}" required>
                            @error('lokasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="anggaran_diajukan" class="form-label fw-semibold">Anggaran yang Diajukan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="anggaran_diajukan" id="anggaran_diajukan" class="form-control @error('anggaran_diajukan') is-invalid @enderror" value="{{ old('anggaran_diajukan', $proposal->anggaran_diajukan) }}" min="100000" max="7000000" required>
                            </div>
                            @error('anggaran_diajukan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="file_proposal" class="form-label fw-semibold">Ganti File Proposal (Opsional)</label>
                            <input type="file" name="file_proposal" id="file_proposal" class="form-control @error('file_proposal') is-invalid @enderror" accept=".pdf,.doc,.docx">
                            @if($proposal->file_proposal)
                                <small class="text-muted d-block mt-1">File saat ini: <a href="{{ route('proposals.preview', $proposal) }}" target="_blank">{{ basename($proposal->file_proposal) }}</a></small>
                            @endif
                            @error('file_proposal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('proposals.show', $proposal) }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
