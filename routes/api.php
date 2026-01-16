<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ObatController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\KaryawanController;
use App\Http\Controllers\Api\ProfileController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Dashboard tunggal untuk semua role
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // ================= ADMIN ONLY =================
    Route::middleware('role:admin')->group(function () {

        // Route::put('/profile/update', [ProfileController::class, 'update']);
        // MASTER DATA
        Route::apiResource('kategori', KategoriController::class)->except(['show']);
        Route::post('/kategori/delete-batch', [KategoriController::class, 'deleteBatch']);

        Route::apiResource('obat', ObatController::class)->except(['show']);
        Route::post('/obat/delete-batch', [ObatController::class, 'deleteBatch']);
        Route::get('/obat/template', [ObatController::class, 'downloadTemplate']);
        Route::get('/obat/export', [ObatController::class, 'export']);
        Route::post('/obat/import', [ObatController::class, 'import']); 

        // KARYAWAN MANAGEMENT
        Route::ApiResource('karyawan', KaryawanController::class)->except(['show']);
        Route::post('/karyawan/delete-batch', [KaryawanController::class, 'deleteBatch']);

    });

    // ================= ADMIN + KARYAWAN =================
    Route::middleware('role:admin|karyawan')->group(function () {
        // TRANSAKSI
        Route::post('/transaksi', [TransaksiController::class, 'store']);
        Route::apiResource('obat', ObatController::class)->except(['show']);
        
        // LAPORAN
        Route::get('/laporan/harian', [LaporanController::class, 'harian']);
        Route::get('/laporan/mingguan', [LaporanController::class, 'mingguan']);
        Route::get('/laporan/bulanan', [LaporanController::class, 'bulanan']);
        Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel']);
    });
});
