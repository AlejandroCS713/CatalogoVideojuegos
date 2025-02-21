@extends('layouts.app')
@section('title', 'Registro De Usuario')
@include('layouts.menu')
@section('content')
    <div class="form-container2">
        <form class="form" action="{{ route('register') }}" method="POST" autocomplete="off">
            @csrf
            <div class="control">
                <h1>Registro De Usuario</h1>
            </div>

            <div class="control block-cube block-input">
                <input type="text" name="email" placeholder="Email" required>
                <div class="bg-top"><div class="bg-inner"></div></div>
                <div class="bg-right"><div class="bg-inner"></div></div>
                <div class="bg"><div class="bg-inner"></div></div>
            </div>

            <div class="control block-cube block-input">
                <input type="text" name="name" placeholder="Nombre" required>
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

            <div class="control block-cube block-input">
                <input type="password" name="password_confirmation" placeholder="Confirma la contraseña" required>
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
            <p>Ya tienes una cuenta? <a href="{{ route('login') }}">Inicia Sesión aqui</a></p>
        </div>
    </div>

@endsection
