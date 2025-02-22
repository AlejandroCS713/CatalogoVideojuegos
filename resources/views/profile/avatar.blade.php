@extends('layouts.app')
@section('title', __('Avatar Change'))
@section('body_class', 'is-preload')
@include('layouts.menu')
@section('content')
    <div class="profile-container">
        <h2>{{ __('Choose your avatar') }}</h2>
        <form action="{{ route('profile.avatar.update') }}" method="POST">
            @csrf
            @foreach ($avatars as $avatar)
                <label class="avatar-option">
                    <input type="radio" name="avatar" value="{{ $avatar }}" {{ Auth::user()->avatar == $avatar ? 'checked' : '' }}>
                    <img src="{{ asset('forty/images/avatars/' . $avatar) }}" alt="{{ __('Avatar') }}">
                </label>
            @endforeach
            <button type="submit" class="btn-avatar">{{ __('Save Avatar') }}</button>
        </form>
    </div>
@endsection
