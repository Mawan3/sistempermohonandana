{{-- resources/views/keuangan/validasi/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Validasi Anggaran & Saldo Ormawa - Bagian Keuangan UM Kudus')
@section('page-title', 'Validasi Anggaran & Pencairan Dana')

@section('content')

{{-- Bagian 1: Ringkasan Saldo & Plafon Tiap Ormawa (Plafon Rp 7.000.000) --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-0 fw-bold text-success"><i class="bi bi-wallet2 me-2"></i>Monitoring Saldo & Plafon Tiap Ormawa</h5>
            <small class="text-muted">Batas maksimal anggaran per Ormawa: <strong>Rp 7.000.000</strong> per periode aktif.</small>
        </div>
        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 fs-6">
            {{ $periodeAktif->nama ?? 'Periode 2026/2027' }}
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Organisasi Mahasiswa (Ormawa)</th>
                        <th>Ketua & Pembina</th>
                        <th class="text-end">Plafon Periode</th>
                        <th class="text-end">Dana Dicairkan</th>
                        <th class="text-end">Sisa Saldo Tersedia</th>
                        <th style="width: 200px;">Status Penyerapan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($saldoOrmawa as $s)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $s['singkatan'] }}</div>
                            <small class="text-muted">{{ $s['nama'] }}</small>
                        </td>
                        <td>
                            @php
                                $ormawaData = \App\Models\Ormawa::find($s['id']);
                            @endphp
                            <small class="d-block text-dark"><i class="bi bi-person"></i> {{ $ormawaData->ketua ?? '-' }}</small>
                            <small class="text-muted"><i class="bi bi-person-badge"></i> {{ $ormawaData->pembina ?? '-' }}</small>
                        </td>
                        <td class="text-end text-muted fw-semibold">Rp {{ number_format($s['plafon'], 0, ',', '.') }}</td>
                        <td class="text-end text-danger fw-bold">Rp {{ number_format($s['terpakai'], 0, ',', '.') }}</td>
                        <td class="text-end text-success fw-bold fs-6">Rp {{ number_format($s['sisa'], 0, ',', '.') }}</td>
                        <td>
                            <div class="d-flex justify-content-between align-items-center small mb-1">
                                <span class="text-muted">{{ $s['persentase'] }}%</span>
                                <span class="badge {{ $s['persentase'] >= 90 ? 'bg-danger' : ($s['persentase'] >= 60 ? 'bg-warning' : 'bg-success') }}" style="font-size:0.65rem;">
                                    {{ $s['persentase'] >= 100 ? 'Penuh' : ($s['persentase'] >= 60 ? 'Mendekati Batas' : 'Aman') }}
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar {{ $s['persentase'] >= 90 ? 'bg-danger' : ($s['persentase'] >= 60 ? 'bg-warning' : 'bg-success') }}" style="width: {{ $s['persentase'] }}%;"></div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">Belum ada data saldo Ormawa</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Bagian 2: Daftar Proposal untuk Divalidasi / Dicairkan --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-cash-stack me-2"></i>Daftar Proposal Menunggu & Riwayat Pencairan</h5>
        
        {{-- Filter Status --}}
        <form action="{{ route('keuangan.validasi.index') }}" method="GET" class="d-flex gap-2">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Status Keuangan</option>
                <option value="disetujui_kemahasiswaan" {{ request('status') === 'disetujui_kemahasiswaan' ? 'selected' : '' }}>Siap Divalidasi / Dicairkan</option>
                <option value="dana_cair" {{ request('status') === 'dana_cair' ? 'selected' : '' }}>Dana Telah Cair</option>
                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak Keuangan</option>
            </select>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Judul Kegiatan</th>
                        <th>Ormawa</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Anggaran Diajukan</th>
                        <th>Status Pencairan</th>
                        <th>Bukti Transfer</th>
                        <th class="text-center" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proposals as $index => $proposal)
                    <tr>
                        <td class="text-center text-muted">{{ $proposals->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $proposal->judul_kegiatan }}</div>
                            <small class="text-muted"><i class="bi bi-geo-alt"></i> {{ $proposal->lokasi ?? 'UM Kudus' }}</small>
                        </td>
                        <td><span class="badge bg-secondary">{{ $proposal->ormawa->singkatan ?? '-' }}</span></td>
                        <td>{{ $proposal->created_at->format('d M Y') }}</td>
                        <td class="fw-bold text-dark">Rp {{ number_format($proposal->anggaran_diajukan, 0, ',', '.') }}</td>
                        <td>
                            @if($proposal->status === 'disetujui_kemahasiswaan')
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass me-1"></i>Siap Dicairkan</span>
                            @elseif($proposal->status === 'dana_cair')
                                <span class="badge bg-success"><i class="bi bi-check2-circle me-1"></i>Dana Cair</span>
                            @elseif($proposal->status === 'ditolak')
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                            @else
                                <span class="badge bg-secondary">{{ $proposal->label_status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($proposal->pencairan && $proposal->pencairan->bukti_transfer)
                                <a href="{{ route('keuangan.validasi.bukti-transfer', $proposal) }}" target="_blank" class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 text-decoration-none">
                                    <i class="bi bi-receipt"></i> Bukti Slip
                                </a>
                            @else
                                <span class="text-muted small">&ndash;</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($proposal->status === 'disetujui_kemahasiswaan')
                                <a href="{{ route('keuangan.validasi.show', $proposal) }}" class="btn btn-sm btn-success">
                                    <i class="bi bi-cash me-1"></i> Proses Cairkan
                                </a>
                            @else
                                <a href="{{ route('keuangan.validasi.show', $proposal) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye me-1"></i> Rincian
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-5">
                            <i class="bi bi-cash-stack fs-1 d-block mb-2 text-muted"></i>
                            Tidak ada proposal yang ditemukan.
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
