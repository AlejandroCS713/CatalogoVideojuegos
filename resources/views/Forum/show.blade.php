@extends('layouts.app')
@section('title', 'Forum')
@section('body_class', 'is-preload')
@include('layouts.menu')
@section('content')
    <div class="game-container">
        <h1 class="game-title">{{ $foro->titulo }}</h1>
        <div class="game-info">
            <p class="game-description">{{ $foro->descripcion }}</p>
        </div>

        <div class="game-genres">
            <h2>Videojuegos Relacionados</h2>
            <div class="game-genres">
            <ul>
                @forelse($foro->videojuegos as $videojuego)
                    <li>
                        <strong>{{ $videojuego->nombre }}</strong>
                    </li>
                @empty
                    <li>No hay videojuegos relacionados con este foro.</li>
                @endforelse
                    <br>
            </ul>
            </div>
        </div>

        <div class="game-genres">
            <h2>Mensajes en este foro:</h2>

            @foreach($foro->mensajes as $mensaje)
                <div>
                    <div>
                        <p>{{ $mensaje->contenido }}</p>
                        <small>Publicado por {{ $mensaje->usuario->name }} el {{ $mensaje->created_at->format('d/m/Y H:i') }}</small>

                        <div>
                            <h5>Respuestas:</h5>
                            @foreach($mensaje->respuestas as $respuesta)
                                <div>
                                    <p>{{ $respuesta->contenido }}</p>
                                    <small>- {{ $respuesta->usuario->name }} el {{ $respuesta->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @auth
            <div>
                <form action="{{ route('mensajes.store', $foro->id) }}" method="POST">
                    @csrf
                    <div>
                        <textarea name="contenido" required style="color: black; margin-bottom: 20px" placeholder="Send message"></textarea>
                    </div>
                    <button type="submit">Enviar</button>
                </form>
            </div>
        @endauth
    </div>
@endsection
