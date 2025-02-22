@php use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.app')
@section('title', __('User Profile'))
@section('body_class', 'is-preload')
@include('layouts.menu')

@section('content')
    <div class="profile-container">
        <div class="profile-header">
            <div class="avatar">
                <a style="background: none; border: none;cursor: pointer;" href="{{ route('profile.avatar') }}" ><img src="{{ asset('forty/images/avatars/' . Auth::user()->avatar) }}" alt="{{ __('Avatar of') }} {{ Auth::user()->name }}">
                </a>
            </div>
            <h1>{{ Auth::user()->name }}</h1>
            <button onclick="window.location='{{ route('profile.settings') }}'">⚙️ {{ __('Settings') }}</button>
            <a href="{{ route('logros.perfil') }}" class="button fit" style="width: 300px">🎯 {{ __('View My Achievements') }}</a>
        </div>
        <div class="profile-sections">
            <div class="profile-section">
                <h2>👥 {{ __('Friends') }}</h2>
                <ul class="friends-list">
                    @foreach ($friends as $friend)
                        @php
                            $friendUser = ($friend->user_id == Auth::id()) ? $friend->friend : $friend->user;
                        @endphp
                        <li class="friend-item">
                            <img src="{{ asset('forty/images/avatars/' . $friendUser->avatar) }}" alt="{{ __('Avatar of') }}{{ $friendUser->name }}" class="friend-avatar">
                            <span class="friend-name">{{ $friendUser->name }}</span>
                            <div class="dropdown">
                                <button class="dropdown-toggle">⋮</button>
                                <div class="dropdown-menu">
                                    <form action="{{ route('message.chat', $friendUser->id)  }}" method="GET">
                                        <button type="submit" class="dropdown-item chat-btn">
                                            💬 {{ __('Chat') }}
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('friends.remove', $friendUser->id) }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">{{ __('Remove Friend') }}</button>
                                    </form>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <h1 class="text-center mb-5">{{ __('Search Users') }}</h1>
            @livewire('search-users')

            <div class="profile-section">
                <h2>📩 {{ __('Friend Requests') }}</h2>
                <ul>
                    @foreach (Auth::user()->friendRequests as $request)
                        <li>{{ $request->user->name }}
                            <form method="POST" action="{{ route('friends.accept', $request->user->id) }}">
                                @csrf
                                <button type="submit">{{ __('Accept') }}</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
