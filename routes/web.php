<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\DistributorController;

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Protected Admin Routes
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Inquiry Management
        Route::get('/inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
        Route::get('/inquiries/{id}', [InquiryController::class, 'show'])->name('inquiries.show');
        Route::put('/inquiries/{id}/status', [InquiryController::class, 'updateStatus'])->name('inquiries.update-status');
        Route::delete('/inquiries/{id}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');
        
        // Distributor Management
        Route::get('/distributors', [DistributorController::class, 'index'])->name('distributors.index');
        Route::get('/distributors/create', [DistributorController::class, 'create'])->name('distributors.create');
        Route::post('/distributors', [DistributorController::class, 'store'])->name('distributors.store');
        Route::get('/distributors/{id}', [DistributorController::class, 'show'])->name('distributors.show');  // Add this line
        Route::get('/distributors/{id}/edit', [DistributorController::class, 'edit'])->name('distributors.edit');
        Route::put('/distributors/{id}', [DistributorController::class, 'update'])->name('distributors.update');
        Route::delete('/distributors/{id}', [DistributorController::class, 'destroy'])->name('distributors.destroy');
    });
});

// Welcome page
Route::get('/', function () {
    return view('welcome');
});