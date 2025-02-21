@extends('layouts.app')
@section('title', 'GAME QUEST')
@section('body_class', 'is-preload')
@include('layouts.menu')

@section('content')
    <section id="banner" class="major" style="background-image: url('{{ asset('forty/images/Designer (4).jpeg') }}'); background-size: cover; background-position: center; height: 100vh;">
        <div class="inner">
            @guest
                <header class="major">
                    <h1>¡Hola jugador!</h1>
                </header>
                <div class="content">
                    <p>¡Encuentra tu próxima aventura hoy!</p>
                    <ul class="actions">
                        <li><a href="{{ route('register') }}" class="button next">Comienza tu aventura</a></li>
                    </ul>
                </div>
            @else
                <header class="major">
                    <h1>¡Hola, {{ Auth::user()->name }}!</h1>
                </header>
                <div class="content">
                    <p>¡Me alegra verte de nuevo por aquí, tienes que ver los nuevos juegos!</p>
                    <ul class="actions">
                        <li><a href="{{ route('videojuegos.index') }}" class="button next">Ver juegos</a></li>
                    </ul>
                </div>
            @endguest
        </div>
    </section>
    <section id="one" class="tiles">
        @foreach ($videojuegos as $videojuego)
            <article>
                <span>
                @if ($videojuego->multimedia->isNotEmpty())
                        <a style="background: none; border: none;cursor: pointer;" href="{{ route('videojuegos.show', $videojuego->id) }}"><img style="width:200px;position: relative; z-index: 2; padding-right: 20px; padding-bottom: 40px" class="imagenes" src="{{ asset($videojuego->multimedia->first()->url) }}" alt="Imagen de {{ $videojuego->nombre }}"/></a>
                    @else
                        <a style="background: none; border: none;cursor: pointer;" href="{{ route('videojuegos.show', $videojuego->id) }}"> {{ $videojuego->nombre }}</a>
                    @endif
            </span>
                <header class="major" style="position: relative; z-index: 2;">
                    <h3>{{ $videojuego->nombre }}</h3>
                    <p>{{ Str::limit($videojuego->descripcion, 70) }}</p>
                    <p>Gustos de los usuarios: {{ number_format($videojuego->rating_usuario, 1) }}</p>
                </header>
            </article>
        @endforeach
    </section>
        <section id="two">
            <div class="inner">
                <header class="major">
                    <h2>Comenta en el foro y habla con otros usuarios.!</h2>
                </header>
                <p>¡Bienvenido de nuevo! Estás listo para explorar aún más: sigue guardando tus juegos favoritos, participa en las discusiones del foro y conéctate con otros jugadores. Eres parte de una comunidad increíble y la aventura sigue mejorando
                </p>
                <ul class="actions">
                    <li><a href="{{ route('forum.index') }}" class="button next">Ir al foro</a></li>
                </ul>
            </div>
        </section>
@endsection
