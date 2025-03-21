@extends('layouts.app')
@section('title', __('Login'))
@section('content')

        @if(session('success'))
            <p style="color: green;">{{ session('success') }}</p>
        @endif

        <x-auth-form
            title="Login"
            route="login"
            buttonText="Log In"
            :fields="[
        ['name' => 'email', 'type' => 'text', 'placeholder' => 'Email'],
        ['name' => 'password', 'type' => 'password', 'placeholder' => 'Password']
    ]"
        />
        <div class="register-option">
            <p>{{ __('No Account') }}<a href="{{ route('register') }}">{{ __('Register Here') }}</a></p>
        </div>
@endsection
