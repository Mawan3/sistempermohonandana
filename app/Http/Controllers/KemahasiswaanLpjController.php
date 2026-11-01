<?php

namespace App\Http\Controllers;

use App\Models\Lpj;
use App\Models\Riwayat;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;

class KemahasiswaanLpjController extends Controller
{
    /**
     * Tampilkan daftar LPJ yang masuk untuk PJ LPJ Kemahasiswaan
     */
    public function index(Request $request)
    {
        $query = Lpj::with(['proposal.ormawa', 'user', 'buktiKegiatan'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $lpjs = $query->paginate(10);

        return view('kemahasiswaan.review_lpj.index', compact('lpjs'));
    }

    /**
     * Tampilkan detail LPJ untuk di-review beserta bukti kegiatan & kuitansi
     */
    public function show(Lpj $lpj)
    {
        $lpj->load(['proposal.ormawa', 'proposal.pencairan', 'user', 'reviewer', 'buktiKegiatan']);
        return view('kemahasiswaan.review_lpj.show', compact('lpj'));
    }

    /**
     * ACC / Setujui LPJ oleh PJ LPJ
     */
    public function acc(Lpj $lpj)
    {
        $pjUser = auth()->user() ?? User::where('role', 'pj_lpj')->first();

        $lpj->update([
            'status'           => 'disetujui',
            'reviewer_id'      => $pjUser?->id,
            'tanggal_review'   => now(),
            'catatan_reviewer' => null,
        ]);

        // Update status proposal menjadi selesai
        $lpj->proposal->update([
            'status' => 'selesai',
        ]);

        // Catat ke Riwayat Aktivitas
        Riwayat::create([
            'proposal_id' => $lpj->proposal_id,
            'ormawa_id'   => $lpj->proposal->ormawa_id,
            'user_id'     => $pjUser?->id,
            'periode_id'  => $lpj->proposal->periode_id,
            'aktivitas'   => 'LPJ Disetujui PJ LPJ',
            'keterangan'  => 'Laporan pertanggungjawaban (LPJ) kegiatan "' . $lpj->proposal->judul_kegiatan . '" telah disetujui penuh oleh Penanggung Jawab LPJ Kemahasiswaan. Siklus kegiatan selesai.',
        ]);

        // Buat Notifikasi untuk Pengurus Ormawa
        Notifikasi::create([
            'ormawa_id'   => $lpj->proposal->ormawa_id,
            'proposal_id' => $lpj->proposal_id,
            'judul'       => 'LPJ Berhasil Disetujui!',
            'pesan'       => 'Selamat! LPJ kegiatan "' . $lpj->proposal->judul_kegiatan . '" telah dinyatakan lengkap dan disetujui oleh Penanggung Jawab LPJ.',
            'tipe'        => 'success',
            'is_dibaca'   => false,
        ]);

        return redirect()->route('kemahasiswaan.review-lpj.index')
            ->with('success', 'LPJ proposal "' . $lpj->proposal->judul_kegiatan . '" berhasil disetujui.');
    }

    /**
     * Berikan Catatan Perbaikan LPJ
     */
    public function perbaikan(Request $request, Lpj $lpj)
    {
        $request->validate([
            'catatan_reviewer' => 'required|string',
        ], [
            'catatan_reviewer.required' => 'Catatan perbaikan LPJ wajib diisi.',
        ]);

        $pjUser = auth()->user() ?? User::where('role', 'pj_lpj')->first();

        $lpj->update([
            'status'           => 'perlu_perbaikan',
            'reviewer_id'      => $pjUser?->id,
            'tanggal_review'   => now(),
            'catatan_reviewer' => $request->catatan_reviewer,
        ]);

        // Catat ke Riwayat Aktivitas
        Riwayat::create([
            'proposal_id' => $lpj->proposal_id,
            'ormawa_id'   => $lpj->proposal->ormawa_id,
            'user_id'     => $pjUser?->id,
            'periode_id'  => $lpj->proposal->periode_id,
            'aktivitas'   => 'Permintaan Perbaikan LPJ',
            'keterangan'  => 'PJ LPJ meminta perbaikan berkas pertanggungjawaban. Catatan: ' . $request->catatan_reviewer,
        ]);

        // Notifikasi ke Ormawa
        Notifikasi::create([
            'ormawa_id'   => $lpj->proposal->ormawa_id,
            'proposal_id' => $lpj->proposal_id,
            'judul'       => 'LPJ Membutuhkan Perbaikan',
            'pesan'       => 'PJ LPJ Kemahasiswaan meminta perbaikan berkas LPJ "' . $lpj->proposal->judul_kegiatan . '". Silakan baca catatan evaluasi.',
            'tipe'        => 'warning',
            'is_dibaca'   => false,
        ]);

        return redirect()->route('kemahasiswaan.review-lpj.index')
            ->with('success', 'Catatan perbaikan LPJ berhasil dikirimkan ke pengurus Ormawa.');
    }
}
