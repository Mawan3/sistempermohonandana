{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Ubah Role Pengguna: ' . $user->name)
@section('page-title', 'Ubah Role Pengguna Sistem')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        {{-- Banner Informasi Batasan Hak Akses Administrator --}}
        <div class="alert alert-info border border-info border-opacity-25 shadow-sm d-flex align-items-center gap-2 mb-4 py-3 rounded-3">
            <i class="bi bi-shield-check fs-4 text-info"></i>
            <div>
                <strong class="d-block">Batasan Otoritas Administrator:</strong>
                <span class="small text-muted">Sesuai aturan keamanan sistem, fitur edit pengguna pada modul Administrator dibatasi secara ketat hanya untuk mengubah <strong>Peran/Role Pengguna</strong>. Nama, email, dan akses akun pengguna dilindungi.</span>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-person-gear me-2"></i>Edit Role Akun: {{ $user->name }}</h5>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Nama Pengguna (Readonly) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Nama Lengkap Pengguna</label>
                        <input type="text" class="form-control bg-light text-dark" value="{{ $user->name }}" readonly disabled>
                        <small class="text-muted">Nama pengguna bersifat tetap dan tidak dapat diubah oleh Administrator.</small>
                    </div>

                    {{-- Email Pengguna (Readonly) --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted">Alamat Email Pengguna</label>
                        <input type="email" class="form-control bg-light text-dark" value="{{ $user->email }}" readonly disabled>
                        <small class="text-muted">Alamat email digunakan untuk login dan tidak dapat diubah oleh Administrator.</small>
                    </div>

                    {{-- Role Pengguna (Dapat diubah) --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="role" class="form-label fw-semibold">Pilih Role / Peran Baru <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required onchange="toggleOrmawaField(this.value)">
                                <option value="pengurus" {{ old('role', $user->role) === 'pengurus' ? 'selected' : '' }}>Pengurus Ormawa (Pengaju)</option>
                                <option value="staf_kemahasiswaan" {{ old('role', $user->role) === 'staf_kemahasiswaan' ? 'selected' : '' }}>Staf Kemahasiswaan (Verifikasi)</option>
                                <option value="pj_lpj" {{ old('role', $user->role) === 'pj_lpj' ? 'selected' : '' }}>Penanggung Jawab LPJ (Review)</option>
                                <option value="staf_keuangan" {{ old('role', $user->role) === 'staf_keuangan' ? 'selected' : '' }}>Staf Keuangan (Pencairan)</option>
                                <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator (Master Data)</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6" id="ormawaField" style="{{ old('role', $user->role) === 'pengurus' ? '' : 'display:none;' }}">
                            <label for="ormawa_id" class="form-label fw-semibold">Afiliasi Ormawa</label>
                            <select name="ormawa_id" id="ormawa_id" class="form-select @error('ormawa_id') is-invalid @enderror">
                                <option value="">-- Pilih Organisasi Mahasiswa --</option>
                                @foreach($ormawaList as $item)
                                    <option value="{{ $item->id }}" {{ old('ormawa_id', $user->ormawa_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_ormawa }} ({{ $item->singkatan }})
                                    </option>
                                @endforeach
                            </select>
                            @error('ormawa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Pilih Ormawa yang berhak dikelola oleh akun ini.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('users.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-shield-check me-1"></i> Simpan Perubahan Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleOrmawaField(role) {
        const field = document.getElementById('ormawaField');
        if (role === 'pengurus') {
            field.style.display = 'block';
        } else {
            field.style.display = 'none';
        }
    }
</script>

@endsection
