{{-- resources/views/proposals/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Detail Proposal: ' . $proposal->judul_kegiatan)
@section('page-title', 'Detail Proposal')

@section('content')

{{-- Tombol Navigasi Kembali --}}
<div class="mb-3 d-flex justify-content-between align-items-center">
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
    </a>

    {{-- Tombol Aksi Sesuai Status --}}
    <div class="d-flex gap-2">
        @if($proposal->status === 'dana_cair' && !$proposal->lpj)
            <a href="#modul-upload-lpj" class="btn btn-success btn-sm">
                <i class="bi bi-journal-arrow-up me-1"></i> Upload LPJ di Bawah
            </a>
        @endif

        @if(in_array($proposal->status, ['draft', 'perlu_revisi']))
            <a href="{{ route('proposals.edit', $proposal) }}" class="btn btn-warning btn-sm text-dark">
                <i class="bi bi-pencil-square me-1"></i> Edit Data
            </a>
        @endif
    </div>
</div>

{{-- Banner Evaluasi Jika Status Perlu Revisi --}}
@if($proposal->status === 'perlu_revisi')
<div class="card border-warning border-opacity-50 bg-warning bg-opacity-10 mb-4 shadow-sm">
    <div class="card-body">
        <div class="d-flex align-items-start gap-3">
            <i class="bi bi-exclamation-triangle-fill text-warning fs-3"></i>
            <div class="flex-grow-1">
                <h6 class="fw-bold text-dark mb-1">Catatan Revisi dari Kemahasiswaan</h6>
                <p class="mb-2 text-dark">{{ $proposal->catatan_reviewer ?? 'Harap lakukan perbaikan sesuai arahan kemahasiswaan.' }}</p>
                
                {{-- Unduh File Catatan Koreksi Jika Ada --}}
                @if($proposal->file_koreksi)
                    <div class="mt-2 mb-3">
                        <span class="small text-muted d-block mb-1">Kemahasiswaan melampirkan berkas koreksi:</span>
                        <a href="{{ route('proposals.download-koreksi', $proposal) }}" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-file-earmark-arrow-down me-1"></i> Unduh File Catatan Koreksi
                        </a>
                    </div>
                @endif

                {{-- Form Unggah Proposal Revisi --}}
                <div class="card border-0 bg-white p-3 rounded-3 mt-3 shadow-sm">
                    <h6 class="fw-semibold text-primary mb-2"><i class="bi bi-upload me-1"></i>Unggah Proposal Hasil Revisi</h6>
                    <form action="{{ route('proposals.upload-revisi', $proposal) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-2 align-items-center">
                            <div class="col-md-6">
                                <input type="file" name="file_proposal_revisi" class="form-control form-control-sm @error('file_proposal_revisi') is-invalid @enderror" required accept=".pdf,.doc,.docx">
                                @error('file_proposal_revisi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="catatan_pengurus" class="form-control form-control-sm" placeholder="Catatan perbaikan (opsional)">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-sm btn-primary w-100">
                                    <i class="bi bi-send me-1"></i> Kirim Revisi
                                </button>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">Format: PDF/Word (Maksimal 10 MB). Setelah diunggah, status akan otomatis kembali "Menunggu Review Kemahasiswaan".</small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row g-4">
    {{-- Kolom Kiri: Informasi Utama & Berkas --}}
    <div class="col-lg-8">
        {{-- Card Detail Proposal --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-primary"><i class="bi bi-info-circle me-2"></i>Informasi Kegiatan</h6>
                <span class="badge bg-{{ $proposal->badge_status }} fs-6 px-3 py-2">{{ $proposal->label_status }}</span>
            </div>
            <div class="card-body">
                <h4 class="fw-bold text-dark mb-3">{{ $proposal->judul_kegiatan }}</h4>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Organisasi Mahasiswa (Ormawa)</small>
                        <span class="fw-semibold text-dark">{{ $proposal->ormawa->nama_ormawa ?? '-' }} ({{ $proposal->ormawa->singkatan ?? '-' }})</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Periode Anggaran</small>
                        <span class="fw-semibold text-dark">{{ $proposal->periode->nama ?? 'Periode Aktif' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Tanggal Pelaksanaan</small>
                        <span class="fw-semibold text-dark"><i class="bi bi-calendar3 me-1"></i>{{ $proposal->tanggal_kegiatan?->format('d F Y') ?? '-' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Lokasi Kegiatan</small>
                        <span class="fw-semibold text-dark"><i class="bi bi-geo-alt me-1"></i>{{ $proposal->lokasi ?? '-' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Anggaran yang Diajukan</small>
                        <span class="fs-5 fw-bold text-success">Rp {{ number_format($proposal->anggaran_diajukan, 0, ',', '.') }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Pengusul / Penanggung Jawab</small>
                        <span class="fw-semibold text-dark">{{ $proposal->user->name ?? 'Pengurus Ormawa' }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold text-dark">Deskripsi & Tujuan Kegiatan:</h6>
                    <div class="p-3 bg-light rounded-3 text-secondary" style="white-space: pre-line;">
                        {{ $proposal->deskripsi }}
                    </div>
                </div>

                {{-- Area Berkas Proposal & Preview --}}
                <div class="border-top pt-3">
                    <h6 class="fw-semibold text-dark mb-3"><i class="bi bi-paperclip me-1"></i>Berkas Proposal:</h6>
                    
                    @if($proposal->file_proposal)
                        <div class="p-3 border rounded-3 bg-light d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-pdf-fill text-danger fs-2"></i>
                                <div>
                                    <div class="fw-semibold text-dark">{{ basename($proposal->file_proposal) }}</div>
                                    <small class="text-muted">Dokumen Proposal Kegiatan Ormawa</small>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                {{-- Tombol Preview Modal/Tab --}}
                                <a href="{{ route('proposals.preview', $proposal) }}" target="_blank" class="btn btn-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i> Preview Proposal (Pop-up/Tab)
                                </a>
                                {{-- Tombol Download --}}
                                <a href="{{ route('proposals.download', $proposal) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-download me-1"></i> Unduh File
                                </a>
                            </div>
                        </div>

                        {{-- Inline PDF Viewer (Jika PDF) --}}
                        @if(Str::endsWith(strtolower($proposal->file_proposal), '.pdf'))
                        <div class="card border mb-3">
                            <div class="card-header bg-white py-2 small fw-semibold text-muted d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-file-text me-1"></i>Pratinjau Dokumen PDF di Halaman:</span>
                                <a href="{{ route('proposals.preview', $proposal) }}" target="_blank" class="small text-decoration-none">Buka Layar Penuh <i class="bi bi-box-arrow-up-right"></i></a>
                            </div>
                            <div class="card-body p-0">
                                <iframe src="{{ route('proposals.preview', $proposal) }}" style="width:100%; height:450px; border:none;" title="Preview Proposal"></iframe>
                            </div>
                        </div>
                        @endif

                    @else
                        <div class="alert alert-secondary py-2 small">
                            <i class="bi bi-exclamation-circle me-1"></i> Belum ada file proposal yang diunggah.
                        </div>
                    @endif
                </div>

                {{-- Berkas Koreksi dari Kemahasiswaan --}}
                @if($proposal->file_koreksi)
                <div class="border-top pt-3 mt-3">
                    <h6 class="fw-semibold text-danger mb-2"><i class="bi bi-file-earmark-diff me-1"></i>Berkas Koreksi dari Kemahasiswaan:</h6>
                    <div class="p-3 border border-danger border-opacity-25 rounded-3 bg-danger bg-opacity-10 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-text text-danger fs-3"></i>
                            <div>
                                <span class="fw-semibold text-dark d-block">Catatan_Koreksi_{{ $proposal->judul_kegiatan }}</span>
                                <small class="text-muted">Lembar perbaikan yang dikirim oleh Kemahasiswaan</small>
                            </div>
                        </div>
                        <a href="{{ route('proposals.download-koreksi', $proposal) }}" class="btn btn-danger btn-sm">
                            <i class="bi bi-download me-1"></i> Unduh File Koreksi
                        </a>
                    </div>
                </div>
                @endif

                {{-- Bukti Transfer dari Keuangan (Jika Dana Cair) --}}
                @if($proposal->pencairan && $proposal->pencairan->bukti_transfer)
                <div class="border-top pt-3 mt-3">
                    <h6 class="fw-semibold text-success mb-2"><i class="bi bi-receipt me-1"></i>Bukti Transfer Pencairan Dana (Keuangan):</h6>
                    <div class="p-3 border border-success border-opacity-25 rounded-3 bg-success bg-opacity-10 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-credit-card-2-front-fill text-success fs-3"></i>
                            <div>
                                <span class="fw-semibold text-dark d-block">Nominal Dicairkan: Rp {{ number_format($proposal->pencairan->jumlah_dicairkan, 0, ',', '.') }}</span>
                                <small class="text-muted">Tanggal: {{ $proposal->pencairan->tanggal_pencairan?->format('d M Y') ?? '-' }} | Catatan: {{ $proposal->pencairan->catatan ?? '-' }}</small>
                            </div>
                        </div>
                        <a href="{{ route('keuangan.validasi.bukti-transfer', $proposal) }}" target="_blank" class="btn btn-success btn-sm">
                            <i class="bi bi-eye me-1"></i> Lihat Bukti Transfer
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Kolom Kanan: Status Alur, Riwayat Aktivitas & Modul Upload LPJ --}}
    <div class="col-lg-4">
        {{-- Card Status Progres --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-semibold text-dark"><i class="bi bi-diagram-3 me-2"></i>Status Tahapan</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0 position-relative">
                    {{-- 1. Pengajuan --}}
                    <li class="d-flex gap-3 mb-3">
                        <div class="text-success fs-5"><i class="bi bi-check-circle-fill"></i></div>
                        <div>
                            <span class="fw-semibold d-block small">1. Pengajuan Ormawa</span>
                            <small class="text-muted">{{ $proposal->created_at->format('d M Y H:i') }}</small>
                        </div>
                    </li>
                    {{-- 2. Verifikasi Kemahasiswaan --}}
                    <li class="d-flex gap-3 mb-3">
                        @if(in_array($proposal->status, ['disetujui_kemahasiswaan', 'dana_cair', 'selesai']))
                            <div class="text-success fs-5"><i class="bi bi-check-circle-fill"></i></div>
                        @elseif($proposal->status === 'perlu_revisi')
                            <div class="text-warning fs-5"><i class="bi bi-exclamation-circle-fill"></i></div>
                        @else
                            <div class="text-muted fs-5"><i class="bi bi-circle"></i></div>
                        @endif
                        <div>
                            <span class="fw-semibold d-block small">2. Verifikasi Kemahasiswaan</span>
                            <small class="text-muted">
                                @if(in_array($proposal->status, ['disetujui_kemahasiswaan', 'dana_cair', 'selesai']))
                                    Disetujui
                                @elseif($proposal->status === 'perlu_revisi')
                                    Perlu Perbaikan
                                @else
                                    Dalam Antrean
                                @endif
                            </small>
                        </div>
                    </li>
                    {{-- 3. Validasi & Pencairan Keuangan --}}
                    <li class="d-flex gap-3 mb-3">
                        @if(in_array($proposal->status, ['dana_cair', 'selesai']))
                            <div class="text-success fs-5"><i class="bi bi-check-circle-fill"></i></div>
                        @elseif($proposal->status === 'ditolak')
                            <div class="text-danger fs-5"><i class="bi bi-x-circle-fill"></i></div>
                        @else
                            <div class="text-muted fs-5"><i class="bi bi-circle"></i></div>
                        @endif
                        <div>
                            <span class="fw-semibold d-block small">3. Pencairan Dana Keuangan</span>
                            <small class="text-muted">
                                @if(in_array($proposal->status, ['dana_cair', 'selesai']))
                                    Dana Telah Cair
                                @elseif($proposal->status === 'disetujui_kemahasiswaan')
                                    Menunggu Pencairan
                                @else
                                    Belum Diproses
                                @endif
                            </small>
                        </div>
                    </li>
                    {{-- 4. LPJ Kegiatan --}}
                    <li class="d-flex gap-3">
                        @if($proposal->status === 'selesai' || ($proposal->lpj && $proposal->lpj->status === 'disetujui'))
                            <div class="text-success fs-5"><i class="bi bi-check-circle-fill"></i></div>
                        @elseif($proposal->lpj)
                            <div class="text-warning fs-5"><i class="bi bi-hourglass-split"></i></div>
                        @else
                            <div class="text-muted fs-5"><i class="bi bi-circle"></i></div>
                        @endif
                        <div>
                            <span class="fw-semibold d-block small">4. Laporan LPJ (PJ LPJ)</span>
                            <small class="text-muted">
                                @if($proposal->lpj)
                                    {{ $proposal->lpj->label_status }}
                                @else
                                    Belum Ada LPJ
                                @endif
                            </small>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Card Riwayat Aktivitas Proposal Ini (Log Aktivitas) --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="mb-0 fw-semibold text-dark"><i class="bi bi-clock-history me-2"></i>Log Aktivitas</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($proposal->riwayat ?? [] as $rw)
                    <div class="list-group-item py-3">
                        <span class="badge bg-light text-dark border mb-1" style="font-size:0.7rem;">{{ $rw->aktivitas }}</span>
                        <p class="mb-1 small text-secondary">{{ $rw->keterangan }}</p>
                        <small class="text-muted" style="font-size:0.7rem;">{{ $rw->created_at->format('d M Y H:i') }}</small>
                    </div>
                    @empty
                    <div class="list-group-item text-center text-muted py-3 small">
                        Belum ada riwayat aktivitas tercatat
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- MODUL UPLOAD LPJ (DIPOSISIKAN TEPAT DI BAWAH BAGIAN LOG AKTIVITAS)        --}}
        {{-- ========================================================================= --}}
        <div class="card shadow-sm border-0" id="modul-upload-lpj">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold text-primary"><i class="bi bi-journal-arrow-up me-2"></i>Modul LPJ Kegiatan</h6>
                @if($proposal->lpj)
                    <span class="badge bg-{{ $proposal->lpj->badge_status }}">{{ $proposal->lpj->label_status }}</span>
                @endif
            </div>
            <div class="card-body">
                @if($proposal->status === 'dana_cair' || $proposal->lpj || $proposal->status === 'selesai')
                    @if(!$proposal->lpj || $proposal->lpj->status === 'perlu_perbaikan')
                        {{-- Form Upload LPJ Ormawa --}}
                        @if($proposal->lpj && $proposal->lpj->status === 'perlu_perbaikan')
                            <div class="alert alert-warning py-2 small mb-3">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                <strong>Catatan Koreksi PJ LPJ:</strong><br>
                                {{ $proposal->lpj->catatan_reviewer ?? 'Harap lakukan perbaikan dan unggah kembali berkas LPJ yang benar.' }}
                            </div>
                        @else
                            <div class="alert alert-success bg-success bg-opacity-10 border-success border-opacity-25 py-2 small mb-3">
                                <i class="bi bi-cash-coin me-1"></i> Dana kegiatan telah dicairkan! Silakan lengkapi formulir pertanggungjawaban di bawah ini.
                            </div>
                        @endif

                        <form action="{{ route('proposals.upload-lpj', $proposal) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            {{-- Total Realisasi --}}
                            <div class="mb-3">
                                <label for="total_realisasi" class="form-label fw-semibold small">Total Realisasi Anggaran (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="total_realisasi" id="total_realisasi" class="form-control form-control-sm @error('total_realisasi') is-invalid @enderror" 
                                       value="{{ old('total_realisasi', $proposal->lpj->total_realisasi ?? $proposal->anggaran_diajukan) }}" required min="0">
                                <small class="text-muted" style="font-size:0.75rem;">Nominal pencairan: Rp {{ number_format($proposal->pencairan->jumlah_dicairkan ?? $proposal->anggaran_diajukan, 0, ',', '.') }}</small>
                                @error('total_realisasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Rincian Penggunaan --}}
                            <div class="mb-3">
                                <label for="rincian_penggunaan" class="form-label fw-semibold small">Ringkasan Penggunaan Dana <span class="text-danger">*</span></label>
                                <textarea name="rincian_penggunaan" id="rincian_penggunaan" rows="3" class="form-control form-control-sm @error('rincian_penggunaan') is-invalid @enderror" 
                                          placeholder="Uraikan singkat penggunaan dana dan pos pengeluaran..." required>{{ old('rincian_penggunaan', $proposal->lpj->rincian_penggunaan ?? '') }}</textarea>
                                @error('rincian_penggunaan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- File Dokumen LPJ --}}
                            <div class="mb-3">
                                <label for="file_lpj" class="form-label fw-semibold small">Dokumen LPJ (PDF/Word) <span class="text-danger">*</span></label>
                                <input type="file" name="file_lpj" id="file_lpj" class="form-control form-control-sm @error('file_lpj') is-invalid @enderror" required accept=".pdf,.doc,.docx">
                                <small class="text-muted" style="font-size:0.75rem;">Maksimal 10 MB (Format PDF atau DOCX).</small>
                                @error('file_lpj')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Lampiran Bukti Foto Kegiatan --}}
                            <div class="mb-3">
                                <label for="bukti_foto" class="form-label fw-semibold small"><i class="bi bi-camera-fill me-1 text-primary"></i>Bukti Foto Kegiatan (Bisa > 1 file)</label>
                                <input type="file" name="bukti_foto[]" id="bukti_foto" class="form-control form-control-sm @error('bukti_foto.*') is-invalid @enderror" multiple accept="image/*">
                                <small class="text-muted" style="font-size:0.75rem;">Format JPG/PNG dokumentasi jalannya acara.</small>
                                @error('bukti_foto.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Lampiran Nota / Kuitansi Transaksi --}}
                            <div class="mb-3">
                                <label for="bukti_nota" class="form-label fw-semibold small"><i class="bi bi-receipt-cutoff me-1 text-success"></i>Nota / Kuitansi Transaksi (Bisa > 1 file)</label>
                                <input type="file" name="bukti_nota[]" id="bukti_nota" class="form-control form-control-sm @error('bukti_nota.*') is-invalid @enderror" multiple accept=".pdf,image/*">
                                <small class="text-muted" style="font-size:0.75rem;">Scan struk, kuitansi, atau bukti bayar sah.</small>
                                @error('bukti_nota.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-success btn-sm w-100">
                                <i class="bi bi-cloud-arrow-up-fill me-1"></i> Unggah LPJ & Berkas Pendukung
                            </button>
                        </form>

                    @else
                        {{-- LPJ Sudah Terkirim & Menunggu Review / Sudah Disetujui --}}
                        <div class="text-center py-2">
                            <div class="text-success fs-1 mb-2">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <h6 class="fw-bold mb-1">LPJ Telah Diajukan</h6>
                            <p class="text-muted small mb-3">Dikirim: {{ $proposal->lpj->tanggal_kirim?->format('d M Y H:i') ?? '-' }}</p>
                        </div>

                        <div class="p-3 bg-light rounded-3 mb-3 small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Total Realisasi:</span>
                                <span class="fw-bold text-dark">Rp {{ number_format($proposal->lpj->total_realisasi, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">Status:</span>
                                <span class="badge bg-{{ $proposal->lpj->badge_status }}">{{ $proposal->lpj->label_status }}</span>
                            </div>
                            @if($proposal->lpj->catatan_reviewer)
                            <div class="mt-2 pt-2 border-top">
                                <span class="text-muted d-block fw-semibold">Catatan Reviewer:</span>
                                <span class="text-dark">{{ $proposal->lpj->catatan_reviewer }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- Tombol Download Dokumen LPJ --}}
                        @if($proposal->lpj->file_lpj)
                        <a href="{{ route('lpj.download', $proposal->lpj) }}" class="btn btn-outline-primary btn-sm w-100 mb-2">
                            <i class="bi bi-file-earmark-arrow-down me-1"></i> Unduh Dokumen LPJ Utama
                        </a>
                        @endif

                        {{-- Lampiran Bukti Kegiatan & Nota --}}
                        @if($proposal->lpj->buktiKegiatan && $proposal->lpj->buktiKegiatan->count() > 0)
                        <div class="mt-3">
                            <span class="fw-semibold small d-block mb-2">Lampiran Bukti Terunggah:</span>
                            <ul class="list-unstyled small mb-0">
                                @foreach($proposal->lpj->buktiKegiatan as $bk)
                                <li class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                    <span class="text-truncate me-2" style="max-width: 180px;">
                                        <i class="bi bi-{{ $bk->jenis === 'foto' ? 'camera text-primary' : 'receipt text-success' }} me-1"></i>
                                        {{ $bk->nama_file }}
                                    </span>
                                    <span class="badge bg-secondary text-capitalize">{{ $bk->jenis }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    @endif
                @else
                    {{-- Belum Tahap Pencairan Dana --}}
                    <div class="text-center py-3 text-muted">
                        <i class="bi bi-lock-fill fs-2 d-block mb-2 text-secondary opacity-50"></i>
                        <h6 class="small fw-semibold mb-1">Form Belum Dibuka</h6>
                        <p class="small text-muted mb-0">
                            Modul Upload LPJ akan aktif secara otomatis setelah proposal disetujui Kemahasiswaan dan dana kegiatan telah dicairkan oleh Keuangan.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
