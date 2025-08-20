<?php

use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\MedicineController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientManagement\PatientManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
});

// Admin
Route::middleware(['auth', 'role:admin'])->prefix('dashboard')->group(function () {
    Route::resource('district', DistrictController::class)->names('admin.district');
    Route::resource('medicine', MedicineController::class)->names('admin.medicine');
});

// Petugas Pendaftaran
Route::middleware(['auth', 'role:petugas_pendaftaran'])->prefix('dashboard')->group(function () {
    Route::resource('patient', PatientManagementController::class)->names('patient_management.patient');
});

require __DIR__.'/auth.php';
