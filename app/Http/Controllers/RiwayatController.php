<?php

namespace App\Http\Controllers;

use App\Models\Riwayat;
use App\Models\Periode;
use App\Models\Ormawa;
use App\Models\Proposal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class RiwayatController extends Controller
{
    /**
     * Tampilkan seluruh riwayat pengajuan proposal Ormawa selama 1 periode penuh yang sedang aktif
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Pencarian periode aktif dengan fallback otomatis jika nama kolom di database berbeda
        $periodeAktif = null;
        if (Schema::hasColumn('periodes', 'is_aktif')) {
            $periodeAktif = Periode::where('is_aktif', true)->first();
        } elseif (Schema::hasColumn('periodes', 'status')) {
            $periodeAktif = Periode::where('status', 'aktif')->first();
        }

        // Jika tidak ada kriteria status yang cocok, ambil periode terbaru
        if (!$periodeAktif) {
            $periodeAktif = Periode::latest()->first();
        }

        $query = Riwayat::with(['proposal.ormawa', 'user', 'periode'])->latest();

        // Filter 1 Periode Aktif Penuh
        if ($periodeAktif) {
            $query->where(function ($q) use ($periodeAktif) {
                $q->where('periode_id', $periodeAktif->id);

                // Pastikan kolom tanggal terisi sebelum di-parse oleh Carbon
                if (!empty($periodeAktif->tanggal_mulai) && !empty($periodeAktif->tanggal_selesai)) {
                    $q->orWhereBetween('created_at', [
                        Carbon::parse($periodeAktif->tanggal_mulai)->startOfDay(),
                        Carbon::parse($periodeAktif->tanggal_selesai)->endOfDay(),
                    ]);
                }
            });
        }

        // Isolasi Ketat: Pengurus Ormawa HANYA bisa melihat riwayat organisasinya
        if ($user->role === 'pengurus') {
            $ormawaId = $user->ormawa_id;
            $query->where('ormawa_id', $ormawaId);
            $ormawaAktif = $user->ormawa ?? Ormawa::find($ormawaId);
        } else {
            if ($request->filled('ormawa_id')) {
                $ormawaId = $request->ormawa_id;
                $query->where('ormawa_id', $ormawaId);
                $ormawaAktif = Ormawa::find($ormawaId);
            } else {
                $ormawaId = null;
                $ormawaAktif = null;
            }
        }

        // Eksekusi Paginasi Data
        $riwayats = $query->paginate(20);

        // Ambil total aktivitas langsung dari paginasi
        $totalAktivitas = $riwayats->total();

        // Statistik Ringkas 1 Periode
        $totalProposalPeriode = Proposal::when($ormawaId, fn($q) => $q->where('ormawa_id', $ormawaId))
            ->when($periodeAktif, fn($q) => $q->where('periode_id', $periodeAktif->id))
            ->count();

        $totalDanaCairPeriode = Proposal::when($ormawaId, fn($q) => $q->where('ormawa_id', $ormawaId))
            ->where('status', 'dana_cair')
            ->when($periodeAktif, fn($q) => $q->where('periode_id', $periodeAktif->id))
            ->sum('anggaran_diajukan');

        $ormawaList = Ormawa::all();

        return view('riwayat.index', compact(
            'riwayats',
            'periodeAktif',
            'ormawaAktif',
            'ormawaList',
            'totalAktivitas',
            'totalProposalPeriode',
            'totalDanaCairPeriode',
            'user'
        ));
    }
}