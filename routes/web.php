<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrmawaController;
use App\Http\Controllers\ProposalController;

// Halaman utama — redirect ke dashboard atau proposals
Route::get('/', function () {
    return redirect()->route('proposals.index');
});

// 4. Resource Routes — memenuhi semua method CRUD (index, create, store, show, edit, update, destroy)
Route::resource('ormawa', OrmawaController::class);
Route::resource('proposals', ProposalController::class);
