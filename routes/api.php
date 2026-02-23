<?php

use App\Http\Controllers\Api\AndroidController;
use App\Http\Controllers\Api\AudifonoController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BasketController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\IphoneController;
use App\Http\Controllers\Api\SmartWatchController;
use Illuminate\Support\Facades\Route;

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::post('iphones', [IphoneController::class, 'store']);
    Route::put('iphones/{iphone}', [IphoneController::class, 'update']);
    Route::patch('iphones/{iphone}', [IphoneController::class, 'update']);
    Route::delete('iphones/{iphone}', [IphoneController::class, 'destroy']);

    Route::post('smart-watches', [SmartWatchController::class, 'store']);
    Route::put('smart-watches/{smart_watch}', [SmartWatchController::class, 'update']);
    Route::patch('smart-watches/{smart_watch}', [SmartWatchController::class, 'update']);
    Route::delete('smart-watches/{smart_watch}', [SmartWatchController::class, 'destroy']);

    Route::post('androids', [AndroidController::class, 'store']);
    Route::put('androids/{android}', [AndroidController::class, 'update']);
    Route::patch('androids/{android}', [AndroidController::class, 'update']);
    Route::delete('androids/{android}', [AndroidController::class, 'destroy']);

    Route::post('audifonos', [AudifonoController::class, 'store']);
    Route::put('audifonos/{audifono}', [AudifonoController::class, 'update']);
    Route::patch('audifonos/{audifono}', [AudifonoController::class, 'update']);
    Route::delete('audifonos/{audifono}', [AudifonoController::class, 'destroy']);

    Route::post('baskets', [BasketController::class, 'store']);
    Route::put('baskets/{basket}', [BasketController::class, 'update']);
    Route::delete('baskets/{basket}', [BasketController::class, 'destroy']);
    Route::post('baskets/{basket}/items', [BasketController::class, 'addItem']);
    Route::post('baskets/{basket}/charge', [BasketController::class, 'charge']);
});

Route::get('iphones', [IphoneController::class, 'index']);
Route::get('iphones/{iphone}', [IphoneController::class, 'show']);
Route::get('smart-watches', [SmartWatchController::class, 'index']);
Route::get('smart-watches/{smart_watch}', [SmartWatchController::class, 'show']);
Route::get('androids', [AndroidController::class, 'index']);
Route::get('androids/{android}', [AndroidController::class, 'show']);
Route::get('audifonos', [AudifonoController::class, 'index']);
Route::get('audifonos/{audifono}', [AudifonoController::class, 'show']);
Route::get('baskets', [BasketController::class, 'index']);
Route::get('baskets/{basket}', [BasketController::class, 'show']);
Route::get('invoices', [InvoiceController::class, 'index']);
Route::get('invoices/{invoice}', [InvoiceController::class, 'show']);
