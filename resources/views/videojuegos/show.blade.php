@extends('layouts.app')

@section('title', $videojuego->nombre)

@section('content')
    <h1>{{ $videojuego->nombre }}</h1>
    <p>{{ $videojuego->descripcion }}</p>
    <p><strong>Calificación de usuarios:</strong> {{ number_format($videojuego->rating_usuario, 1) }}</p>

    <h2>Imágenes</h2>
    @foreach ($videojuego->multimedia as $media)
        @if ($media->tipo === 'imagen')
            <img src="{{ asset($media->url) }}" alt="Imagen de {{ $videojuego->nombre }}">
        @endif
    @endforeach
@endsection
