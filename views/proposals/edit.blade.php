@extends('layouts.app')

@section('title', 'Edit Proposal')
@section('page-title', 'Edit Proposal')

@section('content')
<div class="mb-3">
    <a href="{{ route('proposals.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke daftar proposal
    </a>
</div>

<div class="card" style="max-width: 720px">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Edit Proposal</h6>
            <span class="badge bg-{{ $proposal->badge_status }}">{{ $proposal->label_status }}</span>
        </div>

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

        <form action="{{ route('proposals.update', $proposal) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Ormawa <span class="text-danger">*</span></label>
                <select name="ormawa_id" class="form-select @error('ormawa_id') is-invalid @enderror">
                    <option value="">-- Pilih Ormawa --</option>
                    @foreach($ormawa as $o)
                        <option value="{{ $o->id }}"
                            {{ old('ormawa_id', $proposal->ormawa_id) == $o->id ? 'selected' : '' }}>
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
                       value="{{ old('judul_kegiatan', $proposal->judul_kegiatan) }}">
                @error('judul_kegiatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                          class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $proposal->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Kegiatan <span class="text-danger">*</span></label>
                    <input type="date" name="tanggal_kegiatan"
                           class="form-control @error('tanggal_kegiatan') is-invalid @enderror"
                           value="{{ old('tanggal_kegiatan', $proposal->tanggal_kegiatan?->format('Y-m-d')) }}">
                    @error('tanggal_kegiatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Lokasi</label>
                    <input type="text" name="lokasi"
                           class="form-control @error('lokasi') is-invalid @enderror"
                           value="{{ old('lokasi', $proposal->lokasi) }}">
                    @error('lokasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Anggaran Diajukan (Rp) <span class="text-danger">*</span></label>
                <input type="number" name="anggaran_diajukan"
                       class="form-control @error('anggaran_diajukan') is-invalid @enderror"
                       value="{{ old('anggaran_diajukan', $proposal->anggaran_diajukan) }}"
                       min="0" step="1000">
                @error('anggaran_diajukan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
                <a href="{{ route('proposals.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
