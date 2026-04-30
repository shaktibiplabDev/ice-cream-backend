<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\DistributorController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\InventoryController;

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
        Route::post('/inquiries/{id}/reply', [InquiryController::class, 'reply'])->name('inquiries.reply');
        Route::put('/inquiries/{id}/status', [InquiryController::class, 'updateStatus'])->name('inquiries.update-status');
        Route::delete('/inquiries/{id}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');
        
        // Distributor Management
        Route::get('/distributors', [DistributorController::class, 'index'])->name('distributors.index');
        Route::get('/distributors/create', [DistributorController::class, 'create'])->name('distributors.create');
        Route::post('/distributors', [DistributorController::class, 'store'])->name('distributors.store');
        Route::get('/distributors/{id}', [DistributorController::class, 'show'])->name('distributors.show');
        Route::get('/distributors/{id}/edit', [DistributorController::class, 'edit'])->name('distributors.edit');
        Route::put('/distributors/{id}', [DistributorController::class, 'update'])->name('distributors.update');
        Route::delete('/distributors/{id}', [DistributorController::class, 'destroy'])->name('distributors.destroy');

        // Product Management
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

        // Inventory Management
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
        Route::post('/inventory', [InventoryController::class, 'store'])->name('inventory.store');
        Route::get('/inventory/{id}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
        Route::put('/inventory/{id}', [InventoryController::class, 'update'])->name('inventory.update');
        Route::get('/inventory-history', [InventoryController::class, 'history'])->name('inventory.history');
        Route::get('/low-stock', [InventoryController::class, 'lowStock'])->name('inventory.low-stock');
    });
});

Route::get('/', function () {
    return redirect()->route('admin.login');
});
