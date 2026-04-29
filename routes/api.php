<?php

use App\Http\Controllers\Api\PublicController;
use Illuminate\Support\Facades\Route;

// Public API Routes (No authentication required)
Route::prefix('v1')->group(function () {
    Route::get('/distributors', [PublicController::class, 'distributors']);
    Route::post('/inquiries', [PublicController::class, 'submitInquiry']);
});