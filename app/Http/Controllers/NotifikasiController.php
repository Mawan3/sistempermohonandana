<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Ormawa;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Tampilkan seluruh notifikasi untuk Ormawa aktif
     */
    public function index()
    {
        $ormawaId = auth()->user()->ormawa_id ?? session('active_ormawa_id', 1);

        $notifikasis = Notifikasi::with(['proposal.ormawa'])
            ->where(function ($q) use ($ormawaId) {
                $role = session('active_role', auth()->user()->role ?? 'pengurus');
                if ($role === 'pengurus') {
                    $q->where('ormawa_id', $ormawaId)->orWhereNull('ormawa_id');
                }
            })
            ->latest()
            ->paginate(15);

        $unreadCount = Notifikasi::where(function ($q) use ($ormawaId) {
                $role = session('active_role', auth()->user()->role ?? 'pengurus');
                if ($role === 'pengurus') {
                    $q->where('ormawa_id', $ormawaId)->orWhereNull('ormawa_id');
                }
            })
            ->where('is_dibaca', false)
            ->count();

        $ormawaAktif = Ormawa::find($ormawaId) ?? Ormawa::first();

        return view('notifikasi.index', compact('notifikasis', 'unreadCount', 'ormawaAktif'));
    }

    /**
     * Tandai notifikasi tertentu sebagai telah dibaca
     */
    public function markAsRead(Notifikasi $notifikasi)
    {
        $notifikasi->update(['is_dibaca' => true]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifikasi berhasil ditandai sebagai dibaca.');
    }

    /**
     * Tandai semua notifikasi sebagai telah dibaca
     */
    public function markAllRead()
    {
        $ormawaId = auth()->user()->ormawa_id ?? session('active_ormawa_id', 1);

        Notifikasi::where(function ($q) use ($ormawaId) {
                $role = session('active_role', auth()->user()->role ?? 'pengurus');
                if ($role === 'pengurus') {
                    $q->where('ormawa_id', $ormawaId)->orWhereNull('ormawa_id');
                }
            })
            ->where('is_dibaca', false)
            ->update(['is_dibaca' => true]);

        return back()->with('success', 'Semua notifikasi berhasil ditandai telah dibaca.');
    }
}
