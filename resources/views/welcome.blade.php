@extends('layouts.app') <!-- Extiende el layout base -->

@section('title', 'Bienvenido a Forty') <!-- Título específico para esta vista -->

@section('body_class', 'is-preload') <!-- Clase específica para el body, si es necesario -->

@section('content')
    <!-- Contenido específico de la página -->
    <section id="main">
        <header>
            <span class="avatar"><img src="{{ asset('forty/images/avatar.jpg') }}" alt="" /></span>
            <h1>Bienvenido a Forty</h1>
            <p>Plantilla implementada con Laravel</p>
        </header>

        <footer>
            <ul class="icons">
                <li><a href="#" class="icon brands fa-twitter"><span class="label">Twitter</span></a></li>
                <li><a href="#" class="icon brands fa-facebook-f"><span class="label">Facebook</span></a></li>
                <li><a href="#" class="icon brands fa-instagram"><span class="label">Instagram</span></a></li>
                <li><a href="#" class="icon brands fa-github"><span class="label">GitHub</span></a></li>
            </ul>
        </footer>
    </section>

    <footer id="footer">
        <p>&copy; Forty by HTML5 UP. Adaptado para Laravel.</p>
    </footer>
@endsection
