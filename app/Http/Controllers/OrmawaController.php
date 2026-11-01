<?php

namespace App\Http\Controllers;

use App\Models\Ormawa;
use Illuminate\Http\Request;

class OrmawaController extends Controller
{
    /**
     * Tampilkan data master ormawa
     */
    public function index()
    {
        $ormawas = Ormawa::all();
        $ormawa = Ormawa::latest()->paginate(10);
        return view('ormawa.index', compact('ormawas', 'ormawa'));
    }

    /**
     * Tampilkan form create
     */
    public function create()
    {
        return view('ormawa.create');
    }

    /**
     * Simpan ormawa baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_ormawa' => 'required|string|max:255',
            'singkatan'   => 'required|string|max:50',
            'ketua'       => 'nullable|string|max:255',
            'pembina'     => 'nullable|string|max:255',
            'jenis'       => 'required|in:ormawa,ortom',
        ]);

        Ormawa::create($request->all());

        return redirect()->route('ormawa.index')->with('success', 'Data Ormawa berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit
     */
    public function edit(Ormawa $ormawa)
    {
        return view('ormawa.edit', compact('ormawa'));
    }

    /**
     * Update data ormawa
     */
    public function update(Request $request, Ormawa $ormawa)
    {
        $request->validate([
            'nama_ormawa' => 'required|string|max:255',
            'singkatan'   => 'required|string|max:50',
            'ketua'       => 'nullable|string|max:255',
            'pembina'     => 'nullable|string|max:255',
            'jenis'       => 'required|in:ormawa,ortom',
        ]);

        $ormawa->update($request->all());

        return redirect()->route('ormawa.index')->with('success', 'Data Ormawa berhasil diperbarui.');
    }

    /**
     * Hapus ormawa
     */
    public function destroy(Ormawa $ormawa)
    {
        $ormawa->delete();
        return redirect()->route('ormawa.index')->with('success', 'Data Ormawa berhasil dihapus.');
    }
}
