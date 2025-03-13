<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <title>@yield('title', 'Forty Template')</title>
    @livewireStyles

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <meta name="friends-search-url" content="{{ route('friends.search') }}">
    <meta name="friends-send-url" content="{{ route('friends.send', '') }}">
    <link rel="stylesheet" href="{{ asset('forty/assets/css/main.css') }}" />
    <noscript><link rel="stylesheet" href="{{ asset('forty/assets/css/noscript.css') }}" /></noscript>
    <link rel="icon" type="image/x-icon" href="{{ asset('forty/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('forty/assets/css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('forty/assets/css/profile.css') }}">

    @stack('styles')
</head>
<body class="@yield('body_class', 'is-preload')">
@include('layouts.menu')
<div id="wrapper">
    @yield('content')
</div>
<script src="{{ asset('forty/assets/js/jquery.min.js') }}"></script>

<script src="{{ asset('forty/assets/js/browser.min.js') }}"></script>
<script src="{{ asset('forty/assets/js/breakpoints.min.js') }}"></script>
<script src="{{ asset('forty/assets/js/util.js') }}"></script>
<script src="{{ asset('forty/assets/js/main.js') }}"></script>
<script src="{{ asset('forty/assets/js/jquery.scrollex.min.js') }}"></script>
<script src="{{ asset('forty/assets/js/jquery.scrolly.min.js') }}"></script>

@stack('scripts')
@livewireScripts
</body>
</html>
