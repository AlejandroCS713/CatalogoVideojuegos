@extends('layouts.app')
@section('title', __('Dashboard Admin'))
@section('content')
    <div class="container_admin">
        <h1 class="dashboard_admin_title">Bienvenido al Dashboard del Administrador</h1>

        <div class="row_admin">
            <div class="col_admin">
                <h3 class="count_admin">Total de Usuarios: {{ $userCount }}</h3>
                <div class="list_container">
                    <ul class="list_admin">
                        @foreach ($users as $user)
                            <li class="list_item">{{ $user->name }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col_admin">
                <h3 class="count_admin">Total de Foros: {{ $forumCount }}</h3>
                <div class="list_container">
                    <ul class="list_admin">
                        @foreach ($foros as $foro)
                            <li class="list_item">{{ $foro->titulo }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col_admin">
                <h3 class="count_admin">Total de Videojuegos: {{ $gameCount }}</h3>
                <div class="list_container">
                    <ul class="list_admin">
                        @foreach ($videojuegos as $videojuego)
                            <li class="list_item">{{ $videojuego->nombre }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="col_admin">
        <h2 style="margin-bottom: 20px;">Enviar Correo Masivo a Usuarios</h2>
        <form action="{{ route('send.bulk.email') }}" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label for="message" style="display: block; margin-bottom: 5px; font-weight: bold;">Mensaje:</label>
                <textarea name="message" id="message" rows="8" required style="width: 700px;color:black; padding: 10px; border: 1px solid #ccc; border-radius: 4px; min-height: 150px;">{{ old('message') }}</textarea>
                @error('message')
                <p style="color: red; font-size: 0.9em; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" style="padding: 10px 20px; cursor: pointer;">
                Enviar Correo a Todos los Usuarios
            </button>
        </form>
    </div>
@endsection
