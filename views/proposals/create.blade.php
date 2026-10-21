@extends('layouts.app')

@section('title', 'Ajukan Proposal')
@section('page-title', 'Ajukan Proposal Baru')

@section('content')
<div class="mb-3">
    <a href="{{ route('proposals.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke daftar proposal
    </a>
</div>

<div class="card" style="max-width: 720px">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3">Form Pengajuan Proposal Kegiatan</h6>

        {{-- 10. Tampilkan error validasi --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle me-1"></i>
                <strong>Mohon perbaiki kesalahan berikut:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('proposals.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Ormawa <span class="text-danger">*</span></label>
                <select name="ormawa_id" class="form-select @error('ormawa_id') is-invalid @enderror">
                    <option value="">-- Pilih Ormawa --</option>
                    @foreach($ormawa as $o)
                        <option value="{{ $o->id }}" {{ old('ormawa_id') == $o->id ? 'selected' : '' }}>
                            {{ $o->nama_ormawa }}
                            @if($o->singkatan) ({{ $o->singkatan }}) @endif
                        </option>
                    @endforeach
                </select>
                @error('ormawa_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Judul Kegiatan <span class="text-danger">*</span></label>
                <input type="text" name="judul_kegiatan"
                       class="form-control @error('judul_kegiatan') is-invalid @enderror"
                       value="{{ old('judul_kegiatan') }}"
                       placeholder="Contoh: Seminar Nasional Kewirausahaan 2024">
                @error('judul_kegiatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                          class="form-control @error('deskripsi') is-invalid @enderror"
                          placeholder="Uraikan tujuan dan gambaran kegiatan...">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Kegiatan <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_kegiatan"
                           class="form-control @error('tanggal_kegiatan') is-invalid @enderror"
                           value="{{ old('tanggal_kegiatan') }}">
                    @error('tanggal_kegiatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Lokasi</label>
                    <input type="text" name="lokasi"
                           class="form-control @error('lokasi') is-invalid @enderror"
                           value="{{ old('lokasi') }}"
                           placeholder="Gedung Aula, Kampus UM Kudus">
                    @error('lokasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Anggaran Diajukan (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="anggaran_diajukan"
                       class="form-control @error('anggaran_diajukan') is-invalid @enderror"
                       value="{{ old('anggaran_diajukan', 0) }}"
                       min="0" step="1000">
                @error('anggaran_diajukan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i> Ajukan Proposal
                </button>
                <a href="{{ route('proposals.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
