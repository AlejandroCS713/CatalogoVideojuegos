@extends('layouts.app')
@section('title', 'GAME QUEST')
@section('body_class', 'is-preload')
@section('content')
    <section id="banner" class="major" style="background-image: url('{{ asset('forty/images/Designer (4).jpeg') }}'); background-size: cover; background-position: center; height: 100vh;">
        <div class="inner">
            @guest
                <header class="major">
                    <h1>{{ __('Hello, player!') }}</h1>
                </header>
                <div class="content">
                    <p>{{ __('Find your next adventure today!') }}</p>
                    <ul class="actions">
                        <li><a href="{{ route('register') }}" class="button next">{{ __('Start your adventure') }}</a></li>
                    </ul>
                </div>
            @else
                <header class="major">
                    <h1>{{ __('Hello, ') }}{{ Auth::user()->name }}!</h1>
                </header>
                <div class="content">
                    <p>{{ __('Glad to see you again, you have to check out the new games!') }}</p>
                    <ul class="actions">
                        <li><a href="{{ route('videojuegos.index') }}" class="button next">{{ __('View games') }}</a></li>
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
                        <a style="background: none; border: none;cursor: pointer;" href="{{ route('videojuegos.show', $videojuego->id) }}"><img style="width:200px;position: relative; z-index: 2; padding-right: 20px; padding-bottom: 40px" class="imagenes" src="{{ asset($videojuego->multimedia->first()->url) }}" alt="{{ __('Image of ') }}{{ $videojuego->nombre }}"/></a>
                    @else
                        <a style="background: none; border: none;cursor: pointer;" href="{{ route('videojuegos.show', $videojuego->id) }}"> {{ $videojuego->nombre }}</a>
                    @endif
            </span>
                <header class="major" style="position: relative; z-index: 2;">
                    <h3>{{ $videojuego->nombre }}</h3>
                    <p>{{ Str::limit($videojuego->descripcion, 70) }}</p>
                    <p>{{ __('User Likes: ') }}{{ number_format($videojuego->rating_usuario, 1) }}</p>
                </header>
            </article>
        @endforeach
    </section>
        <section id="two">
            <div class="inner">
                <header class="major">
                    <h2>{{ __('Comment on the forum and chat with other users!') }}</h2>
                </header>
                <p>{{ __('Welcome back! Are you ready to explore even more: keep saving your favorite games, participate in forum discussions, and connect with other players. You are part of an amazing community and the adventure keeps getting better!') }}
                </p>
                <ul class="actions">
                    <li><a href="{{ route('forum.index') }}" class="button next">{{ __('Go to forum') }}</a></li>
                </ul>
            </div>
        </section>
@endsection
