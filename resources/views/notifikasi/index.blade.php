{{-- resources/views/notifikasi/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Notifikasi & Pemberitahuan - SI Proma UM Kudus')
@section('page-title', 'Pemberitahuan & Notifikasi')

@section('content')

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="fw-bold mb-1 text-primary"><i class="bi bi-bell me-2"></i>Pusat Pemberitahuan Ormawa</h5>
                <p class="text-muted small mb-0">Informasi verifikasi, koreksi, dan pencairan dana kegiatan untuk {{ $ormawaAktif->nama_ormawa ?? 'Ormawa' }}.</p>
            </div>
            @if($unreadCount > 0)
            <form action="{{ route('notifikasi.read-all') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-check2-all me-1"></i> Tandai Semua Sudah Dibaca
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="list-group list-group-flush">
            @forelse($notifikasis as $notif)
            <div class="list-group-item p-3 {{ !$notif->is_dibaca ? 'bg-light border-start border-4 border-primary' : '' }}">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div class="d-flex gap-3">
                        <div class="mt-1">
                            @if($notif->tipe === 'success')
                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                            @elseif($notif->tipe === 'warning')
                                <i class="bi bi-exclamation-triangle-fill text-warning fs-4"></i>
                            @elseif($notif->tipe === 'danger')
                                <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                            @else
                                <i class="bi bi-info-circle-fill text-primary fs-4"></i>
                            @endif
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2">
                                <h6 class="fw-bold mb-1 text-dark">{{ $notif->judul }}</h6>
                                @if(!$notif->is_dibaca)
                                    <span class="badge bg-primary" style="font-size:0.65rem;">Baru</span>
                                @endif
                            </div>
                            <p class="mb-2 text-secondary">{{ $notif->pesan }}</p>
                            
                            <div class="d-flex gap-3 align-items-center flex-wrap">
                                <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }} ({{ $notif->created_at->format('d M Y H:i') }})</small>
                                
                                @if($notif->proposal)
                                    <a href="{{ route('proposals.show', $notif->proposal) }}" class="small text-decoration-none fw-semibold">
                                        Lihat Proposal Terkait <i class="bi bi-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(!$notif->is_dibaca)
                    <form action="{{ route('notifikasi.read', $notif) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Tandai Sudah Dibaca">
                            <i class="bi bi-check2"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-5">
                <i class="bi bi-bell-slash fs-1 d-block mb-2 text-muted"></i>
                Belum ada notifikasi apapun saat ini.
            </div>
            @endforelse
        </div>
    </div>
    @if($notifikasis->hasPages())
    <div class="card-footer bg-white border-top py-3">
        {{ $notifikasis->links() }}
    </div>
    @endif
</div>

@endsection
