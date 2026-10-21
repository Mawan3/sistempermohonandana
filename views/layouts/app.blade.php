<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Pengajuan Proposal') — UM Kudus</title>

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1a3a5c 0%, #0d2137 100%);
            color: #fff;
            width: 240px;
            position: fixed;
            top: 0;
            left: 0;
        }
        .sidebar .brand {
            padding: 1.2rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar .brand h6 {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.55);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 0;
        }
        .sidebar .brand h5 {
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            margin: 0.2rem 0 0;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 0.6rem 1rem;
            border-radius: 6px;
            margin: 2px 8px;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.9rem;
            transition: background 0.15s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.12);
            color: #fff;
        }
        .sidebar .nav-label {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.4);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1rem 1rem 0.3rem;
        }
        .main-content {
            margin-left: 240px;
            padding: 1.5rem 2rem;
            min-height: 100vh;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.8rem 2rem;
            margin-left: 240px;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar h4 {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 600;
            color: #1a3a5c;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
        }
        .btn-primary {
            background: #1a3a5c;
            border-color: #1a3a5c;
        }
        .btn-primary:hover {
            background: #0d2137;
            border-color: #0d2137;
        }
        .table th {
            background: #f8fafc;
            font-size: 0.82rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar --}}
<div class="sidebar">
    <div class="brand">
        <h6>Universitas Muhammadiyah Kudus</h6>
        <h5>Sistem Proposal Ormawa</h5>
    </div>
    <div class="mt-2">
        <div class="nav-label">Menu Utama</div>
        <a href="{{ route('proposals.index') }}" class="nav-link {{ request()->routeIs('proposals.*') ? 'active' : '' }}">
            <i class="bi bi-file-earmark-text"></i> Proposal Kegiatan
        </a>
        <a href="{{ route('ormawa.index') }}" class="nav-link {{ request()->routeIs('ormawa.*') ? 'active' : '' }}">
            <i class="bi bi-building"></i> Data Ormawa
        </a>
    </div>
</div>

{{-- Topbar --}}
<div class="topbar">
    <h4>@yield('page-title', 'Dashboard')</h4>
    <span class="text-muted small"><i class="bi bi-person-circle me-1"></i>Admin</span>
</div>

{{-- Konten Utama --}}
<div class="main-content">

    {{-- 10. Alert Feedback Sukses/Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-check-circle-fill"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
