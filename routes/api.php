<?php

use App\Http\Controllers\Api\AndroidController;
use App\Http\Controllers\Api\AudifonoController;
use App\Http\Controllers\Api\BasketController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\IphoneController;
use App\Http\Controllers\Api\SmartWatchController;
use Illuminate\Support\Facades\Route;

Route::apiResource('iphones', IphoneController::class);
Route::apiResource('smart-watches', SmartWatchController::class);
Route::apiResource('androids', AndroidController::class);
Route::apiResource('audifonos', AudifonoController::class);

Route::apiResource('baskets', BasketController::class);
Route::post('baskets/{basket}/items', [BasketController::class, 'addItem']);
Route::post('baskets/{basket}/charge', [BasketController::class, 'charge']);

Route::apiResource('invoices', InvoiceController::class)->only(['index', 'show']);
