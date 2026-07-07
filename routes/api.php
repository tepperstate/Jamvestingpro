<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ScreenshotApiController;
use App\Http\Controllers\Api\SiteSettingsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Screenshot Generator API Endpoint
Route::middleware('auth:sanctum')->any('/screenshot/generate', [ScreenshotApiController::class, 'generate'])->name('api.screenshot.generate');

// Next.js Frontend Routes
Route::get('/settings/global', [SiteSettingsController::class, 'globalSettings']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
Route::post('/logout', [AuthController::class, 'logout']);
