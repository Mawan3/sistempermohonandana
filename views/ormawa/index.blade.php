@extends('layouts.app')

@section('title', 'Data Ormawa')
@section('page-title', 'Data Ormawa & Ortom')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Daftar seluruh organisasi mahasiswa yang terdaftar.</p>
    <a href="{{ route('ormawa.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Tambah Ormawa
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-3" style="width:40px">#</th>
                        <th>Nama Ormawa</th>
                        <th>Singkatan</th>
                        <th>Ketua</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        <th>Proposal</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ormawa as $i => $o)
                    <tr>
                        <td class="ps-3 text-muted">{{ $ormawa->firstItem() + $i }}</td>
                        <td class="fw-semibold">{{ $o->nama_ormawa }}</td>
                        <td>{{ $o->singkatan ?? '-' }}</td>
                        <td>{{ $o->ketua ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $o->jenis === 'ortom' ? 'info' : 'secondary' }} text-capitalize">
                                {{ $o->jenis }}
                            </span>
                        </td>
                        <td>
                            @if($o->aktif)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $o->proposals_count ?? $o->proposals()->count() }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('ormawa.edit', $o) }}"
                               class="btn btn-outline-primary btn-sm" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('ormawa.destroy', $o) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus ormawa ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-building fs-3 d-block mb-1"></i>
                            Belum ada data ormawa. <a href="{{ route('ormawa.create') }}">Tambah sekarang</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($ormawa->hasPages())
    <div class="card-footer bg-white d-flex justify-content-end">
        {{ $ormawa->links() }}
    </div>
    @endif
</div>
@endsection
