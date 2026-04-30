<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\DistributorController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MailController;
use App\Http\Controllers\Admin\CronController;

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

        // Warehouse Management
        Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
        Route::get('/warehouses/create', [WarehouseController::class, 'create'])->name('warehouses.create');
        Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
        Route::get('/warehouses/{warehouse}', [WarehouseController::class, 'show'])->name('warehouses.show');
        Route::get('/warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit');
        Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
        Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

        // Territory Map
        Route::get('/map', [MapController::class, 'index'])->name('map.index');

        // Search
        Route::get('/search', [SearchController::class, 'index'])->name('search');

        // Point of Sale (POS)
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
        Route::get('/pos/nearest-warehouse', [PosController::class, 'nearestWarehouse'])->name('pos.nearest-warehouse');
        Route::get('/pos/check-inventory', [PosController::class, 'checkInventory'])->name('pos.check-inventory');
        Route::get('/pos/history', [PosController::class, 'history'])->name('pos.history');
        Route::get('/pos/bill/{sale}', [PosController::class, 'bill'])->name('pos.bill');

        // Settings - Separate Pages
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('/settings/company', [SettingsController::class, 'company'])->name('settings.company');
        Route::get('/settings/tax', [SettingsController::class, 'tax'])->name('settings.tax');
        Route::get('/settings/invoice', [SettingsController::class, 'invoice'])->name('settings.invoice');
        Route::get('/settings/bank', [SettingsController::class, 'bank'])->name('settings.bank');
        Route::get('/settings/email', [SettingsController::class, 'email'])->name('settings.email');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // Categories
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Mail / Email
        Route::get('/mail/inbox', [MailController::class, 'inbox'])->name('mail.inbox');
        Route::get('/mail/sent', [MailController::class, 'sent'])->name('mail.sent');
        Route::get('/mail/drafts', [MailController::class, 'drafts'])->name('mail.drafts');
        Route::get('/mail/starred', [MailController::class, 'starred'])->name('mail.starred');
        Route::get('/mail/compose', [MailController::class, 'compose'])->name('mail.compose');
        Route::post('/mail', [MailController::class, 'store'])->name('mail.store');
        Route::get('/mail/{email}', [MailController::class, 'show'])->name('mail.show');
        Route::delete('/mail/{email}', [MailController::class, 'destroy'])->name('mail.destroy');
        Route::post('/mail/{email}/star', [MailController::class, 'toggleStar'])->name('mail.star');
        Route::post('/mail/{email}/important', [MailController::class, 'toggleImportant'])->name('mail.important');
        Route::get('/mail/{email}/reply', [MailController::class, 'reply'])->name('mail.reply');
        Route::post('/mail/{email}/reply', [MailController::class, 'sendReply'])->name('mail.reply.send');

        // Cron Jobs
        Route::get('/cron', [CronController::class, 'index'])->name('cron.index');
        Route::post('/cron/trigger/{job}', [CronController::class, 'trigger'])->name('cron.trigger');
        Route::get('/cron/status', [CronController::class, 'status'])->name('cron.status');
        Route::get('/cron/regenerate', [CronController::class, 'regenerateToken'])->name('cron.regenerate');
    });
});

Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Public Cron Endpoint (no auth required)
Route::get('/cron/{token}', [CronController::class, 'run'])->name('cron.run');
