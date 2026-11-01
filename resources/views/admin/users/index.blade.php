{{-- resources/views/admin/users/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manajemen Pengguna - Administrator SI Proma')
@section('page-title', 'Manajemen Pengguna Sistem')

@section('content')

{{-- Header & Aksi --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-md-5">
                <h5 class="fw-bold mb-1 text-primary"><i class="bi bi-people me-2"></i>Daftar Akun Pengelola Sistem</h5>
                <p class="text-muted small mb-0">Kelola akun dan hak akses peran: Ormawa, Kemahasiswaan, PJ LPJ, Keuangan, dan Administrator.</p>
            </div>
            <div class="col-md-7 d-flex justify-content-md-end gap-2 flex-wrap">
                <form action="{{ route('users.index') }}" method="GET" class="d-flex gap-2">
                    <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Peran / Role</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="staf_kemahasiswaan" {{ request('role') === 'staf_kemahasiswaan' ? 'selected' : '' }}>Staf Kemahasiswaan</option>
                        <option value="pj_lpj" {{ request('role') === 'pj_lpj' ? 'selected' : '' }}>PJ LPJ</option>
                        <option value="staf_keuangan" {{ request('role') === 'staf_keuangan' ? 'selected' : '' }}>Bagian Keuangan</option>
                        <option value="pengurus" {{ request('role') === 'pengurus' ? 'selected' : '' }}>Pengurus Ormawa</option>
                    </select>
                </form>

                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-person-plus me-1"></i> Tambah Pengguna Baru
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Data Pengguna --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Nama Pengguna</th>
                        <th>Alamat Email</th>
                        <th>Peran / Role</th>
                        <th>Ormawa Terafiliasi</th>
                        <th>Terdaftar</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $index => $u)
                    <tr>
                        <td class="text-center text-muted">{{ $users->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $u->name }}</div>
                        </td>
                        <td><code class="text-muted">{{ $u->email }}</code></td>
                        <td>
                            @if($u->role === 'admin')
                                <span class="badge bg-danger">Administrator</span>
                            @elseif($u->role === 'staf_kemahasiswaan')
                                <span class="badge bg-primary">Staf Kemahasiswaan</span>
                            @elseif($u->role === 'pj_lpj')
                                <span class="badge" style="background:#654ea3; color:#fff;">PJ LPJ</span>
                            @elseif($u->role === 'staf_keuangan')
                                <span class="badge bg-success">Bagian Keuangan</span>
                            @elseif($u->role === 'pengurus')
                                <span class="badge bg-warning text-dark">Pengurus Ormawa</span>
                            @else
                                <span class="badge bg-secondary">{{ $u->role }}</span>
                            @endif
                        </td>
                        <td>
                            @if($u->ormawa)
                                <span class="badge bg-secondary">{{ $u->ormawa->singkatan }}</span>
                                <small class="text-muted d-block">{{ $u->ormawa->nama_ormawa }}</small>
                            @else
                                <span class="text-muted small">&ndash;</span>
                            @endif
                        </td>
                        <td>{{ $u->created_at->format('d M Y') }}</td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('users.edit', $u) }}" class="btn btn-outline-primary" title="Edit Akun">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if(auth()->id() !== $u->id)
                                <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun pengguna ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus Akun">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            Belum ada pengguna yang sesuai.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $users->links() }}
    </div>
    @endif
</div>

@endsection
