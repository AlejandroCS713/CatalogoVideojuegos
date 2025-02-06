@extends('layouts.app')
@section('title', 'GAME QUEST')
@section('body_class', 'is-preload') <!-- Clase para el body -->
@include('layouts.menu')

@section('content')
    <!-- Header -->
    <!-- Menu -->



    <!-- Banner -->
    <section id="banner" class="major" style="background-image: url('{{ asset('forty/images/Designer (4).jpeg') }}'); background-size: cover; background-position: center; height: 100vh;">
        <div class="inner">
            <header class="major">
                <h1>¡Hello, player!</h1>
            </header>
            <div class="content">
                <p>¡Find your next adventure today!</p>
                <ul class="actions">
                    <li><a href="{{ route('register') }}" class="button next">Start your adventure</a></li>
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
                        <img style="width:200px;position: relative; z-index: 2; padding-right: 20px; padding-bottom: 40px" class="imagenes" src="{{ asset($videojuego->multimedia->first()->url) }}" alt="Imagen de {{ $videojuego->nombre }}"/>
                    @else

                    @endif
            </span>
                <header class="major" style="position: relative; z-index: 2;">
                    <h3>{{ $videojuego->nombre }}</h3>
                    <p>{{ Str::limit($videojuego->descripcion, 70) }}</p>
                    <p>Rating de usuarios: {{ number_format($videojuego->rating_usuario, 1) }}</p>
                </header>
            </article>
        @endforeach
    </section>

    <!-- Section Two -->
        <section id="two">
            <div class="inner">
                <header class="major">
                    <h2>Join our community of players</h2>
                </header>
                <p>Create your account and unlock the full experience: save your favorite games, share your opinions in the forums and connect with other gaming enthusiasts. Start being part of something epic today!
                </p>
                <ul class="actions">
                    <li><a href="{{ route('register') }}" class="button next">Start your adventure</a></li>
                </ul>
            </div>
        </section>


@endsection
