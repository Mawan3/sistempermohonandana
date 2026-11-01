{{-- resources/views/partials/sidebar.blade.php --}}
@php
    $user = auth()->user();
    $role = $user->role ?? 'pengurus';
@endphp

<div class="sidebar">
    {{-- Brand Identitas Kampus UM Kudus --}}
    <div class="brand">
        <h6>Universitas Muhammadiyah Kudus</h6>
        <h5>SI Proma &mdash; Ormawa</h5>
        
        {{-- Badge identitas role akun aktif --}}
        @if($role === 'staf_kemahasiswaan')
            <span class="role-badge kemahasiswaan"><i class="bi bi-patch-check me-1"></i>Staf Kemahasiswaan</span>
        @elseif($role === 'pj_lpj')
            <span class="role-badge pj_lpj"><i class="bi bi-clipboard2-check me-1"></i>Penanggung Jawab LPJ</span>
        @elseif($role === 'staf_keuangan')
            <span class="role-badge keuangan"><i class="bi bi-cash-coin me-1"></i>Bagian Keuangan</span>
        @elseif($role === 'pengurus')
            <span class="role-badge pengurus"><i class="bi bi-mortarboard me-1"></i>{{ $user->ormawa->singkatan ?? 'Pengurus Ormawa' }}</span>
        @elseif($role === 'admin')
            <span class="role-badge admin"><i class="bi bi-shield-lock me-1"></i>Administrator</span>
        @endif
    </div>

    <div class="mt-3 flex-grow-1 overflow-auto">
        
        {{-- ======================================================= --}}
        {{-- MENU UTAMA: DASHBOARD                                  --}}
        {{-- ======================================================= --}}
        <div class="nav-label">Navigasi Utama</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        {{-- ==================================================== --}}
        {{-- 1. POV PENGURUS KAMPUS: KEMAHASISWAAN (VERIFIKASI)   --}}
        {{-- HANYA untuk role staf_kemahasiswaan                  --}}
        {{-- ==================================================== --}}
        @if($role === 'staf_kemahasiswaan')
            <div class="nav-label">Kemahasiswaan</div>
            <a href="{{ route('kemahasiswaan.verifikasi.index') }}" 
               class="nav-link {{ request()->routeIs('kemahasiswaan.verifikasi.*') ? 'active' : '' }}">
                <i class="bi bi-patch-check"></i> Verifikasi Proposal
            </a>
        @endif

        {{-- ==================================================== --}}
        {{-- 2. POV PENGURUS KAMPUS: PENANGGUNG JAWAB LPJ (PJ LPJ) --}}
        {{-- HANYA untuk role pj_lpj                              --}}
        {{-- ==================================================== --}}
        @if($role === 'pj_lpj')
            <div class="nav-label">Penanggung Jawab LPJ</div>
            <a href="{{ route('kemahasiswaan.review-lpj.index') }}" 
               class="nav-link {{ request()->routeIs('kemahasiswaan.review-lpj.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-check"></i> Review LPJ
            </a>
        @endif

        {{-- ==================================================== --}}
        {{-- 3. POV PENGURUS KAMPUS: BAGIAN KEUANGAN              --}}
        {{-- HANYA untuk role staf_keuangan                       --}}
        {{-- ==================================================== --}}
        @if($role === 'staf_keuangan')
            <div class="nav-label">Bagian Keuangan</div>
            <a href="{{ route('keuangan.validasi.index') }}" 
               class="nav-link {{ request()->routeIs('keuangan.validasi.*') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i> Validasi Anggaran & Saldo
            </a>
        @endif

        {{-- ==================================================== --}}
        {{-- 4. POV PENGURUS ORMAWA: RIWAYAT 1 PERIODE PENUH     --}}
        {{-- HANYA untuk role pengurus                            --}}
        {{-- ==================================================== --}}
        @if($role === 'pengurus')
            <div class="nav-label">Laporan & Arsip</div>
            <a href="{{ route('riwayat.index') }}" 
               class="nav-link {{ request()->routeIs('riwayat.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Riwayat 1 Periode
            </a>
        @endif

        {{-- ==================================================== --}}
        {{-- 5. POV ADMINISTRATOR                                  --}}
        {{-- Hanya tampilkan link ke manajemen akun pengguna       --}}
        {{-- TIDAK ada link ke proposal, LPJ, ormawa, atau keuangan --}}
        {{-- ==================================================== --}}
        @if($role === 'admin')
            <div class="nav-label">Manajemen Akun</div>
            <a href="{{ route('users.index') }}" 
               class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Kelola Pengguna
            </a>
        @endif

    </div>

    {{-- Profil Footer di Sidebar --}}
    <div class="sidebar-footer">
        <div class="d-flex align-items-center justify-content-between w-100 px-2 py-1">
            <div class="d-flex align-items-center gap-2 overflow-hidden">
                <i class="bi bi-person-circle fs-5 text-secondary"></i>
                <div class="text-truncate">
                    <span class="d-block fw-semibold text-white small text-truncate" style="max-width: 140px;">{{ $user->name ?? 'Pengguna' }}</span>
                    <span class="text-muted d-block" style="font-size: 0.7rem;">{{ $user->email ?? '' }}</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1" title="Keluar dari Aplikasi">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                </button>
            </form>
        </div>
    </div>
</div>
