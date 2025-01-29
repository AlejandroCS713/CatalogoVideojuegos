@extends('layouts.app')
@section('title', 'GAME QUEST')

@section('body_class', 'is-preload') <!-- Clase para el body -->

@section('content')
    <!-- Header -->
    <!-- Menu -->

    @include('layouts.menu')




    <!-- Banner -->


    <section id="banner" class="major" style="background-image: url('{{ asset('forty/images/Designer (4).jpeg') }}'); background-size: cover; background-position: center; height: 100vh;">
        <div class="inner">
            <header class="major">
                <h1>¡Hola, gamer!</h1>
            </header>
            <div class="content">
                <p>¡Encuentra tu próxima aventura hoy!</p>
                <ul class="actions">
                    <li><a href="{{ route('register') }}" class="button next">Comienza tu aventura</a></li>
                </ul>
            </div>
        </div>
    </section>
<!-- Main -->
    <section id="one" class="tiles">

        @foreach ($videojuegos as $videojuego)
            <article>
                <!-- Imagen predeterminada como fondo -->
                <span>
                @if ($videojuego->multimedia->isNotEmpty())
                        <!-- Si hay multimedia, muestra la imagen relacionada como fondo -->
                        <img class="imagenes" src="{{ asset($videojuego->multimedia->first()->url) }}" alt="Imagen de {{ $videojuego->nombre }}"/>
                    @else

                    @endif
            </span>


                <header class="major" style="position: relative; z-index: 2;">
                    <h3>{{ $videojuego->nombre }}</h3>
                    <p>{{ Str::limit($videojuego->descripcion, 150) }}</p>
                    <p>Rating de usuarios: {{ number_format($videojuego->rating_usuario, 1) }}</p>
                </header>
            </article>
        @endforeach
    </section>





    <!-- Section Two -->
        <section id="two">
            <div class="inner">
                <header class="major">
                    <h2>Únete a nuestra comunidad de jugadores</h2>
                </header>
                <p>Crea tu cuenta y desbloquea la experiencia completa: guarda tus juegos favoritos, comparte tus opiniones en los foros y conecta con otros apasionados por los videojuegos. ¡Empieza a formar parte de algo épico hoy mismo!</p>
                <ul class="actions">
                    <li><a href="{{ route('register') }}" class="button next">Comienza tu aventura</a></li>
                </ul>
            </div>
        </section>


@endsection
