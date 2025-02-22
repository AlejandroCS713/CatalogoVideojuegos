@extends('layouts.app')

@section('title', __('Login'))
@include('layouts.menu')
@section('content')
    <div class="form-container2">
        @if(session('success'))
            <p style="color: green;">{{ session('success') }}</p>
        @endif
        <form class="form" action="{{ route('login') }}" method="POST" autocomplete="off">
            @csrf
            <div class="control">
                <h1>{{ __('Login') }}</h1>
            </div>

            <div class="control block-cube block-input">
                <input type="text" name="email" placeholder="{{ __('Email') }}" required>
                <div class="bg-top"><div class="bg-inner"></div></div>
                <div class="bg-right"><div class="bg-inner"></div></div>
                <div class="bg"><div class="bg-inner"></div></div>
            </div>

            <div class="control block-cube block-input">
                <input type="password" name="password" placeholder="{{ __('Password') }}" required>
                <div class="bg-top"><div class="bg-inner"></div></div>
                <div class="bg-right"><div class="bg-inner"></div></div>
                <div class="bg"><div class="bg-inner"></div></div>
            </div>

            <button class="btn block-cube block-cube-hover" type="submit">
                <div class="bg-top"><div class="bg-inner"></div></div>
                <div class="bg-right"><div class="bg-inner"></div></div>
                <div class="bg"><div class="bg-inner"></div></div>
                <span class="text">Log In</span>
            </button>
            <br>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </form>

        <div class="register-option">
            <p>{{ __('No Account') }}<a href="{{ route('register') }}">{{ __('Register Here') }}</a></p>
        </div>
    </div>

@endsection
