{{-- resources/views/admin/users/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru - Administrator SI Proma')
@section('page-title', 'Tambah Pengguna Baru')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-person-plus me-2"></i>Formulir Tambah Akun Pengguna</h5>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Pengguna
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    {{-- Nama Lengkap --}}
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Nama Lengkap Pengguna <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Contoh: Ahmad Fauzi, S.Kom." required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Contoh: ahmad@umkudus.ac.id" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Kata Sandi (Password) <span class="text-danger">*</span></label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        {{-- Role --}}
                        <div class="col-md-6">
                            <label for="role" class="form-label fw-semibold">Peran / Role Pengguna <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required onchange="toggleOrmawaField(this.value)">
                                <option value="">-- Pilih Peran --</option>
                                <option value="pengurus" {{ old('role') === 'pengurus' ? 'selected' : '' }}>Pengurus Ormawa (Pengaju)</option>
                                <option value="staf_kemahasiswaan" {{ old('role') === 'staf_kemahasiswaan' ? 'selected' : '' }}>Staf Kemahasiswaan (Verifikasi)</option>
                                <option value="pj_lpj" {{ old('role') === 'pj_lpj' ? 'selected' : '' }}>Penanggung Jawab LPJ (Review)</option>
                                <option value="staf_keuangan" {{ old('role') === 'staf_keuangan' ? 'selected' : '' }}>Staf Keuangan (Pencairan)</option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator (Master)</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Afiliasi Ormawa --}}
                        <div class="col-md-6" id="ormawaField">
                            <label for="ormawa_id" class="form-label fw-semibold">Afiliasi Ormawa (Khusus Pengurus)</label>
                            <select name="ormawa_id" id="ormawa_id" class="form-select @error('ormawa_id') is-invalid @enderror">
                                <option value="">-- Pilih Ormawa --</option>
                                @foreach($ormawaList as $item)
                                    <option value="{{ $item->id }}" {{ old('ormawa_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_ormawa }} ({{ $item->singkatan }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Wajib jika peran adalah Pengurus Ormawa.</small>
                            @error('ormawa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('users.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Pengguna
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
    document.addEventListener('DOMContentLoaded', function() {
        toggleOrmawaField(document.getElementById('role').value);
    });
</script>

@endsection
