<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileApiController;
use App\Http\Controllers\Api\CctvStatusController;

// Public status route for dashboard maps
Route::get('/cctvs/status', [CctvStatusController::class, 'index'])->name('api.cctvs.status');

// Mobile App REST API Routes
Route::prefix('v1')->group(function () {
    // Public Auth Route
    Route::post('/login', [MobileApiController::class, 'login']);

    // Protected Sanctum Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return response()->json([
                'status' => 'success',
                'data' => $request->user(),
            ]);
        });
        Route::get('/cctvs', [MobileApiController::class, 'getCctvList']);
        Route::get('/cctvs/{id}', [MobileApiController::class, 'getCctvDetail']);
        Route::get('/events', [MobileApiController::class, 'getEvents']);
        Route::post('/cctvs/{id}/ptz', [MobileApiController::class, 'controlPtz']);
        Route::post('/logout', [MobileApiController::class, 'logout']);
    });

    // Unauthenticated Fallback for CCTV List during transition/testing if no token provided
    Route::get('/public/cctvs', [MobileApiController::class, 'getCctvList']);
});
