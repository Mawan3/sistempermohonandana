<?php

namespace App\Http\Controllers;

use App\Models\Ormawa;
use Illuminate\Http\Request;

class OrmawaController extends Controller
{
    /**
     * 5. Halaman index — tampilkan semua ormawa
     */
    public function index()
    {
        $ormawa = Ormawa::latest()->paginate(10);
        return view('ormawa.index', compact('ormawa'));
    }

    /**
     * 6. Tampilkan form create
     */
    public function create()
    {
        return view('ormawa.create');
    }

    /**
     * 6. Simpan ormawa baru (store) — dengan validasi (9) dan feedback (10)
     */
    public function store(Request $request)
    {
        // 9. Validasi
        $validated = $request->validate([
            'nama_ormawa' => 'required|string|max:255|unique:ormawa,nama_ormawa',
            'singkatan'   => 'nullable|string|max:20',
            'ketua'       => 'nullable|string|max:255',
            'pembina'     => 'nullable|string|max:255',
            'jenis'       => 'required|in:ormawa,ortom',
        ], [
            'nama_ormawa.required' => 'Nama ormawa wajib diisi.',
            'nama_ormawa.unique'   => 'Nama ormawa sudah terdaftar.',
            'jenis.required'       => 'Jenis ormawa wajib dipilih.',
            'jenis.in'             => 'Jenis harus berupa ormawa atau ortom.',
        ]);

        Ormawa::create($validated);

        // 10. Feedback sukses
        return redirect()->route('ormawa.index')
            ->with('success', 'Ormawa berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail ormawa beserta proposalnya
     */
    public function show(Ormawa $ormawa)
    {
        $ormawa->load('proposals');
        return view('ormawa.show', compact('ormawa'));
    }

    /**
     * 7. Tampilkan form edit
     */
    public function edit(Ormawa $ormawa)
    {
        return view('ormawa.edit', compact('ormawa'));
    }

    /**
     * 7. Update ormawa
     */
    public function update(Request $request, Ormawa $ormawa)
    {
        // 9. Validasi (unique kecuali dirinya sendiri)
        $validated = $request->validate([
            'nama_ormawa' => 'required|string|max:255|unique:ormawa,nama_ormawa,' . $ormawa->id,
            'singkatan'   => 'nullable|string|max:20',
            'ketua'       => 'nullable|string|max:255',
            'pembina'     => 'nullable|string|max:255',
            'jenis'       => 'required|in:ormawa,ortom',
            'aktif'       => 'boolean',
        ], [
            'nama_ormawa.required' => 'Nama ormawa wajib diisi.',
            'nama_ormawa.unique'   => 'Nama ormawa sudah terdaftar.',
            'jenis.required'       => 'Jenis ormawa wajib dipilih.',
        ]);

        $validated['aktif'] = $request->has('aktif');
        $ormawa->update($validated);

        // 10. Feedback sukses
        return redirect()->route('ormawa.index')
            ->with('success', 'Data ormawa berhasil diperbarui.');
    }

    /**
     * 8. Hapus ormawa
     */
    public function destroy(Ormawa $ormawa)
    {
        // Cek apakah masih ada proposal terkait
        if ($ormawa->proposals()->exists()) {
            return redirect()->route('ormawa.index')
                ->with('error', 'Ormawa tidak dapat dihapus karena masih memiliki proposal.');
        }

        $ormawa->delete();

        // 10. Feedback sukses
        return redirect()->route('ormawa.index')
            ->with('success', 'Ormawa berhasil dihapus.');
    }
}
