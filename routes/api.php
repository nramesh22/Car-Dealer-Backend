<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Admin\CarAdminController;
use App\Http\Controllers\Api\CarController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/cars', [CarController::class, 'index']);
Route::get('/cars/{slug}', [CarController::class, 'show']);

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::post('/cars', [CarAdminController::class, 'store']);
    Route::put('/cars/{car}', [CarAdminController::class, 'update']);
    Route::delete('/cars/{car}', [CarAdminController::class, 'destroy']);
});