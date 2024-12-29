<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

/* Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); */

Route::controller(OrderController::class)
    ->middleware('api.token')
    ->group(function () {   
        Route::get('/orders/search', 'search')->name('orders.search'); // Dettaglio ordine
        Route::post('/orders', 'store')->name('orders.store'); // Creazione ordine
        Route::put('/orders', 'update')->name('orders.update'); // Modifica ordine
        Route::delete('/orders/{id}', 'destroy')->name('orders.destroy'); // Cancellazione ordine
    });