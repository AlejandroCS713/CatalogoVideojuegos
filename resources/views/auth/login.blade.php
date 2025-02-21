@extends('layouts.app')

@section('title', 'Logear')
@include('layouts.menu')
@section('content')
    <div class="form-container2">
        @if(session('success'))
            <p style="color: green;">{{ session('success') }}</p>
        @endif
        <form class="form" action="{{ route('login') }}" method="POST" autocomplete="off">
            @csrf
            <div class="control">
                <h1>Iniciar Sesión</h1>
            </div>

            <div class="control block-cube block-input">
                <input type="text" name="email" placeholder="Email" required>
                <div class="bg-top"><div class="bg-inner"></div></div>
                <div class="bg-right"><div class="bg-inner"></div></div>
                <div class="bg"><div class="bg-inner"></div></div>
            </div>

            <div class="control block-cube block-input">
                <input type="password" name="password" placeholder="Contraseña" required>
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
            <p>¿No tienes cuenta aun? <a href="{{ route('register') }}">Registrate Aqui</a></p>
        </div>
    </div>

@endsection
