{{-- resources/views/keuangan/validasi/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Validasi Anggaran: ' . $proposal->judul_kegiatan)
@section('page-title', 'Validasi Anggaran & Pencairan Dana')

@section('content')

{{-- Tombol Kembali --}}
<div class="mb-3">
    <a href="{{ route('keuangan.validasi.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Validasi Keuangan
    </a>
</div>

<div class="row g-4">
    {{-- Kolom Kiri: Detail Proposal & Analisis Saldo Plafon --}}
    <div class="col-lg-7">
        {{-- Card Plafon Ormawa --}}
        <div class="card shadow-sm border-0 mb-4 bg-light">
            <div class="card-body p-3">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-bank me-2 text-success"></i>Status Plafon Anggaran Ormawa</h6>
                <div class="row g-2 text-center">
                    <div class="col-4">
                        <div class="p-2 bg-white rounded-2 border">
                            <small class="text-muted d-block">Plafon Periode</small>
                            <span class="fw-bold text-dark">Rp {{ number_format($plafon ?? 7000000, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-white rounded-2 border">
                            <small class="text-muted d-block">Telah Dicairkan</small>
                            <span class="fw-bold text-danger">Rp {{ number_format($totalCair ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="p-2 bg-white rounded-2 border">
                            <small class="text-muted d-block">Sisa Plafon</small>
                            <span class="fw-bold text-success">Rp {{ number_format($sisaPlafon ?? 7000000, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Proposal --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-primary"><i class="bi bi-file-earmark-text me-2"></i>Informasi Proposal Kegiatan</h6>
                <span class="badge bg-{{ $proposal->badge_status }} fs-6 px-3 py-2">{{ $proposal->label_status }}</span>
            </div>
            <div class="card-body">
                <h4 class="fw-bold text-dark mb-3">{{ $proposal->judul_kegiatan }}</h4>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Ormawa Pengaju</small>
                        <span class="fw-semibold text-dark">{{ $proposal->ormawa->nama_ormawa ?? '-' }} ({{ $proposal->ormawa->singkatan ?? '-' }})</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Nominal yang Diajukan</small>
                        <span class="fs-5 fw-bold text-primary">Rp {{ number_format($proposal->anggaran_diajukan, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Tanggal Pelaksanaan</small>
                        <span class="fw-semibold text-dark">{{ $proposal->tanggal_kegiatan?->format('d F Y') ?? '-' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Lokasi Kegiatan</small>
                        <span class="fw-semibold text-dark">{{ $proposal->lokasi ?? '-' }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold text-dark">Deskripsi Kegiatan:</h6>
                    <div class="p-3 bg-light rounded-3 text-secondary" style="white-space: pre-line;">
                        {{ $proposal->deskripsi }}
                    </div>
                </div>

                {{-- Berkas Proposal --}}
                @if($proposal->file_proposal)
                <div class="border-top pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-pdf text-danger fs-3"></i>
                            <div>
                                <span class="fw-semibold text-dark d-block">{{ basename($proposal->file_proposal) }}</span>
                                <small class="text-muted">Berkas Proposal Mahasiswa yang telah di-ACC Kemahasiswaan</small>
                            </div>
                        </div>
                        <a href="{{ route('proposals.preview', $proposal) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye me-1"></i> Preview Dokumen
                        </a>
                    </div>
                </div>
                @endif

                {{-- Bukti Transfer Jika Sudah Cair --}}
                @if($proposal->pencairan && $proposal->pencairan->bukti_transfer)
                <div class="border-top pt-3 mt-3">
                    <h6 class="fw-bold text-success mb-2"><i class="bi bi-receipt me-1"></i>Bukti Transfer Pencairan Dana:</h6>
                    <div class="p-3 border border-success border-opacity-25 rounded-3 bg-success bg-opacity-10 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold text-dark d-block">Dana Dicairkan: Rp {{ number_format($proposal->pencairan->jumlah_dicairkan, 0, ',', '.') }}</span>
                            <small class="text-muted">Tanggal: {{ $proposal->pencairan->tanggal_pencairan?->format('d M Y') ?? '-' }} &bull; Diproses oleh Bagian Keuangan</small>
                        </div>
                        <a href="{{ route('keuangan.validasi.bukti-transfer', $proposal) }}" target="_blank" class="btn btn-success btn-sm">
                            <i class="bi bi-eye me-1"></i> Buka Bukti Slip
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Panel Pencairan & Upload Bukti Transfer --}}
    <div class="col-lg-5">
        @if($proposal->status === 'disetujui_kemahasiswaan')
            {{-- Form Pencairan Dana & Upload Bukti Transfer --}}
            <div class="card shadow-sm border-0 border-top border-success border-4 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold text-success mb-0"><i class="bi bi-cash-coin me-2"></i>Form Pencairan Dana Kegiatan</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('keuangan.validasi.cairkan', $proposal) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        {{-- Jumlah Dicairkan --}}
                        <div class="mb-3">
                            <label for="jumlah_dicairkan" class="form-label fw-semibold small">Jumlah Dana Dicairkan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="jumlah_dicairkan" id="jumlah_dicairkan" class="form-control @error('jumlah_dicairkan') is-invalid @enderror" value="{{ old('jumlah_dicairkan', $proposal->anggaran_diajukan) }}" max="{{ min(7000000, $sisaPlafon ?? 7000000) }}" required>
                            </div>
                            <small class="text-muted">Maksimal dapat dicairkan: Rp {{ number_format(min(7000000, $sisaPlafon ?? 7000000), 0, ',', '.') }}</small>
                            @error('jumlah_dicairkan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Upload Bukti Transfer (Fitur Wajib Baru) --}}
                        <div class="mb-3">
                            <label for="bukti_transfer" class="form-label fw-semibold small">Unggah Bukti Transfer / Slip Pembayaran <span class="text-danger">*</span></label>
                            <input type="file" name="bukti_transfer" id="bukti_transfer" class="form-control @error('bukti_transfer') is-invalid @enderror" accept=".pdf,image/*" required>
                            <small class="text-muted">Format: PDF atau Gambar (JPG, PNG). Maksimal 5 MB.</small>
                            @error('bukti_transfer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Catatan Keuangan --}}
                        <div class="mb-4">
                            <label for="catatan" class="form-label fw-semibold small">Catatan Pembayaran (Opsional)</label>
                            <textarea name="catatan" id="catatan" rows="3" class="form-control form-control-sm" placeholder="Contoh: Transfer via BSI ke Rekening Ormawa No. Rek 7123456789 a.n Bendahara HIMA.">{{ old('catatan') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-semibold py-2">
                            <i class="bi bi-check2-circle me-1"></i> Konfirmasi & Cairkan Dana
                        </button>
                    </form>
                </div>
            </div>

            {{-- Form Tolak Anggaran --}}
            <div class="card shadow-sm border-0 border-top border-danger border-4">
                <div class="card-body p-3">
                    <h6 class="fw-bold text-danger mb-2"><i class="bi bi-x-circle me-1"></i>Tolak Pengajuan Anggaran</h6>
                    <form action="{{ route('keuangan.validasi.tolak', $proposal) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menolak pengajuan dana ini?');">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <textarea name="catatan" rows="2" class="form-control form-control-sm @error('catatan') is-invalid @enderror" placeholder="Alasan penolakan pencairan anggaran..." required></textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                            <i class="bi bi-x-octagon me-1"></i> Tolak Pengajuan
                        </button>
                    </form>
                </div>
            </div>

        @elseif($proposal->status === 'dana_cair')
            <div class="alert alert-success border-success border-opacity-25 shadow-sm p-4 text-center rounded-3">
                <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                <h5 class="fw-bold text-dark">Dana Telah Berhasil Dicairkan</h5>
                <p class="text-muted small mb-3">Dana sebesar Rp {{ number_format($proposal->pencairan->jumlah_dicairkan ?? $proposal->anggaran_diajukan, 0, ',', '.') }} telah ditransfer dan bukti transfer telah diterbitkan.</p>
                @if($proposal->pencairan && $proposal->pencairan->bukti_transfer)
                    <a href="{{ route('keuangan.validasi.bukti-transfer', $proposal) }}" target="_blank" class="btn btn-success btn-sm">
                        <i class="bi bi-receipt me-1"></i> Unduh Slip Bukti Transfer
                    </a>
                @endif
            </div>
        @else
            <div class="alert alert-secondary py-3 text-center">
                Status proposal ini adalah <strong>{{ $proposal->label_status }}</strong> dan belum dalam tahap pencairan keuangan.
            </div>
        @endif
    </div>
</div>

@endsection
