@extends('layouts.app')

<!-- Header -->
<!-- Menu -->
@section('body_class', 'is-preload')
@include('layouts.menu')

@section('title', 'Todos los Videojuegos')
@section('content')
    <div class="videojuegos-container">
    <h1 class="text-center mb-5">Videojuegos Disponibles</h1>
    <div class="videojuegos-grid">
        @foreach ($videojuegos as $videojuego)
            <div class="videojuego-card">
                <div class="videojuego-image">
                    <img class="imagenes" src="{{ asset($videojuego->multimedia->first()->url) }}" alt="Imagen de {{ $videojuego->nombre }}"/>
                </div>
                <div class="videojuego-info">
                    <h3>{{ $videojuego->nombre }}</h3>
                    <p>{{ Str::limit($videojuego->descripcion, 100) }}</p>
                    <a href="{{ route('videojuegos.show', $videojuego->id) }}" class="btn btn-primary">Ver más</a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Paginación -->
        <ul class="pagination">
            {{ $videojuegos->links() }}
        </ul>
    </div>
@endsection
