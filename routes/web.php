<?php

use App\Http\Controllers\InfoController;
use App\Http\Controllers\users\LogroController;
use App\Http\Controllers\users\UserAdminController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
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
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Route;





Route::get('/', function () {
    return view('welcome');
})->middleware('verified');

Route::post('/videojuegos', [VideojuegoController::class, 'store']);

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

Route::post('/videojuegos/{videojuego}/multimedia', [MultimediaController::class, 'store'])->name('multimedia.store');

Route::get('/', [VideojuegoController::class, 'mejoresValoraciones'])->name('welcome');
Route::get('/videojuegos', [VideojuegoController::class, 'index'])->name('videojuegos.index');
Route::get('/forum', [ForoController::class, 'index'])->name('forum.index');
Route::get('/info', [InfoController::class, 'index'])->name('info');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth','verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/logros', [LogroController::class, 'index'])->name('logros.perfil');
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

Route::middleware(['auth','verified'])->group(function () {
    Route::post('/admin', [UserAdminController::class, 'store'])->name('admin.store');
    Route::get('/admin/create', [UserAdminController::class, 'create'])->name('admin.create');
    Route::get('/admin/{id}/edit', [UserAdminController::class, 'edit'])->name('admin.edit');
    Route::put('/admin/{id}', [UserAdminController::class, 'update'])->name('admin.update');
    Route::delete('/admin/{id}', [UserAdminController::class, 'destroy'])->name('admin.destroy');
});

Route::middleware(['auth','verified'])->group(function () {
    Route::get('/forum/{foro}/pdf', [ForoController::class, 'generarPDF'])->name('forum.pdf');
    Route::get('/forum/{foro}/edit', [ForoController::class, 'edit'])->name('forum.edit');
    Route::put('/forum/{foro}', [ForoController::class, 'update'])->name('forum.update');
    Route::delete('/forum/{foro}', [ForoController::class, 'destroy'])->name('forum.destroy');
});

Route::get('/forum/{foro}', [ForoController::class, 'show'])->name('forum.show');
Route::get('/videojuegos/{id}', [VideojuegoController::class, 'show'])->name('videojuegos.show');

