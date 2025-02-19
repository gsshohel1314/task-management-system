<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Tenant\AuthController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    // Authentication Routes
    Route::controller(AuthController::class)->group(function () {
        Route::post('/register', 'register')->name('tenant.register');
        Route::post('/login', 'login')->name('tenant.login');
    });

    // Routes for authenticated users
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('tenant.logout');

        Route::get('/user', function (Request $request) {
            return response()->json($request->user());
        })->name('tenant.user');
    });

});

