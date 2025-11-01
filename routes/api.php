<?php

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

// Fingerprint Sync API - Called by Node.js service
Route::post('/fingerprint/sync', [\App\Http\Controllers\Api\FingerprintSyncController::class, 'sync']);
Route::post('/fingerprint/sync-bulk', [\App\Http\Controllers\Api\FingerprintSyncController::class, 'syncBulk']);