{{-- resources/views/proposals/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Daftar Proposal Kegiatan - SI Proma UM Kudus')
@section('page-title', 'Proposal Kegiatan Ormawa')

@section('content')

{{-- Header Aksi & Filter --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-center justify-content-between">
            <div class="col-md-5">
                <h5 class="fw-bold mb-1 text-primary">Daftar Pengajuan Proposal</h5>
                <p class="text-muted small mb-0">Kelola dan pantau seluruh pengajuan proposal kegiatan {{ $ormawaAktif->nama_ormawa ?? 'Ormawa' }}.</p>
            </div>
            <div class="col-md-7 d-flex justify-content-md-end gap-2 flex-wrap">
                {{-- Filter Status --}}
                <form action="{{ route('proposals.index') }}" method="GET" class="d-flex gap-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Menunggu Review</option>
                        <option value="perlu_revisi" {{ request('status') === 'perlu_revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                        <option value="disetujui_kemahasiswaan" {{ request('status') === 'disetujui_kemahasiswaan' ? 'selected' : '' }}>Disetujui Kemahasiswaan</option>
                        <option value="dana_cair" {{ request('status') === 'dana_cair' ? 'selected' : '' }}>Dana Cair</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </form>

                <a href="{{ route('proposals.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i> Ajukan Proposal Baru
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Data Proposal --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Judul Kegiatan</th>
                        <th>Ormawa</th>
                        <th>Tanggal Pelaksanaan</th>
                        <th>Anggaran</th>
                        <th>Dokumen Berkas</th>
                        <th>Status</th>
                        <th class="text-center" style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proposals as $index => $proposal)
                    <tr>
                        <td class="text-center text-muted">{{ $proposals->firstItem() + $index }}</td>
                        <td>
                            <a href="{{ route('proposals.show', $proposal) }}" class="fw-semibold text-dark text-decoration-none">
                                {{ $proposal->judul_kegiatan }}
                            </a>
                            <small class="d-block text-muted"><i class="bi bi-geo-alt"></i> {{ $proposal->lokasi ?? 'UM Kudus' }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $proposal->ormawa->singkatan ?? '-' }}</span></td>
                        <td>{{ $proposal->tanggal_kegiatan?->format('d M Y') ?? '-' }}</td>
                        <td class="fw-bold text-dark">Rp {{ number_format($proposal->anggaran_diajukan, 0, ',', '.') }}</td>
                        <td>
                            @if($proposal->file_proposal)
                                <a href="{{ route('proposals.preview', $proposal) }}" target="_blank" class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 text-decoration-none">
                                    <i class="bi bi-file-earmark-pdf"></i> Preview PDF
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">Tidak Ada File</span>
                            @endif

                            @if($proposal->file_koreksi)
                                <a href="{{ route('proposals.download-koreksi', $proposal) }}" class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 text-decoration-none d-block mt-1">
                                    <i class="bi bi-file-earmark-arrow-down"></i> File Koreksi
                                </a>
                            @endif
                        </td>
                        <td><span class="badge bg-{{ $proposal->badge_status }}">{{ $proposal->label_status }}</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('proposals.show', $proposal) }}" class="btn btn-outline-primary" title="Lihat Detail">
                                    <i class="bi bi-eye"></i> Detail
                                </a>

                                @if($proposal->status === 'dana_cair' && !$proposal->lpj)
                                    <a href="{{ route('lpj.create', ['proposal_id' => $proposal->id]) }}" class="btn btn-success" title="Ajukan LPJ">
                                        <i class="bi bi-journal-plus"></i> LPJ
                                    </a>
                                @endif

                                @if(in_array($proposal->status, ['draft', 'perlu_revisi']))
                                    <a href="{{ route('proposals.edit', $proposal) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-folder-x fs-1 d-block mb-2 text-muted"></i>
                            Belum ada proposal yang diajukan.
                            <div class="mt-2">
                                <a href="{{ route('proposals.create') }}" class="btn btn-sm btn-primary">Ajukan Proposal Pertama</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($proposals->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $proposals->links() }}
    </div>
    @endif
</div>

@endsection
