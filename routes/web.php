<?php

use App\Http\Controllers\MultimediaController;
use App\Http\Controllers\VideojuegoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');
Route::post('/videojuegos', [VideojuegoController::class, 'store']);
Route::get('/', [VideojuegoController::class, 'index'])->name('welcome');
Route::post('/videojuegos/{videojuego}/multimedia', [MultimediaController::class, 'store'])->name('multimedia.store');
