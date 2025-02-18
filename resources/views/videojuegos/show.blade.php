@extends('layouts.app')
@section('title', 'GAME QUEST')
@section('body_class', 'is-preload')
@include('layouts.menu')

@section('title', $videojuego->nombre)

@section('content')
    <div class="game-container">
        <h1 class="game-title">{{ $videojuego->nombre }}</h1>
        <div class="game-images">
            @foreach ($videojuego->multimedia as $media)
                @if ($media->tipo === 'imagen')
                    <img class="game-image" src="{{ asset($media->url) }}" alt="Imagen de {{ $videojuego->nombre }}">
                @endif
            @endforeach
        </div>
        <div class="game-info">
            <p class="game-description">{{ $videojuego->descripcion }}</p>
            <p><strong>Release Date:</strong> {{ $videojuego->fecha_lanzamiento ?? 'Desconocida' }}</p>
            <p><strong>User Rating:</strong> ⭐ {{ number_format($videojuego->rating_usuario, 1) }}</p>
            <p><strong>Reviews Rating:</strong> ⭐ {{ number_format($videojuego->rating_criticas, 1) }}</p>
            <p><strong>Developer:</strong> {{ $videojuego->desarrollador }}</p>
            <p><strong>Publisher:</strong> {{ $videojuego->publicador }}</p>
        </div>
        <div class="game-genres">
            <h2>Genres</h2>
            <ul>
                @foreach ($videojuego->generos as $genero)
                    <li class="genre-item">{{ $genero->nombre }}</li>
                @endforeach
            </ul>
        </div>
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
            @auth
            @if(auth()->user()->can('editar juegos'))
                <a href="{{ route('videojuegos.edit', $videojuego->id) }}" class="btn btn-warning">Editar</a>
            @endif

            @if(auth()->user()->can('eliminar juegos'))
                <form action="{{ route('videojuegos.destroy', $videojuego->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            @endif
            @endauth
        </div>
    </div>
@endsection
