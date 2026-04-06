<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All admin functionality is now unified in the Web Admin (Blade) system.
| Routes are defined in routes/web.php under the 'admin' prefix.
|
| This file only contains essential API routes for authentication.
|
*/

// User info (Sanctum authentication)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// CSRF cookie for Sanctum
Route::get('/sanctum/csrf-cookie', [\Laravel\Sanctum\Http\Controllers\CsrfCookieController::class, 'show']);
