<?php

namespace App\Http\Controllers;

use App\Models\Lpj;
use App\Models\Proposal;
use App\Models\BuktiKegiatan;
use App\Models\Riwayat;
use App\Models\Notifikasi;
use App\Models\User;
use App\Models\Ormawa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LpjController extends Controller
{
    /**
     * Tampilkan daftar LPJ Ormawa dan Proposal yang siap diajukan LPJ-nya
     */
    public function index()
    {
        $user = Auth::user();

        $query = Lpj::with(['proposal.ormawa', 'buktiKegiatan', 'reviewer'])->latest();

        if ($user->role === 'pengurus') {
            $ormawaId = $user->ormawa_id;
            $query->whereHas('proposal', function ($q) use ($ormawaId) {
                $q->where('ormawa_id', $ormawaId);
            });

            $proposalsSiapLpj = Proposal::where('status', 'dana_cair')
                ->where('ormawa_id', $ormawaId)
                ->whereDoesntHave('lpj')
                ->get();

            $ormawaAktif = $user->ormawa ?? Ormawa::find($ormawaId);
        } else {
            $proposalsSiapLpj = Proposal::where('status', 'dana_cair')
                ->whereDoesntHave('lpj')
                ->get();
            $ormawaAktif = Ormawa::first();
        }

        $lpjs = $query->paginate(10);

        return view('lpj.index', compact('lpjs', 'proposalsSiapLpj', 'ormawaAktif'));
    }

    /**
     * Form pembuatan LPJ untuk proposal yang telah dicairkan
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $proposalId = $request->proposal_id;
        $proposal = null;

        if ($proposalId) {
            $proposal = Proposal::with(['ormawa', 'pencairan'])->findOrFail($proposalId);
            if ($user->role === 'pengurus' && $proposal->ormawa_id !== $user->ormawa_id) {
                abort(403, 'Akses Ditolak: Anda tidak dapat mengakses proposal ormawa lain.');
            }
        }

        $querySiap = Proposal::where('status', 'dana_cair')->whereDoesntHave('lpj');
        if ($user->role === 'pengurus') {
            $querySiap->where('ormawa_id', $user->ormawa_id);
        }
        $proposalsSiap = $querySiap->get();

        return view('lpj.create', compact('proposal', 'proposalsSiap'));
    }

    /**
     * Simpan pengajuan LPJ beserta upload dokumen dan bukti kegiatan
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'proposal_id'        => 'required|exists:proposals,id',
            'rincian_penggunaan' => 'required|string',
            'total_realisasi'    => 'required|numeric|min:0',
            'file_lpj'           => 'required|file|mimes:pdf,doc,docx|max:10240',
            'bukti_foto.*'       => 'nullable|image|max:5120',
            'bukti_nota.*'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'proposal_id.required'        => 'Pilihan proposal kegiatan wajib dipilih.',
            'rincian_penggunaan.required' => 'Rincian pertanggungjawaban penggunaan dana wajib diisi.',
            'total_realisasi.required'    => 'Total realisasi dana wajib diisi.',
            'file_lpj.required'           => 'Berkas laporan LPJ (PDF/Word) wajib diunggah.',
            'file_lpj.mimes'              => 'Format berkas LPJ harus PDF atau Word.',
        ]);

        $proposal = Proposal::findOrFail($request->proposal_id);

        if ($user->role === 'pengurus' && $proposal->ormawa_id !== $user->ormawa_id) {
            abort(403, 'Akses Ditolak: Anda tidak dapat mengunggah LPJ untuk kegiatan ormawa lain.');
        }

        // Upload file dokumen LPJ utama
        $fileLpjPath = $request->file('file_lpj')->store('bukti_lpj', 'public');

        // Buat LPJ
        $lpj = Lpj::create([
            'proposal_id'        => $proposal->id,
            'user_id'            => $user->id,
            'rincian_penggunaan' => $request->rincian_penggunaan,
            'total_realisasi'    => $request->total_realisasi,
            'status'             => 'menunggu_review',
            'tanggal_kirim'      => now(),
            'file_lpj'           => $fileLpjPath,
        ]);

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
            'keterangan'  => 'Ormawa telah mengirimkan Laporan Pertanggungjawaban (LPJ) untuk kegiatan "' . $proposal->judul_kegiatan . '" dengan realisasi Rp ' . number_format($request->total_realisasi, 0, ',', '.') . '.',
        ]);

        // Notifikasi ke PJ LPJ
        Notifikasi::create([
            'ormawa_id'   => $proposal->ormawa_id,
            'proposal_id' => $proposal->id,
            'judul'       => 'LPJ Baru Menunggu Review',
            'pesan'       => 'Pengurus Ormawa telah mengunggah LPJ kegiatan "' . $proposal->judul_kegiatan . '". Silakan periksa kelengkapan SPJ dan lampirannya.',
            'tipe'        => 'info',
            'is_dibaca'   => false,
        ]);

        return redirect()->route('proposals.show', $proposal)
            ->with('success', 'Laporan Pertanggungjawaban (LPJ) berhasil diajukan dan sedang menunggu review PJ LPJ Kemahasiswaan.');
    }

    /**
     * Tampilkan detail LPJ beserta lampiran bukti kegiatan
     */
    public function show(Lpj $lpj)
    {
        $user = Auth::user();

        if ($user->role === 'pengurus' && $lpj->proposal->ormawa_id !== $user->ormawa_id) {
            abort(403, 'Akses Ditolak: Anda tidak dapat melihat LPJ milik ormawa lain.');
        }

        $lpj->load(['proposal.ormawa', 'proposal.pencairan', 'user', 'reviewer', 'buktiKegiatan']);
        return view('lpj.show', compact('lpj'));
    }

    /**
     * Unduh Berkas LPJ
     */
    public function download(Lpj $lpj)
    {
        $user = Auth::user();

        if ($user->role === 'pengurus' && $lpj->proposal->ormawa_id !== $user->ormawa_id) {
            abort(403, 'Akses Ditolak: Anda tidak dapat mengunduh berkas LPJ milik ormawa lain.');
        }

        if (!$lpj->file_lpj || !Storage::disk('public')->exists($lpj->file_lpj)) {
            return back()->with('error', 'Berkas LPJ tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('public')->download($lpj->file_lpj, 'LPJ_' . str_replace(' ', '_', $lpj->proposal->judul_kegiatan) . '.' . pathinfo($lpj->file_lpj, PATHINFO_EXTENSION));
    }
}
