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

Route::prefix('/book')->group(function () {
    Route::post('/', [App\Http\Controllers\Api\BookController::class, 'index']);
    Route::put('/', [App\Http\Controllers\Api\BookController::class, 'store']);
    Route::get('/{id}', [App\Http\Controllers\Api\BookController::class, 'show']);
    Route::post('/{id}', [App\Http\Controllers\Api\BookController::class, 'update']);
    Route::delete('/{id}', [App\Http\Controllers\Api\BookController::class, 'destroy']);
});
