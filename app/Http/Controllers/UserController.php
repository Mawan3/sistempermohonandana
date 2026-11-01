<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ormawa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna pengelola sistem (Admin POV)
     */
    public function index(Request $request)
    {
        $query = User::with('ormawa')->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10);

        // Statistik akun per role (hanya data pengguna — tidak ada data proposal/ormawa operasional)
        $totalUsers    = User::count();
        $totalAdmin    = User::where('role', 'admin')->count();
        $totalKampus   = User::whereIn('role', ['staf_kemahasiswaan', 'pj_lpj', 'staf_keuangan'])->count();
        $totalOrmawa   = User::where('role', 'pengurus')->count();

        return view('admin.users.index', compact('users', 'totalUsers', 'totalAdmin', 'totalKampus', 'totalOrmawa'));
    }

    /**
     * Form penambahan akun pengguna baru
     */
    public function create()
    {
        $ormawaList = Ormawa::where('aktif', true)->get();
        return view('admin.users.create', compact('ormawaList'));
    }

    /**
     * Simpan akun pengguna baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'role'      => 'required|in:admin,staf_kemahasiswaan,pj_lpj,staf_keuangan,pengurus',
            'ormawa_id' => 'nullable|exists:ormawa,id',
        ], [
            'name.required'      => 'Nama lengkap pengguna wajib diisi.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.unique'       => 'Alamat email ini sudah terdaftar dalam sistem.',
            'password.required'  => 'Kata sandi akun wajib diisi.',
            'password.min'       => 'Kata sandi minimal 6 karakter.',
            'role.required'      => 'Peran/Role akun wajib dipilih.',
            'role.in'            => 'Pilihan peran tidak valid.',
            'ormawa_id.exists'   => 'Pilihan Ormawa tidak valid.',
        ]);

        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => $request->role,
            'ormawa_id' => $request->role === 'pengurus' ? $request->ormawa_id : null,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Akun pengguna "' . $request->name . '" berhasil ditambahkan.');
    }

    /**
     * Form edit akun pengguna
     * DIBATASI: Hanya Role Pengguna yang dapat diubah, nama & email readonly
     */
    public function edit(User $user)
    {
        $ormawaList = Ormawa::where('aktif', true)->get();
        return view('admin.users.edit', compact('user', 'ormawaList'));
    }

    /**
     * Perbarui peran/role akun pengguna
     * BATASAN ADMINISTRATOR: Hanya boleh mengubah Role — nama & email tidak dapat diubah
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'role'      => 'required|in:admin,staf_kemahasiswaan,pj_lpj,staf_keuangan,pengurus',
            'ormawa_id' => 'nullable|exists:ormawa,id',
        ], [
            'role.required'    => 'Peran / Role akun wajib dipilih.',
            'role.in'          => 'Pilihan peran tidak valid.',
            'ormawa_id.exists' => 'Pilihan Ormawa tidak valid.',
        ]);

        // Proteksi ketat: HANYA update role dan relasi ormawa — nama & email tidak boleh diubah
        $user->update([
            'role'      => $request->role,
            'ormawa_id' => $request->role === 'pengurus' ? $request->ormawa_id : null,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Peran/Role akun pengguna "' . $user->name . '" berhasil diubah.');
    }

    /**
     * Hapus akun pengguna
     * Tidak bisa menghapus akun yang sedang digunakan (login aktif)
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $nama = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Akun pengguna "' . $nama . '" berhasil dihapus dari sistem.');
    }
}
