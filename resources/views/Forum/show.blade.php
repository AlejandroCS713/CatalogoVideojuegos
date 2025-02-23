@extends('layouts.app')

@section('title',  __('View Forum'))
@section('body_class', 'is-preload')
@include('layouts.menu')

@section('content')
    <div class="game-container">
        <h1 class="game-title">{{ $foro->titulo }}</h1>
        <div class="game-info">
            <p class="game-description">{{ $foro->descripcion }}</p>
        </div>

        <div class="game-genres">
            <h2>{{ __('Related Games') }}</h2>
            <ul>
                @forelse($foro->videojuegos as $videojuego)
                    <li>
                        <strong>{{ $videojuego->nombre }}</strong>
                        <br>
                        @if ($videojuego->multimedia->isNotEmpty())
                            <a style="background: none; border: none;cursor: pointer;" href="{{ route('videojuegos.show', $videojuego->id) }}"><img style="width:200px;position: relative; z-index: 2; padding-right: 20px; padding-bottom: 40px" class="imagenes" src="{{ asset($videojuego->multimedia->first()->url) }}" alt="{{ __('Image of') }} {{ $videojuego->nombre }}"/></a>
                        @else
                            <a style="background: none; border: none;cursor: pointer;" href="{{ route('videojuegos.show', $videojuego->id) }}"> {{ $videojuego->nombre }}</a>
                        @endif
                    </li>
                @empty
                    <li>{{ __('No related games for this forum.') }}</li>
                @endforelse
            </ul>
        </div>

        <div class="game-genres">
            <h2>{{ __('Messages') }}</h2>

            @foreach($foro->mensajes as $mensaje)
                <div>
                    <div>
                        <p>{{ $mensaje->contenido }}</p>
                        <small>{{ __('Posted by') }} {{ $mensaje->usuario->name }} el {{ $mensaje->created_at->format('d/m/Y H:i') }}</small>

                        <div>
                            <h5>{{ __('Replies') }}</h5>
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
                                    <textarea name="contenido" required style="color: black; margin-bottom: 20px" placeholder="{{ __('Reply to this message...') }}"></textarea>
                                    <input type="hidden" name="mensaje_id" value="{{ $mensaje->id }}">
                                </div>
                                <button type="submit">{{ __('Send Message') }}</button>
                            </form>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>

        @auth
            <div>
                <h3>{{ __('New Message') }}</h3>
                <form action="{{ route('mensajes.store', $foro->id) }}" method="POST">
                    @csrf
                    <div>
                        <textarea name="contenido" required style="color: black; margin-bottom: 20px" placeholder="{{ __('Write your message...') }}"></textarea>
                    </div>
                    <input type="hidden" name="foro_id" value="{{ $foro->id }}">
                    <button type="submit">{{ __('Send Message') }}</button>
                </form>
            </div>
                <a href="{{ route('forum.edit', $foro) }}" class="button fit" style="width: 250px; margin-top: 20px; margin-bottom: 20px ">{{ __('Edit') }}</a>
                <form action="{{ route('forum.destroy', $foro) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="width: 200px;">{{ __('Delete') }}</button>
                </form>

            <a href="{{ route('forum.pdf', $foro) }}" class="button fit" style="width: 250px; margin-top: 20px; margin-bottom: 20px ">
                {{ __('Download PDF') }}
            </a>
        @endauth
    </div>
@endsection

