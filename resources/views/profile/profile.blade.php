@php use Illuminate\Support\Facades\Auth; @endphp
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

            <!-- Lista de Amigos -->
            <div class="profile-section">
                <h2>👥 Friends</h2>
                <ul class="friends-list">
                    @foreach ($friends as $friend)
                        @php
                            // Determinar quién es el amigo (porque la relación es bidireccional)
                            $friendUser = ($friend->user_id == Auth::id()) ? $friend->friend : $friend->user;
                        @endphp
                        <li class="friend-item">
                            <img src="{{ asset('forty/images/avatars/' . $friendUser->avatar) }}" alt="Avatar de {{ $friendUser->name }}" class="friend-avatar">
                            <span class="friend-name">{{ $friendUser->name }}</span>

                            <!-- Menú con 3 puntos -->
                            <div class="dropdown">
                                <button class="dropdown-toggle">⋮</button>
                                <div class="dropdown-menu">
                                    <form method="POST" action="{{ route('friends.remove', $friendUser->id) }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Delete Friend</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Buscador de Usuarios -->
            <div class="search-users">
                <input type="text" id="search" placeholder="Search for users..." class="search-user-cs">
                <ul id="search-results"></ul>
            </div>

            <!-- Solicitudes de Amistad -->
            <div class="profile-section">
                <h2>📩 Friend Requests</h2>
                <ul>
                    @foreach (Auth::user()->friendRequests as $request)
                        <li>{{ $request->user->name }}
                            <form method="POST" action="{{ route('friends.accept', $request->user->id) }}">
                                @csrf
                                <button type="submit">Accept</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>

@endsection
