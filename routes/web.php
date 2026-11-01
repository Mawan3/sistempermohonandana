<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\LpjController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\KemahasiswaanProposalController;
use App\Http\Controllers\KemahasiswaanLpjController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Pengajuan Kegiatan dan Anggaran (SI Proma)
| Universitas Muhammadiyah Kudus
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. AUTENTIKASI (GUEST ONLY)
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// ==========================================
// 2. APLIKASI (WAJIB LOGIN / AUTHENTICATED)
// ==========================================
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard Utama (Menampilkan POV sesuai role user login)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil Pengguna (redirect ke dashboard)
    Route::get('/profile', function () {
        return redirect()->route('dashboard');
    })->name('profile');

    // =======================================================
    // 3. POV: PENGURUS ORMAWA (Organisasi Mahasiswa)
    //    HANYA role 'pengurus' — admin TIDAK termasuk
    // =======================================================
    Route::middleware('role:pengurus')->group(function () {
        // Proposal CRUD & Aksi
        Route::resource('proposals', ProposalController::class);
        Route::get('/proposals/{proposal}/preview', [ProposalController::class, 'preview'])->name('proposals.preview');
        Route::get('/proposals/{proposal}/download', [ProposalController::class, 'download'])->name('proposals.download');
        Route::get('/proposals/{proposal}/download-koreksi', [ProposalController::class, 'downloadKoreksi'])->name('proposals.download-koreksi');
        Route::post('/proposals/{proposal}/upload-revisi', [ProposalController::class, 'uploadRevisi'])->name('proposals.upload-revisi');

        // Form Upload LPJ di halaman Detail Proposal
        Route::post('/proposals/{proposal}/upload-lpj', [ProposalController::class, 'uploadLpj'])->name('proposals.upload-lpj');

        // Modul Pengajuan LPJ
        Route::prefix('lpj')->name('lpj.')->group(function () {
            Route::get('/', [LpjController::class, 'index'])->name('index');
            Route::get('/create', [LpjController::class, 'create'])->name('create');
            Route::post('/', [LpjController::class, 'store'])->name('store');
            Route::get('/{lpj}', [LpjController::class, 'show'])->name('show');
            Route::get('/{lpj}/download', [LpjController::class, 'download'])->name('download');
        });

        // Notifikasi Ormawa
        Route::prefix('notifikasi')->name('notifikasi.')->group(function () {
            Route::get('/', [NotifikasiController::class, 'index'])->name('index');
            Route::patch('/{notifikasi}/read', [NotifikasiController::class, 'markAsRead'])->name('read');
            Route::post('/read-all', [NotifikasiController::class, 'markAllRead'])->name('read-all');
        });

        // Riwayat 1 Periode Penuh Ormawa
        Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    });

    // =======================================================
    // 4. POV: KEMAHASISWAAN - VERIFIKASI PROPOSAL
    //    HANYA role 'staf_kemahasiswaan' — admin TIDAK termasuk
    // =======================================================
    Route::middleware('role:staf_kemahasiswaan')->prefix('kemahasiswaan/verifikasi')->name('kemahasiswaan.verifikasi.')->group(function () {
        Route::get('/', [KemahasiswaanProposalController::class, 'index'])->name('index');
        Route::get('/{proposal}', [KemahasiswaanProposalController::class, 'show'])->name('show');
        Route::patch('/{proposal}/acc', [KemahasiswaanProposalController::class, 'acc'])->name('acc');
        Route::patch('/{proposal}/revisi', [KemahasiswaanProposalController::class, 'revisi'])->name('revisi');
        Route::get('/{proposal}/preview', [KemahasiswaanProposalController::class, 'preview'])->name('preview');
        Route::get('/{proposal}/download', [KemahasiswaanProposalController::class, 'download'])->name('download');
    });

    // =======================================================
    // 5. POV: KEMAHASISWAAN - PENANGGUNG JAWAB LPJ (PJ LPJ)
    //    HANYA role 'pj_lpj' — admin TIDAK termasuk
    // =======================================================
    Route::middleware('role:pj_lpj')->prefix('kemahasiswaan/review-lpj')->name('kemahasiswaan.review-lpj.')->group(function () {
        Route::get('/', [KemahasiswaanLpjController::class, 'index'])->name('index');
        Route::get('/{lpj}', [KemahasiswaanLpjController::class, 'show'])->name('show');
        Route::patch('/{lpj}/acc', [KemahasiswaanLpjController::class, 'acc'])->name('acc');
        Route::patch('/{lpj}/perbaikan', [KemahasiswaanLpjController::class, 'perbaikan'])->name('perbaikan');
    });

    // =======================================================
    // 6. POV: KEUANGAN - VALIDASI ANGGARAN & SALDO ORMAWA
    //    HANYA role 'staf_keuangan' — admin TIDAK termasuk
    // =======================================================
    Route::middleware('role:staf_keuangan')->prefix('keuangan/validasi')->name('keuangan.validasi.')->group(function () {
        Route::get('/', [KeuanganController::class, 'index'])->name('index');
        Route::get('/{proposal}', [KeuanganController::class, 'show'])->name('show');
        Route::patch('/{proposal}/cairkan', [KeuanganController::class, 'cairkan'])->name('cairkan');
        Route::patch('/{proposal}/tolak', [KeuanganController::class, 'tolak'])->name('tolak');
        Route::get('/{proposal}/bukti-transfer', [KeuanganController::class, 'downloadBuktiTransfer'])->name('bukti-transfer');
    });

    // =======================================================
    // 7. MODUL ADMINISTRATOR
    //    Akses HANYA via URL /administrator atau /admin
    //    HANYA dapat mengelola akun user — tidak ada akses ke modul lain
    // =======================================================
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin', function () {
            return redirect('/administrator');
        });

        Route::prefix('administrator')->group(function () {
            // Redirect root /administrator ke daftar user
            Route::get('/', function () {
                return redirect()->route('users.index');
            });

            // CRUD Akun Pengguna — SATU-SATUNYA yang boleh diakses admin
            Route::resource('users', UserController::class);
        });

        // Blokir akses admin ke route lain: redirect ke halaman manajemen user
        Route::get('/administrator/ormawa', function () {
            return redirect()->route('users.index')
                ->with('error', 'Administrator hanya dapat mengakses manajemen akun pengguna.');
        })->name('ormawa.index');
    });

    Route::get('/periode', function () {
        return redirect()->route('dashboard')->with('info', 'Data periode aktif diatur melalui database master.');
    })->name('periode.index');
});
