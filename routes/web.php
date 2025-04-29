<?php

use App\Http\Controllers\users\LogroController;
use App\Http\Controllers\users\UserAdminController;
use App\Livewire\AcceptFriendRequests;
use App\Livewire\ChatComponent;
use App\Livewire\FriendList;
use App\Livewire\SearchUsers;
use App\Livewire\Videojuegos\VideoGamesViewComponent;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Livewire\Livewire;
use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\auth\RegisterController;
use App\Http\Controllers\Foro\ForoController;
use App\Http\Controllers\Foro\MensajeForoController;
use App\Http\Controllers\Foro\RespuestaForoController;
use App\Http\Controllers\games\VideojuegoController;
use App\Http\Controllers\users\ProfileController;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
})->name('welcome');


Route::group(['middleware' => ['auth', 'verified', 'role:admin']], function () {
    Route::get('/admin/dashboard', [UserAdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/send-bulk-email', [UserAdminController::class, 'sendBulkEmail'])->name('send.bulk.email');
});

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

Route::get('/', [VideojuegoController::class, 'mejoresValoraciones'])->name('welcome');
Route::get('/videojuegos', [VideojuegoController::class, 'index'])->name('videojuegos.index');
Route::get('/forum', [ForoController::class, 'index'])->name('forum.index');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth', 'verified', 'role:user']], function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/mis-logros/pdf', [LogroController::class, 'generarPDF'])->name('logros.pdf');
    Route::get('/profile/logros', [LogroController::class, 'index'])->name('logros.perfil');
    Route::get('/profile/avatar', [ProfileController::class, 'editAvatar'])->name('profile.avatar');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');

    Route::get('/friends', FriendList::class)->name('friends.list');
    Route::get('/search-users', SearchUsers::class)->name('friends.search');
    Route::get('/accept-friend-requests', AcceptFriendRequests::class)->name('friends.accept.requests');
    Route::post('/send-friend-request/{id}', [SearchUsers::class, 'sendFriendRequest'])->name('friends.send');
    Route::post('/accept-friend-request/{id}', [AcceptFriendRequests::class, 'acceptRequest'])->name('friends.accept');
    Route::post('/remove-friend/{id}', [FriendList::class, 'removeFriend'])->name('friends.remove');

    Route::get('/chat/{friendId}', ChatComponent::class)->name('message.chat');
    Route::post('/forum/{foro}/mensajes', [MensajeForoController::class, 'store'])->name('mensajes.store');
    Route::post('/mensajes/{mensaje}/respuestas', [RespuestaForoController::class, 'store'])->name('respuestas.store');

    Route::get('/forum/{foro}/pdf', [ForoController::class, 'generarPDF'])->name('forum.pdf');
    Route::delete('/mensaje-foro/{mensaje}', [MensajeForoController::class, 'destroy'])->name('mensaje-foro.destroy');
});

Route::get('/forum/{foro}', [ForoController::class, 'show'])->name('forum.show');
Route::get('/videojuegos/{id}', [VideojuegoController::class, 'show'])->name('videojuegos.show');

