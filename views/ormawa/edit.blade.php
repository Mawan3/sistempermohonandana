@extends('layouts.app')

@section('title', 'Edit Ormawa')
@section('page-title', 'Edit Ormawa')

@section('content')
<div class="mb-3">
    <a href="{{ route('ormawa.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke daftar ormawa
    </a>
</div>

<div class="card" style="max-width: 600px">
    <div class="card-body p-4">
        <h6 class="fw-bold mb-3">Edit Data Ormawa</h6>

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

        <form action="{{ route('ormawa.update', $ormawa) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Ormawa <span class="text-danger">*</span></label>
                <input type="text" name="nama_ormawa"
                       class="form-control @error('nama_ormawa') is-invalid @enderror"
                       value="{{ old('nama_ormawa', $ormawa->nama_ormawa) }}">
                @error('nama_ormawa')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Singkatan</label>
                <input type="text" name="singkatan"
                       class="form-control @error('singkatan') is-invalid @enderror"
                       value="{{ old('singkatan', $ormawa->singkatan) }}"
                       maxlength="20">
                @error('singkatan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Ketua</label>
                    <input type="text" name="ketua"
                           class="form-control @error('ketua') is-invalid @enderror"
                           value="{{ old('ketua', $ormawa->ketua) }}">
                    @error('ketua')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Pembina</label>
                    <input type="text" name="pembina"
                           class="form-control @error('pembina') is-invalid @enderror"
                           value="{{ old('pembina', $ormawa->pembina) }}">
                    @error('pembina')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                <select name="jenis" class="form-select @error('jenis') is-invalid @enderror">
                    <option value="ormawa" {{ old('jenis', $ormawa->jenis) === 'ormawa' ? 'selected' : '' }}>Ormawa</option>
                    <option value="ortom" {{ old('jenis', $ormawa->jenis) === 'ortom' ? 'selected' : '' }}>Ortom</option>
                </select>
                @error('jenis')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="aktif" id="aktif" value="1"
                           {{ old('aktif', $ormawa->aktif) ? 'checked' : '' }}>
                    <label class="form-check-label" for="aktif">Ormawa Aktif</label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
                <a href="{{ route('ormawa.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
