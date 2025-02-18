<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Container\Attributes\Auth;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TenantController;

// Route::controller(AuthController::class)->group(function () {
//     Route::post('/register', 'register')->name('register');
//     Route::post('/login', 'login')->name('login');
// });

// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//     Route::apiResource('tenants', TenantController::class);

//     Route::get('/user', function (Request $request) {
//         return response()->json($request->user());
//     });
// });

// Central Routes (Only for central domains)
foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {

        // Authentication Routes
        Route::controller(AuthController::class)->group(function () {
            Route::post('/register', 'register')->name('register');
            Route::post('/login', 'login')->name('login');
        });

        // Routes for authenticated users
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

            Route::apiResource('tenants', TenantController::class);

            Route::get('/user', function (Request $request) {
                return response()->json($request->user());
            });
        });

    });
}

