<?php

use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\DistributorController;
use App\Http\Controllers\Api\InventoryController;
use Illuminate\Support\Facades\Route;

// Public API Routes (No authentication required)
// HandleCors middleware in bootstrap/app.php handles all CORS preflight
Route::prefix('v1')->group(function () {

    // Health check - test if API is reachable
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'message' => 'API is running',
            'timestamp' => now()->toISOString(),
            'version' => '1.0'
        ]);
    });

    // === DISTRIBUTOR ENDPOINTS (Enhanced) ===
    // Legacy endpoints (kept for backward compatibility)
    Route::get('/distributors/legacy', [PublicController::class, 'distributors']);
    Route::post('/inquiries', [PublicController::class, 'submitInquiry']);

    // Enhanced distributor endpoints with pagination & search
    Route::get('/distributors', [DistributorController::class, 'index']);
    Route::get('/distributors/nearby', [DistributorController::class, 'nearby']);
    Route::get('/distributors/{id}', [DistributorController::class, 'show']);

    // === INVENTORY & PRODUCT ENDPOINTS ===
    // Product catalog
    Route::get('/products', [InventoryController::class, 'products']);
    Route::get('/products/categories', [InventoryController::class, 'categories']);
    Route::get('/products/{id}', [InventoryController::class, 'product']);

    // Inventory at distributors
    Route::get('/distributors/{id}/inventory', [InventoryController::class, 'distributorInventory']);

    // Check product availability at nearby locations
    Route::get('/inventory/check-availability', [InventoryController::class, 'checkAvailability']);

});