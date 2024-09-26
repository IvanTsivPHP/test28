<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\CarModelController;


Route::post('/register', [AuthController::class, 'register'])->middleware('guest:sanctum');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest:sanctum');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api');

Route::get('/brands', [BrandController::class, 'index']);
Route::get('/models', [CarModelController::class, 'index']);

Route::prefix('v1')->group(base_path('routes/api_v1.php'));
Route::prefix('v2')->group(base_path('routes/api_v2.php'));

