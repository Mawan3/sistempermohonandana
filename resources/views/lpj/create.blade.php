{{-- resources/views/lpj/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Form Pengajuan LPJ - SI Proma UM Kudus')
@section('page-title', 'Pengajuan LPJ Kegiatan')

@section('content')

<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-journal-plus me-2"></i>Formulir Laporan Pertanggungjawaban (LPJ)</h5>
                <a href="{{ route('lpj.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar LPJ
                </a>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('lpj.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    {{-- Pilihan Proposal --}}
                    <div class="mb-3">
                        <label for="proposal_id" class="form-label fw-semibold">Pilih Kegiatan / Proposal yang Telah Cair <span class="text-danger">*</span></label>
                        @if(isset($proposal) && $proposal)
                            <input type="hidden" name="proposal_id" value="{{ $proposal->id }}">
                            <div class="p-3 border rounded-3 bg-light">
                                <h6 class="fw-bold mb-1 text-primary">{{ $proposal->judul_kegiatan }}</h6>
                                <div class="small text-muted">
                                    <span>Ormawa: <strong>{{ $proposal->ormawa->nama_ormawa ?? '-' }}</strong></span> &bull; 
                                    <span>Dana Dicairkan: <strong class="text-success">Rp {{ number_format($proposal->anggaran_diajukan, 0, ',', '.') }}</strong></span> &bull;
                                    <span>Tanggal: {{ $proposal->tanggal_kegiatan?->format('d M Y') ?? '-' }}</span>
                                </div>
                            </div>
                        @else
                            <select name="proposal_id" id="proposal_id" class="form-select @error('proposal_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Proposal yang Telah Dicairkan --</option>
                                @foreach($proposalsSiap as $p)
                                    <option value="{{ $p->id }}" {{ old('proposal_id') == $p->id ? 'selected' : '' }}>
                                        {{ $p->judul_kegiatan }} (Cair: Rp {{ number_format($p->anggaran_diajukan, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('proposal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        @endif
                    </div>

                    {{-- Total Realisasi Anggaran --}}
                    <div class="mb-3">
                        <label for="total_realisasi" class="form-label fw-semibold">Total Realisasi Anggaran (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="total_realisasi" id="total_realisasi" class="form-control @error('total_realisasi') is-invalid @enderror" value="{{ old('total_realisasi', $proposal->anggaran_diajukan ?? '') }}" placeholder="0" required min="0">
                        </div>
                        <small class="text-muted">Total aktual pengeluaran dana yang digunakan dalam kegiatan.</small>
                        @error('total_realisasi')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Rincian Penggunaan Dana --}}
                    <div class="mb-3">
                        <label for="rincian_penggunaan" class="form-label fw-semibold">Rincian Penggunaan Dana & Laporan Pelaksanaan <span class="text-danger">*</span></label>
                        <textarea name="rincian_penggunaan" id="rincian_penggunaan" rows="5" class="form-control @error('rincian_penggunaan') is-invalid @enderror" placeholder="Tuliskan rincian pos pengeluaran, ringkasan jalannya kegiatan, dan hasil ketercapaian kegiatan..." required>{{ old('rincian_penggunaan') }}</textarea>
                        @error('rincian_penggunaan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Unggah Berkas LPJ --}}
                    <div class="mb-3">
                        <label for="file_lpj" class="form-label fw-semibold">Unggah Berkas Laporan LPJ Lengkap (PDF/Word) <span class="text-danger">*</span></label>
                        <input type="file" name="file_lpj" id="file_lpj" class="form-control @error('file_lpj') is-invalid @enderror" accept=".pdf,.doc,.docx" required>
                        <small class="text-muted">Format berkas: .pdf, .doc, .docx (Maksimal 10 MB).</small>
                        @error('file_lpj')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-3 mb-4">
                        {{-- Unggah Bukti Foto --}}
                        <div class="col-md-6">
                            <label for="bukti_foto" class="form-label fw-semibold">Lampiran Foto Dokumentasi (Opsional)</label>
                            <input type="file" name="bukti_foto[]" id="bukti_foto" class="form-control @error('bukti_foto.*') is-invalid @enderror" accept="image/*" multiple>
                            <small class="text-muted">Dapat memilih lebih dari 1 foto (JPG, PNG).</small>
                        </div>

                        {{-- Unggah Bukti Nota / Kuitansi --}}
                        <div class="col-md-6">
                            <label for="bukti_nota" class="form-label fw-semibold">Lampiran Nota / Kuitansi Belanja (Opsional)</label>
                            <input type="file" name="bukti_nota[]" id="bukti_nota" class="form-control @error('bukti_nota.*') is-invalid @enderror" accept=".pdf,image/*" multiple>
                            <small class="text-muted">Bukti transaksi, nota, atau invoice pendukung.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="{{ route('lpj.index') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="bi bi-send-check me-1"></i> Ajukan LPJ Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
