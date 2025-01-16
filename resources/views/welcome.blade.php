@extends('layouts.app') <!-- Usa el layout base -->

@section('title', 'Forty by HTML5 UP') <!-- Título dinámico -->

@section('body_class', 'is-preload') <!-- Clase para el body -->

@section('content')
    <!-- Header -->
    <header id="header" class="alt">
        <a href="{{ route('welcome') }}" class="logo"><strong>GameQuest</strong></a>
        <nav>
            <a href="#menu">Menu</a>
        </nav>
    </header>

    <!-- Menu -->
    <nav id="menu">
        <ul class="links">
            <li><a href="{{ route('welcome') }}">Home</a></li>
            <li><a href="#one">Features</a></li>
        </ul>
        <ul class="actions stacked">
            <li><a href="#" class="button primary fit">Get Started</a></li>
            <li><a href="#" class="button fit">Log In</a></li>
        </ul>
    </nav>

    <!-- Banner -->

    <section id="banner" class="major" style="background-image: url('{{ asset('forty/images/Designer (4).jpeg') }}'); background-size: cover; background-position: center; height: 100vh;">
        <div class="inner">
            <header class="major">
                <h1>¡Hola, gamer!</h1>
            </header>
            <div class="content">
                <p>¡Encuentra tu próxima aventura hoy!</p>
                <ul class="actions">
                    <li><a href="#one" class="button next scrolly">Get Started</a></li>
                </ul>
            </div>
        </div>
    </section>
<!-- Main -->
    <div id="main">
        <!-- Section One -->
        <section id="one" class="tiles">
            @foreach ([1, 2, 3, 4, 5, 6] as $i)
                <article>
                    <span class="image">
                        <img src="{{ asset('forty/images/pic0'.$i.'.jpg') }}" alt="" />
                    </span>
                    <header class="major">
                        <h3><a href="#" class="link">Heading {{ $i }}</a></h3>
                        <p>Description {{ $i }}</p>
                    </header>
                </article>
            @endforeach
        </section>

        <!-- Section Two -->
        <section id="two">
            <div class="inner">
                <header class="major">
                    <h2>Massa libero</h2>
                </header>
                <p>Nullam et orci eu lorem consequat tincidunt vivamus et sagittis libero. Mauris aliquet magna magna sed
                    nunc rhoncus pharetra. Pellentesque condimentum sem. In efficitur ligula tate urna.</p>
                <ul class="actions">
                    <li><a href="#" class="button next">Get Started</a></li>
                </ul>
            </div>
        </section>
    </div>

@endsection
