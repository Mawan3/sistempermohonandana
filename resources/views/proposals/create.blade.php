{{-- resources/views/proposals/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Ajukan Proposal Baru - SI Proma UM Kudus')
@section('page-title', 'Ajukan Proposal Baru')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-file-earmark-plus me-2"></i>Form Pengajuan Kegiatan & Anggaran</h5>
                <a href="{{ route('proposals.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body p-4">
                
                {{-- Info Plafon --}}
                <div class="alert alert-info py-2 px-3 small d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Plafon anggaran per Ormawa: <strong>Rp 7.000.000</strong> per periode aktif.
                    </div>
                    <div>
                        Sisa pagu tersedia: <strong class="text-success">Rp {{ number_format($sisaPlafon ?? 7000000, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <form action="{{ route('proposals.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Ormawa Pengusul --}}
                    <div class="mb-3">
                        <label for="ormawa_id" class="form-label fw-semibold">Ormawa Pengaju <span class="text-danger">*</span></label>
                        <select name="ormawa_id" id="ormawa_id" class="form-select @error('ormawa_id') is-invalid @enderror" required>
                            @foreach($ormawaList as $item)
                                <option value="{{ $item->id }}" {{ (old('ormawa_id', $ormawa->id ?? 1) == $item->id) ? 'selected' : '' }}>
                                    {{ $item->nama_ormawa }} ({{ $item->singkatan }})
                                </option>
                            @endforeach
                        </select>
                        @error('ormawa_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Judul Kegiatan --}}
                    <div class="mb-3">
                        <label for="judul_kegiatan" class="form-label fw-semibold">Judul Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" name="judul_kegiatan" id="judul_kegiatan" class="form-control @error('judul_kegiatan') is-invalid @enderror" value="{{ old('judul_kegiatan') }}" placeholder="Contoh: Seminar Nasional Kecerdasan Buatan 2026" required>
                        @error('judul_kegiatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deskripsi Kegiatan --}}
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label fw-semibold">Deskripsi & Tujuan Kegiatan <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" id="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror" placeholder="Jelaskan latar belakang, tujuan kegiatan, dan output yang diharapkan..." required>{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-3">
                        {{-- Tanggal Pelaksanaan --}}
                        <div class="col-md-6">
                            <label for="tanggal_kegiatan" class="form-label fw-semibold">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_kegiatan" id="tanggal_kegiatan" class="form-control @error('tanggal_kegiatan') is-invalid @enderror" value="{{ old('tanggal_kegiatan', now()->addDays(14)->toDateString()) }}" required>
                            @error('tanggal_kegiatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Lokasi Kegiatan --}}
                        <div class="col-md-6">
                            <label for="lokasi" class="form-label fw-semibold">Lokasi Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="lokasi" id="lokasi" class="form-control @error('lokasi') is-invalid @enderror" value="{{ old('lokasi') }}" placeholder="Contoh: Auditorium Kampus 1 UM Kudus" required>
                            @error('lokasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        {{-- Anggaran yang Diajukan --}}
                        <div class="col-md-6">
                            <label for="anggaran_diajukan" class="form-label fw-semibold">Anggaran yang Diajukan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="anggaran_diajukan" id="anggaran_diajukan" class="form-control @error('anggaran_diajukan') is-invalid @enderror" value="{{ old('anggaran_diajukan', 2500000) }}" min="100000" max="7000000" required>
                            </div>
                            <small class="text-muted">Maksimal Rp 7.000.000 per kegiatan.</small>
                            @error('anggaran_diajukan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Upload Dokumen Proposal --}}
                        <div class="col-md-6">
                            <label for="file_proposal" class="form-label fw-semibold">Unggah Dokumen Proposal (PDF/Word) <span class="text-danger">*</span></label>
                            <input type="file" name="file_proposal" id="file_proposal" class="form-control @error('file_proposal') is-invalid @enderror" accept=".pdf,.doc,.docx" required>
                            <small class="text-muted">Format: .pdf, .doc, .docx (Maksimal 10 MB).</small>
                            @error('file_proposal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('proposals.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-send me-1"></i> Ajukan Proposal Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
