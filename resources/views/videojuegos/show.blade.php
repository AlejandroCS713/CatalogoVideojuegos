@extends('layouts.app')
<!-- Header -->
@section('title', 'GAME QUEST')

<!-- Menu -->
@section('body_class', 'is-preload')
@include('layouts.menu')

@section('title', $videojuego->nombre)

@section('content')
    <div class="game-container">
        <!-- Título del videojuego -->
        <h1 class="game-title">{{ $videojuego->nombre }}</h1>

        <!-- Imágenes -->
        <div class="game-images">
            @foreach ($videojuego->multimedia as $media)
                @if ($media->tipo === 'imagen')
                    <img class="game-image" src="{{ asset($media->url) }}" alt="Imagen de {{ $videojuego->nombre }}">
                @endif
            @endforeach
        </div>

        <!-- Información del videojuego -->
        <div class="game-info">
            <p class="game-description">{{ $videojuego->descripcion }}</p>
            <p><strong>Release Date:</strong> {{ $videojuego->fecha_lanzamiento ?? 'Desconocida' }}</p>
            <p><strong>User Rating:</strong> ⭐ {{ number_format($videojuego->rating_usuario, 1) }}</p>
            <p><strong>Reviews Rating:</strong> ⭐ {{ number_format($videojuego->rating_criticas, 1) }}</p>
            <p><strong>Developer:</strong> {{ $videojuego->desarrollador }}</p>
            <p><strong>Publisher:</strong> {{ $videojuego->publicador }}</p>
        </div>

        <!-- Géneros -->
        <div class="game-genres">
            <h2>Genres</h2>
            <ul>
                @foreach ($videojuego->generos as $genero)
                    <li class="genre-item">{{ $genero->nombre }}</li>
                @endforeach
            </ul>
        </div>

        <!-- Plataformas y precios -->
        <div class="game-platforms">
            <h2>Available in:</h2>
            <ul>
                @foreach ($videojuego->plataformas as $plataforma)
                    <li>
                        <strong>{{ $plataforma->nombre }}</strong>
                        @php
                            $precio = $videojuego->precios->where('plataforma_id', $plataforma->id)->first();
                        @endphp
                        @if ($precio)
                            - <span class="game-price">💰 {{ number_format($precio->precio, 2) }} €</span>
                        @else
                            - <span class="game-price">Price not available</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
@endsection
