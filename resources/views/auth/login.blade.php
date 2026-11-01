<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem &mdash; SI Proma UM Kudus</title>

    {{-- Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            background: linear-gradient(135deg, #0d2137 0%, #1a3a5c 50%, #2a5298 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, sans-serif;
            padding: 2rem 1rem;
        }
        .login-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            max-width: 960px;
            width: 100%;
        }
        .login-banner {
            background: linear-gradient(180deg, #1a3a5c 0%, #0d2137 100%);
            color: #ffffff;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .login-banner .brand-logo {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .login-form-area {
            padding: 3rem 2.5rem;
        }
        .btn-primary {
            background: #1a3a5c;
            border-color: #1a3a5c;
            padding: 0.65rem 1.25rem;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #0d2137;
            border-color: #0d2137;
        }
        .quick-acc-btn {
            font-size: 0.72rem;
            text-align: left;
            padding: 0.35rem 0.6rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s;
        }
        .quick-acc-btn:hover {
            background: #f1f5f9;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="row g-0">
        {{-- Sisi Kiri: Informasi Kampus & Aplikasi --}}
        <div class="col-lg-5 login-banner d-none d-lg-flex">
            <div>
                <div class="brand-logo">
                    <i class="bi bi-mortarboard-fill text-warning"></i>
                </div>
                <h6 class="text-uppercase text-white-50 small fw-bold mb-1" style="letter-spacing: 0.08em;">Universitas Muhammadiyah Kudus</h6>
                <h3 class="fw-bold text-white mb-3">SI Proma</h3>
                <p class="text-white-50 small mb-4" style="line-height: 1.6;">
                    Sistem Informasi Pengajuan Kegiatan dan Anggaran Organisasi Mahasiswa. Mendukung tata kelola administrasi proposal, pencairan dana, dan pertanggungjawaban LPJ secara transparan dan akuntabel.
                </p>

                <div class="d-flex flex-column gap-2 small text-white-50">
                    <div><i class="bi bi-shield-check text-success me-2"></i> Akses Terisolasi Per Organisasi</div>
                    <div><i class="bi bi-check2-circle text-info me-2"></i> Verifikasi Kemahasiswaan & PJ LPJ</div>
                    <div><i class="bi bi-wallet2 text-warning me-2"></i> Monitoring Plafon Keuangan Rp 7.000.000</div>
                </div>
            </div>

            <div class="pt-4 border-top border-white border-opacity-10 text-white-50 small">
                &copy; {{ date('Y') }} Biro Kemahasiswaan & Keuangan UM Kudus
            </div>
        </div>

        {{-- Sisi Kanan: Form Login --}}
        <div class="col-lg-7 login-form-area">
            <div class="d-lg-none mb-3 text-center">
                <h5 class="fw-bold text-primary mb-0">SI Proma UM Kudus</h5>
                <small class="text-muted">Sistem Pengajuan Proposal & LPJ Ormawa</small>
            </div>

            <h4 class="fw-bold text-dark mb-1">Masuk ke Akun Anda</h4>
            <p class="text-muted small mb-4">Silakan masukkan email dan kata sandi untuk mengakses sistem.</p>

            {{-- Alert Notifikasi --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 px-3 small" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show py-2 px-3 small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                {{-- Input Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold text-secondary">Alamat Email Kampus</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" id="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="nama@umkudus.ac.id" required autofocus>
                    </div>
                    @error('email')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Input Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold text-secondary">Kata Sandi</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" placeholder="••••••••" required>
                    </div>
                    @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label small text-muted" for="remember">
                            Ingat sesi saya
                        </label>
                    </div>
                    <span class="small text-muted">Bantuan IT: (0291) 437218</span>
                </div>

                {{-- Tombol Submit --}}
                <button type="submit" class="btn btn-primary w-100 mb-4">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                </button>
            </form>

            {{-- Kotak Bantuan Login Cepat untuk Pengujian / Presentasi --}}
            <div class="border rounded-3 p-3 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-secondary text-uppercase" style="font-size:0.7rem; letter-spacing:0.05em;">
                        <i class="bi bi-key-fill text-warning me-1"></i> Akun Pengujian (Klik untuk Isi Otomatis)
                    </span>
                    <span class="badge bg-secondary" style="font-size:0.65rem;">Password: password</span>
                </div>

                <div class="row g-2">
                    {{-- 1. Kemahasiswaan --}}
                    <div class="col-sm-6">
                        <div class="quick-acc-btn border bg-white" onclick="fillLogin('kemahasiswaan@umkudus.ac.id')">
                            <span class="d-block fw-semibold text-primary">Staf Kemahasiswaan</span>
                            <small class="text-muted">kemahasiswaan@umkudus.ac.id</small>
                        </div>
                    </div>
                    {{-- 2. PJ LPJ --}}
                    <div class="col-sm-6">
                        <div class="quick-acc-btn border bg-white" onclick="fillLogin('pjlpj@umkudus.ac.id')">
                            <span class="d-block fw-semibold" style="color:#654ea3;">Penanggung Jawab LPJ</span>
                            <small class="text-muted">pjlpj@umkudus.ac.id</small>
                        </div>
                    </div>
                    {{-- 3. Keuangan --}}
                    <div class="col-sm-6">
                        <div class="quick-acc-btn border bg-white" onclick="fillLogin('keuangan@umkudus.ac.id')">
                            <span class="d-block fw-semibold text-success">Bagian Keuangan</span>
                            <small class="text-muted">keuangan@umkudus.ac.id</small>
                        </div>
                    </div>
                    {{-- 4. Ormawa Hima Sisfo --}}
                    <div class="col-sm-6">
                        <div class="quick-acc-btn border bg-white" onclick="fillLogin('himasisfo@umkudus.ac.id')">
                            <span class="d-block fw-semibold text-warning text-dark">Pengurus Hima Sisfo</span>
                            <small class="text-muted">himasisfo@umkudus.ac.id</small>
                        </div>
                    </div>
                    {{-- 5. Ormawa Hima Ilkom --}}
                    <div class="col-sm-6">
                        <div class="quick-acc-btn border bg-white" onclick="fillLogin('himailkom@umkudus.ac.id')">
                            <span class="d-block fw-semibold text-warning text-dark">Pengurus Hima Ilkom</span>
                            <small class="text-muted">himailkom@umkudus.ac.id</small>
                        </div>
                    </div>
                    {{-- 6. Administrator --}}
                    <div class="col-sm-6">
                        <div class="quick-acc-btn border bg-white" onclick="fillLogin('admin@umkudus.ac.id')">
                            <span class="d-block fw-semibold text-danger">Administrator</span>
                            <small class="text-muted">admin@umkudus.ac.id</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function fillLogin(email) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = 'password';
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
