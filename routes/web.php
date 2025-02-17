<?php

use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\auth\RegisterController;
use App\Http\Controllers\forum\ForoController;
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
Route::get('/videojuegos/{id}', [VideojuegoController::class, 'show'])->name('videojuegos.show');
Route::get('/foro', [ForoController::class, 'index'])->name('foro.index');

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
});
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('videojuegos', VideojuegoController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
});

