@php use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.app')
@section('title', __('User Profile'))
@section('body_class', 'is-preload')
@section('content')
    <div class="profile-container">
        <div class="profile-header">
            <div class="avatar">
                <a style="background: none; border: none;cursor: pointer;" href="{{ route('profile.avatar') }}" ><img src="{{ asset('forty/images/avatars/' . Auth::user()->avatar) }}" alt="{{ __('Avatar of') }} {{ Auth::user()->name }}">
                </a>
            </div>
            <h1>{{ Auth::user()->name }}</h1>
            <a href="{{ route('logros.perfil') }}" class="button fit" style="width: 300px">🎯 {{ __('View My Achievements') }}</a>
        </div>
        <div class="profile-sections">
            @livewire('friend-list')
            <h1 class="text-center mb-5">{{ __('Search Users') }}</h1>
            @livewire('search-users')

            @livewire('accept-friend-requests')
        </div>
    </div>
@endsection
