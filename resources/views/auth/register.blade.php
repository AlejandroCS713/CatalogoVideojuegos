@extends('layouts.app')
@section('title', __('Register'))
@section('content')
    <div class="form-container2">
        <form class="form" action="{{ route('register') }}" method="POST" autocomplete="off">
            @csrf
            <div class="control">
                <h1>{{ __('Register') }}</h1>
            </div>

            <div class="control block-cube block-input">
                <input type="text" name="email" placeholder="{{ __('Email') }}" required>
                <div class="bg-top"><div class="bg-inner"></div></div>
                <div class="bg-right"><div class="bg-inner"></div></div>
                <div class="bg"><div class="bg-inner"></div></div>
            </div>

            <div class="control block-cube block-input">
                <input type="text" name="name" placeholder="{{ __('Name') }}" required>
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

            <div class="control block-cube block-input">
                <input type="password" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" required>
                <div class="bg-top"><div class="bg-inner"></div></div>
                <div class="bg-right"><div class="bg-inner"></div></div>
                <div class="bg"><div class="bg-inner"></div></div>
            </div>

            <button class="btn block-cube block-cube-hover" type="submit">
                <div class="bg-top"><div class="bg-inner"></div></div>
                <div class="bg-right"><div class="bg-inner"></div></div>
                <div class="bg"><div class="bg-inner"></div></div>
                <span class="text">Registrar</span>
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
            <p>{{ __('Have Account') }} <a href="{{ route('login') }}">{{ __('Login Here') }}</a></p>
        </div>
    </div>

@endsection
