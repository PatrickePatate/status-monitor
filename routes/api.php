<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/metrics/{id}', [\App\Http\Controllers\Api\MetricsController::class, 'metrics'])->name('api.metrics');
Route::get('/services', [\App\Http\Controllers\Api\ServicesController::class, 'services'])->name('api.services');
Route::get('/services/badge/{id}', [\App\Http\Controllers\Api\BadgeController::class, 'badge'])->name('api.services.badge');

Route::middleware('auth:sanctum')->group(function(){

});

