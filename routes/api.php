<?php
use App\Http\Controllers\Api\ForoController;
use Illuminate\Support\Facades\Route;

Route::get('foros', [ForoController::class, 'index'])->name('api.foros.index');
Route::get('foros/{foro}', [ForoController::class, 'show'])->name('api.foros.show');
