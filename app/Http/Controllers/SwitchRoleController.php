<?php

namespace App\Http\Controllers;

use App\Models\Ormawa;
use Illuminate\Http\Request;

class SwitchRoleController extends Controller
{
    /**
     * Ganti Sudut Pandang (Role POV) untuk Pengujian & Demo Sistem
     */
    public function switchRole(Request $request, string $role)
    {
        $allowedRoles = ['pengurus', 'staf_kemahasiswaan', 'pj_lpj', 'staf_keuangan', 'admin'];

        if (in_array($role, $allowedRoles)) {
            session(['active_role' => $role]);

            // Jika berganti ke pengurus dan belum ada ormawa yang dipilih, set default Hima Sisfo
            if ($role === 'pengurus' && !session()->has('active_ormawa_id')) {
                $himaSisfo = Ormawa::where('singkatan', 'Hima Sisfo')->first() ?? Ormawa::first();
                session(['active_ormawa_id' => $himaSisfo?->id ?? 1]);
            }
        }

        return redirect()->route('dashboard', ['role' => $role])
            ->with('success', 'Sudut pandang (POV) beralih ke: ' . ucfirst(str_replace('_', ' ', $role)));
    }

    /**
     * Ganti Ormawa Aktif untuk POV Pengurus
     */
    public function switchOrmawa(Request $request, int $id)
    {
        $ormawa = Ormawa::findOrFail($id);
        session(['active_ormawa_id' => $ormawa->id]);
        session(['active_role' => 'pengurus']);

        return redirect()->route('dashboard')
            ->with('success', 'Akun pengurus beralih ke: ' . $ormawa->nama_ormawa);
    }
}
