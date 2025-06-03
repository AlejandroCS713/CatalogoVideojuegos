<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\ForoController;

use App\Http\Controllers\Api\MensajeForoController;
use App\Http\Controllers\Api\RespuestaForoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

// Rutas públicas para Foros,Mensajes y Respuestas (index y show)
Route::get('/foros', [ForoController::class, 'index'])->name('api.foros.index');
Route::get('/foros/{foro}', [ForoController::class, 'show'])->name('api.foros.show');
Route::get('/foros/{foro}/mensajes', [MensajeForoController::class, 'index'])->name('api.foros.mensajes.index');
Route::get('/mensajes/{mensajeForo}', [MensajeForoController::class, 'show'])->name('api.mensajes.show');
Route::get('/mensajes/{mensajeForo}/respuestas', [RespuestaForoController::class, 'index'])->name('api.mensajes.respuestas.index');
Route::get('/respuestas/{respuestaForo}', [RespuestaForoController::class, 'show'])->name('api.respuestas.show');

// Rutas protegidas para Foros,Mensajes y Respuestas (store, update, destroy)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/foros', [ForoController::class, 'store'])->name('api.foros.store');
    Route::put('/foros/{foro}', [ForoController::class, 'update'])->name('api.foros.update');
    Route::patch('/foros/{foro}', [ForoController::class, 'update']);
    Route::delete('/foros/{foro}', [ForoController::class, 'destroy'])
    ->name('api.foros.destroy')
        ->middleware('can:delete,foro');
    Route::post('/foros/{foro}/mensajes', [MensajeForoController::class, 'store'])->name('api.foros.mensajes.store');
    Route::put('/mensajes/{mensajeForo}', [MensajeForoController::class, 'update'])->name('api.mensajes.update');
    Route::patch('/mensajes/{mensajeForo}', [MensajeForoController::class, 'update']);
    Route::delete('/mensajes/{mensajeForo}', [MensajeForoController::class, 'destroy'])->name('api.mensajes.destroy')
        ->middleware('can:delete,mensajeForo');
    Route::post('/mensajes/{mensajeForo}/respuestas', [RespuestaForoController::class, 'store'])->name('api.mensajes.respuestas.store');
    Route::put('/respuestas/{respuestaForo}', [RespuestaForoController::class, 'update'])->name('api.respuestas.update');
    Route::patch('/respuestas/{respuestaForo}', [RespuestaForoController::class, 'update']);
    Route::delete('/respuestas/{respuestaForo}', [RespuestaForoController::class, 'destroy'])->name('api.respuestas.destroy')
        ->middleware('can:delete,respuestaForo');;
});
