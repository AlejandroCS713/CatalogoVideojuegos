@extends('layouts.app')
@section('title', 'User Profile')
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
            <button class="settings-button" onclick="window.location='{{ route('profile.settings') }}'">⚙️ Settings</button>
        </div>

        <div class="profile-sections">
            <!-- Juegos Favoritos -->
            <div class="profile-section">
                <h2>🎮 Favorite Games</h2>

            </div>

            <!-- Amigos -->
            <div class="profile-section">
                <h2>👥 Friends</h2>

            </div>
        </div>
    </div>

@endsection
