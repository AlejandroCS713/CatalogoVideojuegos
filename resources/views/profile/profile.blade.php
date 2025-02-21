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
            <button onclick="window.location='{{ route('profile.settings') }}'">⚙️ Configuración</button>
        </div>
        <div class="profile-sections">
            <div class="profile-section">
                <h2>👥 Amigos</h2>
                <ul class="friends-list">
                    @foreach ($friends as $friend)
                        @php
                            $friendUser = ($friend->user_id == Auth::id()) ? $friend->friend : $friend->user;
                        @endphp
                        <li class="friend-item">
                            <img src="{{ asset('forty/images/avatars/' . $friendUser->avatar) }}" alt="Avatar de {{ $friendUser->name }}" class="friend-avatar">
                            <span class="friend-name">{{ $friendUser->name }}</span>
                            <div class="dropdown">
                                <button class="dropdown-toggle">⋮</button>
                                <div class="dropdown-menu">
                                    <form action="{{ route('message.chat', $friendUser->id)  }}" method="GET">
                                        <button type="submit" class="dropdown-item chat-btn">
                                            💬 Chat
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('friends.remove', $friendUser->id) }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Eliminar Amigo</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <h1 class="text-center mb-5">Buscar Usuarios</h1>
            @livewire('search-users')

            <div class="profile-section">
                <h2>📩 Solicitudes de amistad</h2>
                <ul>
                    @foreach (Auth::user()->friendRequests as $request)
                        <li>{{ $request->user->name }}
                            <form method="POST" action="{{ route('friends.accept', $request->user->id) }}">
                                @csrf
                                <button type="submit">Aceptar</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
