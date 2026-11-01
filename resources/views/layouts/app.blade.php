<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Pengajuan Proposal') &mdash; UM Kudus</title>

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            color: #334155;
        }

        /* ======================== SIDEBAR ======================== */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1a3a5c 0%, #0d2137 100%);
            color: #fff;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            z-index: 200;
            box-shadow: 2px 0 10px rgba(0,0,0,0.08);
        }
        .sidebar .brand {
            padding: 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar .brand h6 {
            font-size: 0.7rem;
            color: rgba(255,255,255,0.55);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin: 0;
        }
        .sidebar .brand h5 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
            margin: 0.25rem 0 0;
            line-height: 1.3;
        }
        .sidebar .role-badge {
            display: inline-block;
            margin-top: 0.45rem;
            font-size: 0.65rem;
            font-weight: 600;
            padding: 0.2rem 0.55rem;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        .role-badge.kemahasiswaan  { background: rgba(59,130,246,0.25);  color: #93c5fd; border: 1px solid rgba(147,197,253,0.3); }
        .role-badge.pj_lpj         { background: rgba(168,85,247,0.25);  color: #d8b4fe; border: 1px solid rgba(216,180,254,0.3); }
        .role-badge.keuangan       { background: rgba(16,185,129,0.25);  color: #6ee7b7; border: 1px solid rgba(110,231,183,0.3); }
        .role-badge.pengurus       { background: rgba(245,158,11,0.25);  color: #fcd34d; border: 1px solid rgba(252,211,77,0.3); }
        .role-badge.admin          { background: rgba(239,68,68,0.25);   color: #fca5a5; border: 1px solid rgba(252,165,165,0.3); }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 0.6rem 1rem;
            border-radius: 6px;
            margin: 2px 8px;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.88rem;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
            font-weight: 600;
        }
        .sidebar .nav-label {
            font-size: 0.68rem;
            color: rgba(255,255,255,0.45);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 1.1rem 1rem 0.3rem;
            font-weight: 600;
        }
        .sidebar-footer {
            margin-top: auto;
            padding: 0.85rem 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255,255,255,0.65);
            font-size: 0.82rem;
            text-decoration: none;
        }
        .sidebar-footer a:hover { color: #fff; }

        /* ======================== NAVBAR / TOPBAR ======================== */
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.75rem 2rem;
            margin-left: 250px;
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
        .topbar .user-info {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }
        .topbar .avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: #1a3a5c;
            color: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.82rem;
            font-weight: 600;
            flex-shrink: 0;
        }
        .topbar .user-name {
            font-size: 0.85rem;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.2;
        }
        .topbar .user-role {
            font-size: 0.72rem;
            color: #64748b;
        }

        /* ======================== MAIN CONTENT ======================== */
        .main-content {
            margin-left: 250px;
            padding: 1.75rem 2rem;
            min-height: calc(100vh - 110px);
        }

        /* ======================== FOOTER ======================== */
        .main-footer {
            margin-left: 250px;
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 0.85rem 2rem;
            font-size: 0.78rem;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* ======================== CARDS & COMPONENTS ======================== */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            background: #fff;
        }
        .stat-card {
            border-radius: 10px;
            padding: 1.25rem 1.4rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 1.1rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }
        .stat-card .icon { font-size: 2.1rem; opacity: 0.88; line-height: 1; }
        .stat-card .label { font-size: 0.72rem; opacity: 0.88; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
        .stat-card .value { font-size: 1.55rem; font-weight: 700; line-height: 1.1; margin-top: 0.2rem; }

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
            font-size: 0.78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Sidebar Component --}}
@include('partials.sidebar')

{{-- Topbar / Navbar Component --}}
@include('partials.navbar')

{{-- Konten Utama --}}
<div class="main-content">
    {{-- Feedback Alerts --}}
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

{{-- Footer Component --}}
@include('partials.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
