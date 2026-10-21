@extends('layouts.app')

@section('title', 'Daftar Proposal')
@section('page-title', 'Proposal Kegiatan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0">Kelola pengajuan proposal kegiatan ormawa/ortom.</p>
    <a href="{{ route('proposals.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i> Ajukan Proposal
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-3" style="width:40px">#</th>
                        <th>Judul Kegiatan</th>
                        <th>Ormawa</th>
                        <th>Tanggal</th>
                        <th>Anggaran</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proposals as $i => $proposal)
                    <tr>
                        <td class="ps-3 text-muted">{{ $proposals->firstItem() + $i }}</td>
                        <td>
                            <div class="fw-semibold">{{ $proposal->judul_kegiatan }}</div>
                            <small class="text-muted">{{ $proposal->lokasi ?? '-' }}</small>
                        </td>
                        <td>{{ $proposal->ormawa->nama_ormawa ?? '-' }}</td>
                        <td>{{ $proposal->tanggal_kegiatan?->format('d M Y') }}</td>
                        <td>Rp {{ number_format($proposal->anggaran_diajukan, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge bg-{{ $proposal->badge_status }}">
                                {{ $proposal->label_status }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('proposals.show', $proposal) }}"
                               class="btn btn-outline-secondary btn-sm" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('proposals.edit', $proposal) }}"
                               class="btn btn-outline-primary btn-sm" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('proposals.destroy', $proposal) }}"
                                  method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus proposal ini?')">
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
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                            Belum ada proposal. <a href="{{ route('proposals.create') }}">Ajukan sekarang</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($proposals->hasPages())
    <div class="card-footer bg-white d-flex justify-content-end">
        {{ $proposals->links() }}
    </div>
    @endif
</div>
@endsection
