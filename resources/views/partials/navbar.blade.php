{{-- resources/views/partials/navbar.blade.php --}}
@php
    $user = auth()->user();
    $role = $user->role ?? 'pengurus';
    $labels = [
        'admin'               => 'Administrator',
        'pengurus'            => 'Pengurus ' . ($user->ormawa->singkatan ?? 'Ormawa'),
        'staf_kemahasiswaan'  => 'Staf Kemahasiswaan',
        'pj_lpj'              => 'PJ LPJ Kemahasiswaan',
        'staf_keuangan'       => 'Bagian Keuangan',
    ];
@endphp

<div class="topbar">
    <div class="d-flex align-items-center gap-2">
        <h4 class="mb-0">@yield('page-title', 'Dashboard')</h4>
    </div>

    {{-- Info Akun Pengguna (Header Bersih Tanpa Role Switcher Pop-up) --}}
    <div class="user-info">
        <div class="text-end d-none d-sm-block">
            <div class="user-name">{{ $user->name ?? 'Pengguna' }}</div>
            <div class="user-role">
                <span class="badge bg-light text-dark border">{{ $labels[$role] ?? ucfirst($role) }}</span>
            </div>
        </div>
        <div class="avatar" title="{{ $user->name ?? 'User' }}">
            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
        </div>
        <form action="{{ route('logout') }}" method="POST" class="mb-0">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Keluar dari Aplikasi">
                <i class="bi bi-box-arrow-right"></i>
            </button>
        </form>
    </div>
</div>
