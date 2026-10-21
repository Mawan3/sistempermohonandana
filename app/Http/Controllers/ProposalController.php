<?php

namespace App\Http\Controllers;

use App\Models\Ormawa;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProposalController extends Controller
{
    /**
     * 5. Halaman index — tampilkan semua proposal
     */
    public function index()
    {
        $proposals = Proposal::with(['ormawa', 'user'])->latest()->paginate(10);
        return view('proposals.index', compact('proposals'));
    }

    /**
     * 6. Tampilkan form create
     */
    public function create()
    {
        $ormawa = Ormawa::where('aktif', true)->orderBy('nama_ormawa')->get();
        return view('proposals.create', compact('ormawa'));
    }

    /**
     * 6. Simpan proposal baru (store)
     */
    public function store(Request $request)
    {
        // 9. Validasi
        $validated = $request->validate([
            'ormawa_id'         => 'required|exists:ormawa,id',
            'judul_kegiatan'    => 'required|string|max:255',
            'deskripsi'         => 'nullable|string',
            'tanggal_kegiatan'  => 'required|date|after_or_equal:today',
            'lokasi'            => 'nullable|string|max:255',
            'anggaran_diajukan' => 'required|numeric|min:0',
        ], [
            'ormawa_id.required'         => 'Ormawa wajib dipilih.',
            'ormawa_id.exists'           => 'Ormawa tidak ditemukan.',
            'judul_kegiatan.required'    => 'Judul kegiatan wajib diisi.',
            'tanggal_kegiatan.required'  => 'Tanggal kegiatan wajib diisi.',
            'tanggal_kegiatan.after_or_equal' => 'Tanggal kegiatan tidak boleh di masa lalu.',
            'anggaran_diajukan.required' => 'Anggaran diajukan wajib diisi.',
            'anggaran_diajukan.numeric'  => 'Anggaran harus berupa angka.',
            'anggaran_diajukan.min'      => 'Anggaran tidak boleh negatif.',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status']  = 'draft';

        Proposal::create($validated);

        // 10. Feedback sukses
        return redirect()->route('proposals.index')
            ->with('success', 'Proposal kegiatan berhasil dibuat.');
    }

    /**
     * Tampilkan detail proposal
     */
    public function show(Proposal $proposal)
    {
        $proposal->load(['ormawa', 'user']);
        return view('proposals.show', compact('proposal'));
    }

    /**
     * 7. Tampilkan form edit
     */
    public function edit(Proposal $proposal)
    {
        $ormawa = Ormawa::where('aktif', true)->orderBy('nama_ormawa')->get();
        return view('proposals.edit', compact('proposal', 'ormawa'));
    }

    /**
     * 7. Update proposal
     */
    public function update(Request $request, Proposal $proposal)
    {
        // 9. Validasi
        $validated = $request->validate([
            'ormawa_id'         => 'required|exists:ormawa,id',
            'judul_kegiatan'    => 'required|string|max:255',
            'deskripsi'         => 'nullable|string',
            'tanggal_kegiatan'  => 'required|date',
            'lokasi'            => 'nullable|string|max:255',
            'anggaran_diajukan' => 'required|numeric|min:0',
        ], [
            'ormawa_id.required'         => 'Ormawa wajib dipilih.',
            'judul_kegiatan.required'    => 'Judul kegiatan wajib diisi.',
            'tanggal_kegiatan.required'  => 'Tanggal kegiatan wajib diisi.',
            'anggaran_diajukan.required' => 'Anggaran diajukan wajib diisi.',
            'anggaran_diajukan.numeric'  => 'Anggaran harus berupa angka.',
        ]);

        $proposal->update($validated);

        // 10. Feedback sukses
        return redirect()->route('proposals.index')
            ->with('success', 'Proposal berhasil diperbarui.');
    }

    /**
     * 8. Hapus proposal
     */
    public function destroy(Proposal $proposal)
    {
        $proposal->delete();

        // 10. Feedback sukses
        return redirect()->route('proposals.index')
            ->with('success', 'Proposal berhasil dihapus.');
    }
}
