<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use App\Models\Riwayat;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KemahasiswaanProposalController extends Controller
{
    /**
     * Tampilkan daftar proposal masuk untuk Staf Kemahasiswaan
     */
    public function index(Request $request)
    {
        $query = Proposal::with(['ormawa', 'user', 'periode'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default: tampilkan yang diajukan atau perlu revisi
            $query->whereIn('status', ['diajukan', 'perlu_revisi', 'disetujui_kemahasiswaan']);
        }

        $proposals = $query->paginate(10);

        return view('kemahasiswaan.verifikasi.index', compact('proposals'));
    }

    /**
     * Tampilkan detail proposal untuk verifikasi beserta dokumen & riwayat
     */
    public function show(Proposal $proposal)
    {
        $proposal->load(['ormawa', 'user', 'periode', 'riwayat']);
        return view('kemahasiswaan.verifikasi.show', compact('proposal'));
    }

    /**
     * ACC / Setujui Proposal oleh Kemahasiswaan
     */
    public function acc(Proposal $proposal)
    {
        $proposal->update([
            'status'           => 'disetujui_kemahasiswaan',
            'catatan_reviewer' => null,
        ]);

        $kemahasiswaanUser = auth()->user() ?? User::where('role', 'staf_kemahasiswaan')->first();

        // Catat ke Riwayat Aktivitas
        Riwayat::create([
            'proposal_id' => $proposal->id,
            'ormawa_id'   => $proposal->ormawa_id,
            'user_id'     => $kemahasiswaanUser?->id,
            'periode_id'  => $proposal->periode_id,
            'aktivitas'   => 'Disetujui oleh Kemahasiswaan',
            'keterangan'  => 'Proposal kegiatan "' . $proposal->judul_kegiatan . '" telah diverifikasi dan disetujui. Diteruskan ke Bagian Keuangan untuk pencairan anggaran.',
        ]);

        // Buat Notifikasi untuk Pengurus Ormawa
        Notifikasi::create([
            'ormawa_id'   => $proposal->ormawa_id,
            'proposal_id' => $proposal->id,
            'judul'       => 'Proposal Disetujui Kemahasiswaan',
            'pesan'       => 'Selamat! Proposal "' . $proposal->judul_kegiatan . '" telah disetujui oleh Kemahasiswaan dan sedang diajukan ke Bagian Keuangan.',
            'tipe'        => 'success',
            'is_dibaca'   => false,
        ]);

        return redirect()->route('kemahasiswaan.verifikasi.index')
            ->with('success', 'Proposal "' . $proposal->judul_kegiatan . '" berhasil di-ACC / disetujui dan diteruskan ke Bagian Keuangan.');
    }

    /**
     * Berikan Catatan Minta Revisi Proposal beserta Unggah File Koreksi
     */
    public function revisi(Request $request, Proposal $proposal)
    {
        $request->validate([
            'catatan_reviewer' => 'required|string',
            'file_koreksi'     => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ], [
            'catatan_reviewer.required' => 'Catatan revisi wajib diisi agar pengurus Ormawa mengetahui bagian yang harus diperbaiki.',
            'file_koreksi.mimes'        => 'File koreksi harus berupa dokumen PDF, Word, atau gambar.',
            'file_koreksi.max'          => 'Ukuran file koreksi maksimal 10 MB.',
        ]);

        $dataUpdate = [
            'status'           => 'perlu_revisi',
            'catatan_reviewer' => $request->catatan_reviewer,
        ];

        // Jika mengunggah file catatan / lembar koreksi
        if ($request->hasFile('file_koreksi')) {
            if ($proposal->file_koreksi && Storage::disk('public')->exists($proposal->file_koreksi)) {
                Storage::disk('public')->delete($proposal->file_koreksi);
            }
            $dataUpdate['file_koreksi'] = $request->file('file_koreksi')->store('koreksi', 'public');
        }

        $proposal->update($dataUpdate);

        $kemahasiswaanUser = auth()->user() ?? User::where('role', 'staf_kemahasiswaan')->first();

        // Catat ke Riwayat Aktivitas
        Riwayat::create([
            'proposal_id' => $proposal->id,
            'ormawa_id'   => $proposal->ormawa_id,
            'user_id'     => $kemahasiswaanUser?->id,
            'periode_id'  => $proposal->periode_id,
            'aktivitas'   => 'Permintaan Revisi Proposal',
            'keterangan'  => 'Kemahasiswaan meminta perbaikan proposal. Catatan: ' . $request->catatan_reviewer . ($request->hasFile('file_koreksi') ? ' (Dilampirkan berkas koreksi).' : ''),
        ]);

        // Buat Notifikasi untuk Pengurus Ormawa
        Notifikasi::create([
            'ormawa_id'   => $proposal->ormawa_id,
            'proposal_id' => $proposal->id,
            'judul'       => 'Perlu Revisi Proposal',
            'pesan'       => 'Kemahasiswaan meminta perbaikan untuk proposal "' . $proposal->judul_kegiatan . '". Silakan cek catatan reviewer dan file koreksi di sistem.',
            'tipe'        => 'warning',
            'is_dibaca'   => false,
        ]);

        return redirect()->route('kemahasiswaan.verifikasi.index')
            ->with('success', 'Catatan revisi dan berkas koreksi berhasil dikirimkan kepada pengurus ' . ($proposal->ormawa->nama_ormawa ?? 'Ormawa') . '.');
    }

    /**
     * Preview File Proposal Mahasiswa (PDF inline di browser)
     */
    public function preview(Proposal $proposal)
    {
        if (!$proposal->file_proposal || !Storage::disk('public')->exists($proposal->file_proposal)) {
            return back()->with('error', 'File proposal mahasiswa tidak ditemukan.');
        }

        $path = Storage::disk('public')->path($proposal->file_proposal);
        $mime = mime_content_type($path);

        return response()->file($path, [
            'Content-Type'        => $mime,
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }

    /**
     * Unduh File Proposal Mahasiswa
     */
    public function download(Proposal $proposal)
    {
        if (!$proposal->file_proposal || !Storage::disk('public')->exists($proposal->file_proposal)) {
            return back()->with('error', 'File proposal tidak ditemukan di penyimpanan.');
        }

        return Storage::disk('public')->download($proposal->file_proposal, 'Proposal_' . str_replace(' ', '_', $proposal->judul_kegiatan) . '.' . pathinfo($proposal->file_proposal, PATHINFO_EXTENSION));
    }
}
