<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\ImportKreditController;
use App\Http\Controllers\Master\DivisiController;
use App\Http\Controllers\Master\KantorController;
use App\Http\Controllers\Master\PermissionController;
use App\Http\Controllers\Master\UserController; // ✅ tambahin ini

// Halaman utama
// Route::get('/', function () {
//     return Inertia::render('Welcome');
// })->name('home');
Route::get('/', function () {
    return Inertia::render('auth/Login', [
        'canResetPassword' => Route::has('password.request'),
        'status' => session('status'),
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ----------------- MASTER -----------------
// Gunakan prefix kosong agar URL jadi /kantor, /divisi, dsb
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('kantor', KantorController::class)->middleware(['role:admin']);
    Route::resource('divisi', DivisiController::class)->middleware(['role:admin']);
    Route::resource('roles', RoleController::class)->middleware(['role:admin']);
    Route::resource('permissions', PermissionController::class)->middleware(['role:admin']);
    Route::resource('users', UserController::class)->middleware(['role:admin']); // ✅ tambahin route user

    Route::get('/import-kredit', [ImportKreditController::class, 'index'])->name('import-kredit.index');
    Route::post('/import-kredit', [ImportKreditController::class, 'store'])->name('import-kredit.store');
    Route::get('/import-kredit/template', [ImportKreditController::class, 'downloadTemplate'])->name('import-kredit.template');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
