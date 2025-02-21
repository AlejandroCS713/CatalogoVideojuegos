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
            <ul>
                @forelse($foro->videojuegos as $videojuego)
                    <li>
                        <strong>{{ $videojuego->nombre }}</strong>
                        <br>
                        @if ($videojuego->multimedia->isNotEmpty())
                            <a style="background: none; border: none;cursor: pointer;" href="{{ route('videojuegos.show', $videojuego->id) }}"><img style="width:200px;position: relative; z-index: 2; padding-right: 20px; padding-bottom: 40px" class="imagenes" src="{{ asset($videojuego->multimedia->first()->url) }}" alt="Imagen de {{ $videojuego->nombre }}"/></a>
                        @else
                            <a style="background: none; border: none;cursor: pointer;" href="{{ route('videojuegos.show', $videojuego->id) }}"> {{ $videojuego->nombre }}</a>
                        @endif
                    </li>
                @empty
                    <li>No hay videojuegos relacionados con este foro.</li>
                @endforelse
            </ul>
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

                        @auth
                            <form action="{{ route('respuestas.store', $mensaje->id) }}" method="POST">
                                @csrf
                                <div>
                                    <textarea name="contenido" required style="color: black; margin-bottom: 20px" placeholder="Responde a este mensaje..."></textarea>
                                    <input type="hidden" name="mensaje_id" value="{{ $mensaje->id }}">
                                </div>
                                <button type="submit">Enviar Respuesta</button>
                            </form>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>

        @auth
            <div>
                <h3>Nuevo Mensaje:</h3>
                <form action="{{ route('mensajes.store', $foro->id) }}" method="POST">
                    @csrf
                    <div>
                        <textarea name="contenido" required style="color: black; margin-bottom: 20px" placeholder="Escribe tu mensaje..."></textarea>
                    </div>
                    <input type="hidden" name="foro_id" value="{{ $foro->id }}">
                    <button type="submit">Enviar Mensaje</button>
                </form>
            </div>
            @can('update', $foro)
                <a href="{{ route('forum.edit', $foro) }}" class="button fit" style="width: 200px; margin-top: 20px; margin-bottom: 20px ">Editar</a>
            @endcan
            @can('delete', $foro)
                <form action="{{ route('forum.destroy', $foro) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="width: 200px;">Eliminar</button>
                </form>
            @endcan
            <a href="{{ route('forum.pdf', $foro) }}" class="button fit" style="width: 200px; margin-top: 20px; margin-bottom: 20px ">
                Descargar PDF
            </a>
        @endauth
    </div>
@endsection

