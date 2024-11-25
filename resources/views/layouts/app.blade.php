<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <title>@yield('title', 'Forty Template')</title> <!-- Título dinámico -->

    <!-- Estilos comunes -->
    <link rel="stylesheet" href="{{ asset('forty/css/main.css') }}" />
    <noscript><link rel="stylesheet" href="{{ asset('forty/css/noscript.css') }}" /></noscript>

    <!-- Otros estilos adicionales si es necesario -->
    @stack('styles') <!-- Para agregar estilos específicos desde las vistas -->
</head>
<body class="@yield('body_class', 'is-preload')"> <!-- Clases dinámicas para el body -->
<!-- Wrapper -->
<div id="wrapper">
    @yield('content') <!-- Contenido específico de cada página -->
</div>

<!-- Scripts comunes -->
<script src="{{ asset('forty/js/jquery.min.js') }}"></script>
<script src="{{ asset('forty/js/browser.min.js') }}"></script>
<script src="{{ asset('forty/js/breakpoints.min.js') }}"></script>
<script src="{{ asset('forty/js/util.js') }}"></script>
<script src="{{ asset('forty/js/main.js') }}"></script>
<script src="{{ asset('forty/js/jquery.scrollex.min.js') }}"></script>
<script src="{{ asset('forty/js/jquery.scrolly.min.js') }}"></script>

<!-- Scripts adicionales si es necesario -->
@stack('scripts') <!-- Para agregar scripts específicos desde las vistas -->
</body>
</html>
