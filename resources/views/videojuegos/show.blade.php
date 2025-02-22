@extends('layouts.app')
@section('body_class', 'is-preload')
@include('layouts.menu')

@section('title', $videojuego->nombre)

@section('content')
    <div class="game-container">
        <h1 class="game-title">{{ $videojuego->nombre }}</h1>
        <div class="game-images">
            @foreach ($videojuego->multimedia as $media)
                @if ($media->tipo === 'imagen')
                    <img class="game-image" src="{{ asset($media->url) }}" alt="{{ __('Image of ') }} {{ $videojuego->nombre }}">
                @endif
            @endforeach
        </div>
        <div class="game-info">
            <p class="game-description">{{ $videojuego->descripcion }}</p>
            <p><strong>{{ __('Release Date:') }}</strong> {{ $videojuego->fecha_lanzamiento ?? __('Unknown') }}</p>
            <p><strong>{{ __('User Rating:') }}</strong> ⭐ {{ number_format($videojuego->rating_usuario, 1) }}</p>
            <p><strong>{{ __('Reviews Rating:') }}</strong> ⭐ {{ number_format($videojuego->rating_criticas, 1) }}</p>
            <p><strong>{{ __('Developer:') }}</strong> {{ $videojuego->desarrollador }}</p>
            <p><strong>{{ __('Publisher:') }}</strong> {{ $videojuego->publicador }}</p>
        </div>
        <div class="game-genres">
            <h2>{{ __('Genres') }}</h2>
            <ul>
                @foreach ($videojuego->generos as $genero)
                    <li class="genre-item">{{ $genero->nombre }}</li>
                @endforeach
            </ul>
        </div>
        <div class="game-platforms">
            <h2>{{ __('Available in:') }}</h2>
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
                            - <span class="game-price">{{ __('Price not available') }}e</span>
                        @endif
                    </li>
                @endforeach
            </ul>
            @can('Actualizar Videojuegos')
                <a href="{{ route('admin.edit', $videojuego->id) }}" class="button fit" style="width: 200px; margin-top: 20px; margin-bottom: 20px ">{{ __('Edit') }}</a>
            @endcan

            @can('Eliminar Videojuegos')
                <form action="{{ route('admin.destroy', $videojuego->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="width: 200px;">{{ __('Delete') }}</button>
                </form>
            @endcan
        </div>
    </div>
@endsection
