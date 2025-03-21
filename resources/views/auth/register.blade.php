@extends('layouts.app')
@section('title', __('Register'))
@section('content')
    <div class="form-container2">
        <x-auth-form
            title="Register"
            route="register"
            buttonText="Registrar"
            :fields="[
        ['name' => 'email', 'type' => 'text', 'placeholder' => 'Email'],
        ['name' => 'name', 'type' => 'text', 'placeholder' => 'Name'],
        ['name' => 'password', 'type' => 'password', 'placeholder' => 'Password'],
        ['name' => 'password_confirmation', 'type' => 'password', 'placeholder' => 'Confirm Password']
    ]"
        />
        <div class="register-option">
            <p>{{ __('Have Account') }} <a href="{{ route('login') }}">{{ __('Login Here') }}</a></p>
        </div>
    </div>

@endsection
