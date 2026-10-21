@extends('layouts.app')

@section('title', 'Detail Proposal')
@section('page-title', 'Detail Proposal')

@section('content')
<div class="mb-3">
    <a href="{{ route('proposals.index') }}" class="text-decoration-none text-muted small">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke daftar proposal
    </a>
</div>

<div class="card" style="max-width: 720px">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <h5 class="fw-bold mb-0">{{ $proposal->judul_kegiatan }}</h5>
            <span class="badge bg-{{ $proposal->badge_status }} fs-6">{{ $proposal->label_status }}</span>
        </div>

        <table class="table table-borderless small">
            <tr>
                <th class="text-muted" style="width:160px">Ormawa</th>
                <td>{{ $proposal->ormawa->nama_ormawa ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-muted">Diajukan oleh</th>
                <td>{{ $proposal->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-muted">Tanggal Kegiatan</th>
                <td>{{ $proposal->tanggal_kegiatan?->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <th class="text-muted">Lokasi</th>
                <td>{{ $proposal->lokasi ?? '-' }}</td>
            </tr>
            <tr>
                <th class="text-muted">Anggaran Diajukan</th>
                <td>Rp {{ number_format($proposal->anggaran_diajukan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th class="text-muted">Deskripsi</th>
                <td>{{ $proposal->deskripsi ?: '-' }}</td>
            </tr>
            @if($proposal->catatan_reviewer)
            <tr>
                <th class="text-muted">Catatan Reviewer</th>
                <td class="text-warning-emphasis">{{ $proposal->catatan_reviewer }}</td>
            </tr>
            @endif
        </table>

        <div class="d-flex gap-2 mt-3">
            <a href="{{ route('proposals.edit', $proposal) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <form action="{{ route('proposals.destroy', $proposal) }}" method="POST"
                  onsubmit="return confirm('Yakin hapus proposal ini?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-trash me-1"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
