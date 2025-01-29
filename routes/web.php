<?php

use App\Http\Controllers\ForoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MultimediaController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\VideojuegoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');
Route::post('/videojuegos', [VideojuegoController::class, 'store']);

Route::post('/videojuegos/{videojuego}/multimedia', [MultimediaController::class, 'store'])->name('multimedia.store');

// Rutas a vistas
Route::get('/', [VideojuegoController::class, 'mejoresValoraciones'])->name('welcome');
Route::get('/videojuegos', [VideojuegoController::class, 'index'])->name('videojuegos.index');
Route::get('/videojuegos/{id}', [VideojuegoController::class, 'show'])->name('videojuegos.show');
Route::get('/foro', [ForoController::class, 'index'])->name('foro.index');
// Mostrar formularios de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');

// Procesar formularios de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register'])->name('register');
