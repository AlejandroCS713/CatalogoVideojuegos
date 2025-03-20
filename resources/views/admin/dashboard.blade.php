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
@endsection
