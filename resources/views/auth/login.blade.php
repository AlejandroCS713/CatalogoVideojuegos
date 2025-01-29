@extends('layouts.app')

@section('title', 'Iniciar Sesión')

@section('content')
    <div class="form-container">
        <form class="form" action="{{ route('login') }}" method="POST" autocomplete="off">
            @csrf
            <div class="control">
                <h1>Sign In</h1>
            </div>

            <div class="control block-cube block-input">
                <input type="text" name="username" placeholder="Username" required>
                <div class="bg-top"><div class="bg-inner"></div></div>
                <div class="bg-right"><div class="bg-inner"></div></div>
                <div class="bg"><div class="bg-inner"></div></div>
            </div>

            <div class="control block-cube block-input">
                <input type="password" name="password" placeholder="Password" required>
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

        </form>
    </div>

@endsection
