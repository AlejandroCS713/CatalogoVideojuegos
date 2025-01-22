@extends('layouts.app')

<!-- Header -->
<!-- Menu -->
@section('body_class', 'is-preload')
@include('layouts.menu')

@section('title', 'Todos los Videojuegos')

@section('content')
    <h1>Videojuegos Disponibles</h1>
    <ul>
        @foreach ($videojuegos as $videojuego)
            <li>
                <a href="{{ route('videojuegos.show', $videojuego->id) }}">
                    {{ $videojuego->nombre }}
                </a>
                <p>{{ Str::limit($videojuego->descripcion, 100) }}</p>
            </li>
        @endforeach
    </ul>

    <!-- Paginación -->
    {{ $videojuegos->links() }}
@endsection
