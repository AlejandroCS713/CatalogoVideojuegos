@extends('layouts.app')
@section('title', 'Perfil de Usuario')
@section('body_class', 'is-preload')
@include('layouts.menu')

@section('content')
    <div class="profile-container">
        <div class="profile-header">
            <div class="avatar">
                <a style="background: none; border: none;cursor: pointer;" href="{{ route('profile.avatar') }}" ><img src="{{ asset('forty/images/avatars/' . Auth::user()->avatar) }}" alt="Avatar de {{ Auth::user()->name }}">
                </a>
            </div>
            <h1>{{ Auth::user()->name }}</h1>
            <button class="settings-button" onclick="window.location='{{ route('profile.settings') }}'">⚙️ Ajustes</button>
        </div>

        <div class="profile-sections">
            <!-- Juegos Favoritos -->
            <div class="profile-section">
                <h2>🎮 Juegos Favoritos</h2>
                <p>Aquí aparecerán tus juegos favoritos próximamente...</p>
            </div>

            <!-- Amigos -->
            <div class="profile-section">
                <h2>👥 Amigos</h2>
                <p>Aquí aparecerá tu lista de amigos próximamente...</p>
            </div>
        </div>
    </div>

@endsection
