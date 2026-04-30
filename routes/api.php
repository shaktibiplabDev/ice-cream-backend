<?php

use App\Http\Controllers\Api\PublicController;
use Illuminate\Support\Facades\Route;

// Handle preflight OPTIONS requests (ADD THIS AT THE TOP)
Route::options('/{any}', function () {
    return response()->json([], 200)
        ->header('Access-Control-Allow-Origin', '*')
        ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN')
        ->header('Access-Control-Allow-Credentials', 'true');
})->where('any', '.*');

// Public API Routes (No authentication required)
Route::prefix('v1')->group(function () {
    Route::get('/distributors', [PublicController::class, 'distributors']);
    Route::post('/inquiries', [PublicController::class, 'submitInquiry']);
});