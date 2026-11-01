<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Ormawa;
use App\Models\Periode;
use App\Models\Lpj;
use App\Models\BuktiKegiatan;
use App\Models\Riwayat;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProposalController extends Controller
{
    /**
     * Tampilkan daftar proposal (Dibatasi ketat per Ormawa jika role pengurus)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Proposal::with(['ormawa', 'lpj', 'periode'])->latest();

        // Isolasi Data Ketat: Pengurus Ormawa hanya melihat proposal organisasinya
        if ($user->role === 'pengurus') {
            $query->where('ormawa_id', $user->ormawa_id);
            $ormawaAktif = $user->ormawa;
        } else {
            $ormawaAktif = Ormawa::first();
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $proposals = $query->paginate(10);

        return view('proposals.index', compact('proposals', 'ormawaAktif'));
    }

    /**
     * Form pengajuan proposal baru
     */
    public function create()
    {
        $user = Auth::user();
        $periodeAktif = Periode::where('is_aktif', true)->first();

        // Pengurus Ormawa otomatis terkunci pada organisasinya
        if ($user->role === 'pengurus') {
            $ormawa = $user->ormawa ?? Ormawa::find($user->ormawa_id);
            $ormawaList = collect([$ormawa]);
            $ormawaId = $user->ormawa_id;
        } else {
            $ormawaList = Ormawa::where('aktif', true)->get();
            $ormawa = $ormawaList->first();
            $ormawaId = $ormawa?->id;
        }

        // Hitung sisa pagu
        $totalCair = Proposal::where('ormawa_id', $ormawaId)
            ->whereIn('status', ['dana_cair', 'selesai'])
            ->when($periodeAktif, fn($q) => $q->where('periode_id', $periodeAktif->id))
            ->sum('anggaran_diajukan');

        $plafon = $periodeAktif ? $periodeAktif->batas_anggaran : 7000000;
        $sisaPlafon = max(0, $plafon - $totalCair);

        return view('proposals.create', compact('ormawa', 'periodeAktif', 'ormawaList', 'sisaPlafon'));
    }

    /**
     * Simpan proposal baru
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'judul_kegiatan'    => 'required|string|max:255',
            'deskripsi'         => 'required|string',
            'tanggal_kegiatan'  => 'required|date',
            'lokasi'            => 'required|string|max:255',
            'anggaran_diajukan' => 'required|numeric|min:100000|max:7000000',
            'file_proposal'     => 'required|file|mimes:pdf,doc,docx|max:10240',
        ], [
            'judul_kegiatan.required'    => 'Judul kegiatan wajib diisi.',
            'deskripsi.required'         => 'Deskripsi kegiatan wajib diisi.',
            'tanggal_kegiatan.required'  => 'Tanggal kegiatan wajib diisi.',
            'lokasi.required'            => 'Lokasi kegiatan wajib diisi.',
            'anggaran_diajukan.required' => 'Anggaran yang diajukan wajib diisi.',
            'anggaran_diajukan.max'      => 'Anggaran tidak boleh melebihi plafon Rp 7.000.000 per periode.',
            'file_proposal.required'     => 'File proposal (PDF/Word) wajib diunggah.',
            'file_proposal.mimes'        => 'Format file proposal harus PDF, DOC, atau DOCX.',
            'file_proposal.max'          => 'Ukuran file proposal maksimal 10 MB.',
        ]);

        // Isolasi ketat: ormawa_id dipaksa dari akun pengurus yang login
        if ($user->role === 'pengurus') {
            $ormawaId = $user->ormawa_id;
        } else {
            $ormawaId = $request->ormawa_id ?? Ormawa::first()?->id;
        }

        $periodeAktif = Periode::where('is_aktif', true)->first();

        // Simpan file upload
        $filePath = null;
        if ($request->hasFile('file_proposal')) {
            $filePath = $request->file('file_proposal')->store('proposals', 'public');
        }

        $proposal = Proposal::create([
            'ormawa_id'         => $ormawaId,
            'user_id'           => $user->id,
            'periode_id'        => $periodeAktif?->id,
            'judul_kegiatan'    => $request->judul_kegiatan,
            'deskripsi'         => $request->deskripsi,
            'tanggal_kegiatan'  => $request->tanggal_kegiatan,
            'lokasi'            => $request->lokasi,
            'anggaran_diajukan' => $request->anggaran_diajukan,
            'status'            => 'diajukan',
            'file_proposal'     => $filePath,
        ]);

        // Catat ke Riwayat Aktivitas
        Riwayat::create([
            'proposal_id' => $proposal->id,
            'ormawa_id'   => $ormawaId,
            'user_id'     => $user->id,
            'periode_id'  => $periodeAktif?->id,
            'aktivitas'   => 'Pengajuan Proposal Baru',
            'keterangan'  => 'Mengajukan proposal "' . $proposal->judul_kegiatan . '" dengan anggaran Rp ' . number_format($proposal->anggaran_diajukan, 0, ',', '.') . '.',
        ]);

        // Buat Notifikasi untuk Staf Kemahasiswaan
        Notifikasi::create([
            'ormawa_id'   => $ormawaId,
            'proposal_id' => $proposal->id,
            'judul'       => 'Proposal Baru Masuk',
            'pesan'       => 'Proposal baru "' . $proposal->judul_kegiatan . '" diajukan oleh ' . ($proposal->ormawa->singkatan ?? 'Ormawa') . ' dan siap diverifikasi.',
            'tipe'        => 'info',
            'is_dibaca'   => false,
        ]);

        return redirect()->route('proposals.show', $proposal)
            ->with('success', 'Proposal "' . $proposal->judul_kegiatan . '" berhasil diajukan dan sedang menunggu review Kemahasiswaan.');
    }

    /**
     * Tampilkan detail proposal beserta Form Upload LPJ di bawah Log Aktivitas
     */
    public function show(Proposal $proposal)
    {
        $user = Auth::user();

        // Isolasi Data Ketat: Pengurus Ormawa tidak boleh membuka proposal ormawa lain!
        if ($user->role === 'pengurus' && $proposal->ormawa_id !== $user->ormawa_id) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk melihat proposal milik Ormawa lain.');
        }

        $proposal->load(['ormawa', 'user', 'periode', 'lpj.buktiKegiatan', 'pencairan', 'riwayat.user']);
        return view('proposals.show', compact('proposal'));
    }

    /**
     * Form edit proposal
     */
    public function edit(Proposal $proposal)
    {
        $user = Auth::user();

        if ($user->role === 'pengurus' && $proposal->ormawa_id !== $user->ormawa_id) {
            abort(403, 'Akses Ditolak.');
        }

        $ormawaList = Ormawa::where('aktif', true)->get();
        $periodeAktif = Periode::where('is_aktif', true)->first();

        return view('proposals.edit', compact('proposal', 'ormawaList', 'periodeAktif'));
    }

    /**
     * Update proposal
     */
    public function update(Request $request, Proposal $proposal)
    {
        $user = Auth::user();

        if ($user->role === 'pengurus' && $proposal->ormawa_id !== $user->ormawa_id) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'judul_kegiatan'    => 'required|string|max:255',
            'deskripsi'         => 'required|string',
            'tanggal_kegiatan'  => 'required|date',
            'lokasi'            => 'required|string|max:255',
            'anggaran_diajukan' => 'required|numeric|min:100000|max:7000000',
            'file_proposal'     => 'nullable|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $data = $request->only(['judul_kegiatan', 'deskripsi', 'tanggal_kegiatan', 'lokasi', 'anggaran_diajukan']);

        if ($request->hasFile('file_proposal')) {
            if ($proposal->file_proposal && Storage::disk('public')->exists($proposal->file_proposal)) {
                Storage::disk('public')->delete($proposal->file_proposal);
            }
            $data['file_proposal'] = $request->file('file_proposal')->store('proposals', 'public');
        }

        $proposal->update($data);

        return redirect()->route('proposals.show', $proposal)
            ->with('success', 'Data proposal berhasil diperbarui.');
    }

    /**
     * Hapus proposal
     */
    public function destroy(Proposal $proposal)
    {
        $user = Auth::user();

        if ($user->role === 'pengurus' && $proposal->ormawa_id !== $user->ormawa_id) {
            abort(403, 'Akses Ditolak.');
        }

        if ($proposal->file_proposal && Storage::disk('public')->exists($proposal->file_proposal)) {
            Storage::disk('public')->delete($proposal->file_proposal);
        }
        if ($proposal->file_koreksi && Storage::disk('public')->exists($proposal->file_koreksi)) {
            Storage::disk('public')->delete($proposal->file_koreksi);
        }

        $judul = $proposal->judul_kegiatan;
        $proposal->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Proposal "' . $judul . '" berhasil dihapus.');
    }

    /**
     * Preview File Proposal (PDF inline di browser)
     */
    public function preview(Proposal $proposal)
    {
        $user = Auth::user();
        if ($user->role === 'pengurus' && $proposal->ormawa_id !== $user->ormawa_id) {
            abort(403, 'Akses Ditolak.');
        }

        if (!$proposal->file_proposal || !Storage::disk('public')->exists($proposal->file_proposal)) {
            return back()->with('error', 'File proposal belum diunggah atau tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($proposal->file_proposal);
        $mime = mime_content_type($path);

        return response()->file($path, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    /**
     * Unduh File Proposal
     */
    public function download(Proposal $proposal)
    {
        $user = Auth::user();
        if ($user->role === 'pengurus' && $proposal->ormawa_id !== $user->ormawa_id) {
            abort(403, 'Akses Ditolak.');
        }

        if (!$proposal->file_proposal || !Storage::disk('public')->exists($proposal->file_proposal)) {
            return back()->with('error', 'File proposal tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('public')->download($proposal->file_proposal, 'Proposal_' . str_replace(' ', '_', $proposal->judul_kegiatan) . '.' . pathinfo($proposal->file_proposal, PATHINFO_EXTENSION));
    }

    /**
     * Unduh File Catatan Koreksi dari Kemahasiswaan
     */
    public function downloadKoreksi(Proposal $proposal)
    {
        $user = Auth::user();
        if ($user->role === 'pengurus' && $proposal->ormawa_id !== $user->ormawa_id) {
            abort(403, 'Akses Ditolak.');
        }

        if (!$proposal->file_koreksi || !Storage::disk('public')->exists($proposal->file_koreksi)) {
            return back()->with('error', 'File koreksi dari Kemahasiswaan tidak ditemukan.');
        }

        return Storage::disk('public')->download($proposal->file_koreksi, 'Koreksi_Proposal_' . str_replace(' ', '_', $proposal->judul_kegiatan) . '.' . pathinfo($proposal->file_koreksi, PATHINFO_EXTENSION));
    }

    /**
     * Upload File Proposal Revisi oleh Ormawa
     */
    public function uploadRevisi(Request $request, Proposal $proposal)
    {
        $user = Auth::user();
        if ($user->role === 'pengurus' && $proposal->ormawa_id !== $user->ormawa_id) {
            abort(403, 'Akses Ditolak.');
        }

        $request->validate([
            'file_proposal_revisi' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'catatan_pengurus'     => 'nullable|string',
        ], [
            'file_proposal_revisi.required' => 'File proposal hasil revisi wajib diunggah.',
            'file_proposal_revisi.mimes'    => 'Format file harus PDF, DOC, atau DOCX.',
            'file_proposal_revisi.max'      => 'Ukuran file maksimal 10 MB.',
        ]);

        $path = $request->file('file_proposal_revisi')->store('proposals', 'public');

        $proposal->update([
            'file_proposal' => $path,
            'status'        => 'diajukan',
        ]);

        // Catat ke Riwayat Aktivitas
        Riwayat::create([
            'proposal_id' => $proposal->id,
            'ormawa_id'   => $proposal->ormawa_id,
            'user_id'     => $user->id,
            'periode_id'  => $proposal->periode_id,
            'aktivitas'   => 'Unggah Proposal Hasil Revisi',
            'keterangan'  => 'Ormawa telah mengunggah perbaikan proposal sesuai arahan koreksi Kemahasiswaan.' . ($request->catatan_pengurus ? ' Catatan: ' . $request->catatan_pengurus : ''),
        ]);

        // Notifikasi ke Kemahasiswaan
        Notifikasi::create([
            'ormawa_id'   => $proposal->ormawa_id,
            'proposal_id' => $proposal->id,
            'judul'       => 'Revisi Proposal Masuk',
            'pesan'       => 'Ormawa ' . ($proposal->ormawa->singkatan ?? '') . ' telah mengunggah proposal revisi untuk kegiatan "' . $proposal->judul_kegiatan . '". Silakan verifikasi ulang.',
            'tipe'        => 'info',
            'is_dibaca'   => false,
        ]);

        return redirect()->route('proposals.show', $proposal)
            ->with('success', 'Proposal hasil revisi berhasil diunggah! Status proposal kembali "Menunggu Review Kemahasiswaan".');
    }

    /**
     * FITUR BARU: Upload LPJ Langsung dari Halaman Detail Proposal (Ormawa)
     */
    public function uploadLpj(Request $request, Proposal $proposal)
    {
        $user = Auth::user();

        // Validasi kepemilikan proposal
        if ($user->role === 'pengurus' && $proposal->ormawa_id !== $user->ormawa_id) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mengunggah LPJ pada kegiatan ini.');
        }

        $request->validate([
            'total_realisasi'    => 'required|numeric|min:0',
            'rincian_penggunaan' => 'required|string',
            'file_lpj'           => 'required|file|mimes:pdf,doc,docx|max:10240',
            'bukti_foto.*'       => 'nullable|image|max:5120',
            'bukti_nota.*'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'total_realisasi.required'    => 'Total realisasi dana wajib diisi.',
            'rincian_penggunaan.required' => 'Rincian pertanggungjawaban penggunaan dana wajib diisi.',
            'file_lpj.required'           => 'Berkas laporan LPJ (PDF/Word) wajib diunggah.',
            'file_lpj.mimes'              => 'Format berkas LPJ harus PDF atau Word.',
        ]);

        // Simpan file dokumen LPJ utama
        $fileLpjPath = $request->file('file_lpj')->store('bukti_lpj', 'public');

        // Buat atau update LPJ
        $lpj = Lpj::updateOrCreate(
            ['proposal_id' => $proposal->id],
            [
                'user_id'            => $user->id,
                'rincian_penggunaan' => $request->rincian_penggunaan,
                'total_realisasi'    => $request->total_realisasi,
                'status'             => 'menunggu_review',
                'tanggal_kirim'      => now(),
                'file_lpj'           => $fileLpjPath,
                'catatan_reviewer'   => null,
            ]
        );

        // Upload lampiran foto kegiatan
        if ($request->hasFile('bukti_foto')) {
            foreach ($request->file('bukti_foto') as $foto) {
                $path = $foto->store('bukti_lpj', 'public');
                BuktiKegiatan::create([
                    'lpj_id'    => $lpj->id,
                    'jenis'     => 'foto',
                    'nama_file' => $foto->getClientOriginalName(),
                    'path_file' => $path,
                ]);
            }
        }

        // Upload lampiran nota / kuitansi
        if ($request->hasFile('bukti_nota')) {
            foreach ($request->file('bukti_nota') as $nota) {
                $path = $nota->store('bukti_lpj', 'public');
                BuktiKegiatan::create([
                    'lpj_id'    => $lpj->id,
                    'jenis'     => 'nota',
                    'nama_file' => $nota->getClientOriginalName(),
                    'path_file' => $path,
                ]);
            }
        }

        // Catat ke Riwayat Aktivitas
        Riwayat::create([
            'proposal_id' => $proposal->id,
            'ormawa_id'   => $proposal->ormawa_id,
            'user_id'     => $user->id,
            'periode_id'  => $proposal->periode_id,
            'aktivitas'   => 'Pengajuan LPJ Kegiatan',
            'keterangan'  => 'Ormawa telah mengunggah Laporan Pertanggungjawaban (LPJ) beserta bukti foto kegiatan dan kuitansi belanja untuk kegiatan "' . $proposal->judul_kegiatan . '". Total realisasi: Rp ' . number_format($request->total_realisasi, 0, ',', '.') . '.',
        ]);

        // Notifikasi ke PJ LPJ
        Notifikasi::create([
            'ormawa_id'   => $proposal->ormawa_id,
            'proposal_id' => $proposal->id,
            'judul'       => 'LPJ Baru Diajukan',
            'pesan'       => 'Pengurus ' . ($proposal->ormawa->singkatan ?? 'Ormawa') . ' telah mengunggah LPJ untuk kegiatan "' . $proposal->judul_kegiatan . '". Silakan periksa kelengkapan SPJ dan lampirannya.',
            'tipe'        => 'info',
            'is_dibaca'   => false,
        ]);

        return redirect()->route('proposals.show', $proposal)
            ->with('success', 'Laporan Pertanggungjawaban (LPJ) beserta bukti foto dan nota transaksi berhasil diunggah! Berkas sekarang dalam antrean review PJ LPJ.');
    }
}
