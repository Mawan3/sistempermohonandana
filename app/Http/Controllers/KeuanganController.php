<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Pencairan;
use App\Models\Ormawa;
use App\Models\Periode;
use App\Models\Riwayat;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KeuanganController extends Controller
{
    /**
     * Tampilkan daftar proposal siap divalidasi anggarannya & Ringkasan Saldo Ormawa
     */
    public function index(Request $request)
    {
        $periodeAktif = Periode::where('is_aktif', true)->first();

        $query = Proposal::with(['ormawa', 'user', 'periode', 'pencairan'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: tampilkan yang telah disetujui kemahasiswaan atau dana cair
            $query->whereIn('status', ['disetujui_kemahasiswaan', 'dana_cair', 'ditolak']);
        }

        $proposals = $query->paginate(10);

        // Ringkasan Saldo Ormawa untuk Periode Aktif (Plafon Rp 7.000.000)
        $saldoOrmawa = Ormawa::where('aktif', true)->get()->map(function ($ormawa) use ($periodeAktif) {
            $query = Proposal::where('ormawa_id', $ormawa->id)->where('status', 'dana_cair');
            if ($periodeAktif) {
                $query->where('periode_id', $periodeAktif->id);
            }
            $terpakai = $query->sum('anggaran_diajukan');
            $plafon = $periodeAktif ? $periodeAktif->batas_anggaran : 7000000;
            $sisa = max(0, $plafon - $terpakai);
            $persentase = $plafon > 0 ? min(100, round(($terpakai / $plafon) * 100)) : 0;

            return [
                'id'         => $ormawa->id,
                'nama'       => $ormawa->nama_ormawa,
                'singkatan'  => $ormawa->singkatan,
                'plafon'     => $plafon,
                'terpakai'   => $terpakai,
                'sisa'       => $sisa,
                'persentase' => $persentase,
            ];
        });

        return view('keuangan.validasi.index', compact('proposals', 'saldoOrmawa', 'periodeAktif'));
    }

    /**
     * Tampilkan detail validasi anggaran & batas plafon Rp 7.000.000
     */
    public function show(Proposal $proposal)
    {
        $proposal->load(['ormawa', 'user', 'periode', 'pencairan', 'riwayat']);
        $periodeAktif = Periode::where('is_aktif', true)->first();

        // Hitung akumulasi penggunaan dana ormawa ini
        $queryTerpakai = Proposal::where('ormawa_id', $proposal->ormawa_id)->where('status', 'dana_cair');
        if ($periodeAktif) {
            $queryTerpakai->where('periode_id', $periodeAktif->id);
        }
        $totalCair = $queryTerpakai->sum('anggaran_diajukan');
        $plafon = $periodeAktif ? $periodeAktif->batas_anggaran : 7000000;
        $sisaPlafon = max(0, $plafon - $totalCair);

        return view('keuangan.validasi.show', compact('proposal', 'totalCair', 'sisaPlafon', 'plafon', 'periodeAktif'));
    }

    /**
     * Setujui & Cairkan Dana serta Unggah File Bukti Transfer
     */
    public function cairkan(Request $request, Proposal $proposal)
    {
        $request->validate([
            'jumlah_dicairkan' => 'required|numeric|min:100000|max:7000000',
            'catatan'          => 'nullable|string',
            'bukti_transfer'   => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'jumlah_dicairkan.required' => 'Jumlah pencairan dana wajib diisi.',
            'jumlah_dicairkan.numeric'  => 'Jumlah pencairan harus berupa angka nominal.',
            'jumlah_dicairkan.max'      => 'Jumlah pencairan tidak boleh melebihi plafon Rp 7.000.000 per periode.',
            'bukti_transfer.required'   => 'File bukti transfer pencairan dana wajib diunggah.',
            'bukti_transfer.mimes'      => 'Format bukti transfer harus berupa PDF atau gambar (JPG, PNG).',
            'bukti_transfer.max'        => 'Ukuran file bukti transfer maksimal 5 MB.',
        ]);

        // Simpan file bukti transfer
        $buktiPath = null;
        if ($request->hasFile('bukti_transfer')) {
            $buktiPath = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
        }

        $proposal->update([
            'status' => 'dana_cair',
        ]);

        $keuanganUser = auth()->user() ?? User::where('role', 'staf_keuangan')->first();

        // Catat ke tabel pencairan
        Pencairan::updateOrCreate(
            ['proposal_id' => $proposal->id],
            [
                'diproses_oleh'     => $keuanganUser?->id,
                'jumlah_dicairkan'  => $request->jumlah_dicairkan,
                'status'            => 'selesai',
                'tanggal_pencairan' => now()->toDateString(),
                'catatan'           => $request->catatan ?? 'Pencairan dana kegiatan telah disetujui oleh Bagian Keuangan.',
                'bukti_transfer'    => $buktiPath,
            ]
        );

        // Catat ke Riwayat Aktivitas
        Riwayat::create([
            'proposal_id' => $proposal->id,
            'ormawa_id'   => $proposal->ormawa_id,
            'user_id'     => $keuanganUser?->id,
            'periode_id'  => $proposal->periode_id,
            'aktivitas'   => 'Pencairan Dana Berhasil',
            'keterangan'  => 'Bagian Keuangan telah mentransfer dana sebesar Rp ' . number_format($request->jumlah_dicairkan, 0, ',', '.') . ' dan mengunggah slip bukti transfer.',
        ]);

        // Buat Notifikasi untuk Pengurus Ormawa
        Notifikasi::create([
            'ormawa_id'   => $proposal->ormawa_id,
            'proposal_id' => $proposal->id,
            'judul'       => 'Dana Proposal Telah Dicairkan!',
            'pesan'       => 'Kabar baik! Dana proposal "' . $proposal->judul_kegiatan . '" sebesar Rp ' . number_format($request->jumlah_dicairkan, 0, ',', '.') . ' telah dicairkan oleh Keuangan. Bukti transfer telah terbit.',
            'tipe'        => 'success',
            'is_dibaca'   => false,
        ]);

        return redirect()->route('keuangan.validasi.index')
            ->with('success', 'Dana proposal "' . $proposal->judul_kegiatan . '" sebesar Rp ' . number_format($request->jumlah_dicairkan, 0, ',', '.') . ' berhasil dicairkan dan bukti transfer telah diarsipkan.');
    }

    /**
     * Unduh / Lihat Bukti Transfer
     */
    public function downloadBuktiTransfer(Proposal $proposal)
    {
        $pencairan = $proposal->pencairan;
        if (!$pencairan || !$pencairan->bukti_transfer || !Storage::disk('public')->exists($pencairan->bukti_transfer)) {
            return back()->with('error', 'File bukti transfer tidak ditemukan di penyimpanan server.');
        }

        $path = Storage::disk('public')->path($pencairan->bukti_transfer);
        $mime = mime_content_type($path);

        return response()->file($path, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    /**
     * Tolak Proposal oleh Bagian Keuangan
     */
    public function tolak(Request $request, Proposal $proposal)
    {
        $request->validate([
            'catatan' => 'required|string',
        ], [
            'catatan.required' => 'Alasan penolakan anggaran wajib diisi.',
        ]);

        $proposal->update([
            'status'           => 'ditolak',
            'catatan_reviewer' => $request->catatan,
        ]);

        $keuanganUser = auth()->user() ?? User::where('role', 'staf_keuangan')->first();

        // Catat ke Riwayat Aktivitas
        Riwayat::create([
            'proposal_id' => $proposal->id,
            'ormawa_id'   => $proposal->ormawa_id,
            'user_id'     => $keuanganUser?->id,
            'periode_id'  => $proposal->periode_id,
            'aktivitas'   => 'Penolakan Anggaran oleh Keuangan',
            'keterangan'  => 'Bagian Keuangan menolak pencairan proposal. Alasan: ' . $request->catatan,
        ]);

        // Notifikasi ke Ormawa
        Notifikasi::create([
            'ormawa_id'   => $proposal->ormawa_id,
            'proposal_id' => $proposal->id,
            'judul'       => 'Pencairan Dana Ditolak',
            'pesan'       => 'Pengajuan pencairan untuk kegiatan "' . $proposal->judul_kegiatan . '" ditolak oleh Keuangan. Alasan: ' . $request->catatan,
            'tipe'        => 'danger',
            'is_dibaca'   => false,
        ]);

        return redirect()->route('keuangan.validasi.index')
            ->with('error', 'Pencairan dana untuk proposal "' . $proposal->judul_kegiatan . '" telah ditolak.');
    }
}
