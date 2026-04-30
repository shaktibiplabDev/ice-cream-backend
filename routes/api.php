<?php

use App\Http\Controllers\Api\PublicController;
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

    // Public endpoints for frontend
    Route::get('/distributors', [PublicController::class, 'distributors']);
    Route::post('/inquiries', [PublicController::class, 'submitInquiry']);

});