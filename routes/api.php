<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\ForoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

// Rutas públicas para Foros (index y show)
Route::get('/foros', [ForoController::class, 'index'])->name('api.foros.index');
Route::get('/foros/{foro}', [ForoController::class, 'show'])->name('api.foros.show');

// Rutas protegidas para Foros (store, update, destroy)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/foros', [ForoController::class, 'store'])->name('api.foros.store');
    Route::put('/foros/{foro}', [ForoController::class, 'update'])->name('api.foros.update');
    Route::patch('/foros/{foro}', [ForoController::class, 'update']);
    Route::delete('/foros/{foro}', [ForoController::class, 'destroy'])
    ->name('api.foros.destroy')
        ->middleware('can:delete,foro');
});
