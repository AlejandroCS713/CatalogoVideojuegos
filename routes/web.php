<?php

use App\Http\Controllers\users\UserAdminController;
use Livewire\Livewire;
use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\auth\RegisterController;
use App\Http\Controllers\forum\ForoController;
use App\Http\Controllers\forum\MensajeForoController;
use App\Http\Controllers\forum\RespuestaForoController;
use App\Http\Controllers\games\MultimediaController;
use App\Http\Controllers\games\VideojuegoController;
use App\Http\Controllers\users\FriendController;
use App\Http\Controllers\users\MessageController;
use App\Http\Controllers\users\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
})->name('welcome');
Route::post('/videojuegos', [VideojuegoController::class, 'store']);

Route::post('/videojuegos/{videojuego}/multimedia', [MultimediaController::class, 'store'])->name('multimedia.store');

// Rutas a vistas
Route::get('/', [VideojuegoController::class, 'mejoresValoraciones'])->name('welcome');
Route::get('/videojuegos', [VideojuegoController::class, 'index'])->name('videojuegos.index');
Route::get('/forum', [ForoController::class, 'index'])->name('forum.index');


// Procesar formularios de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::get('/profile/avatar', [ProfileController::class, 'editAvatar'])->name('profile.avatar');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::post('/send-friend-request/{id}', [FriendController::class, 'sendRequest'])->name('friends.send');
    Route::post('/accept-friend-request/{id}', [FriendController::class, 'acceptRequest'])->name('friends.accept');
    Route::post('/remove-friend/{id}', [FriendController::class, 'removeFriend'])->name('friends.remove');
    Route::get('/search-users', [FriendController::class, 'searchUsers'])->name('friends.search');
    Route::get('/chat/{friend_id}', [MessageController::class, 'chat'])->name('message.chat');
    Route::post('/send-message', [MessageController::class, 'sendMessage'])->name('message.send');
    Route::post('/forum/{foro}/mensajes', [MensajeForoController::class, 'store'])->name('mensajes.store');
    Route::post('/mensajes/{mensaje}/respuestas', [RespuestaForoController::class, 'store'])->name('respuestas.store');
    Route::get('/forum/create', [ForoController::class, 'create'])->name('forum.create');
    Route::post('/forum', [ForoController::class, 'store'])->name('forum.store');

});
Route::middleware(['auth', 'permission:crear juegos'])->group(function () {
    Route::get('/admin/create', [VideojuegoController::class, 'create'])->name('admin.create');
    Route::post('/admin', [VideojuegoController::class, 'store'])->name('admin.store');
});

Route::middleware(['auth', 'permission:editar juegos'])->group(function () {
    Route::get('/admin/{id}/edit', [VideojuegoController::class, 'edit'])->name('admin.edit');
    Route::put('/admin/{id}', [VideojuegoController::class, 'update'])->name('admin.update');
});

Route::middleware(['auth', 'permission:eliminar juegos'])->group(function () {
    Route::delete('/admin/{id}', [VideojuegoController::class, 'destroy'])->name('admin.destroy');
});
Route::get('/forum/{foro}', [ForoController::class, 'show'])->name('forum.show');
Route::get('/videojuegos/{id}', [VideojuegoController::class, 'show'])->name('videojuegos.show');

