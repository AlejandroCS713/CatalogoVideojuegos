<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <title>@yield('title', 'Forty Template')</title> <!-- Título dinámico -->

    <!-- Estilos comunes -->
    <link rel="stylesheet" href="{{ asset('forty/assets/css/main.css') }}" />
    <noscript><link rel="stylesheet" href="{{ asset('forty/assets/css/noscript.css') }}" /></noscript>

    <!-- Otros estilos adicionales si es necesario -->
    @stack('styles') <!-- Para agregar estilos específicos desde las vistas -->
</head>
<body class="@yield('body_class', 'is-preload')"> <!-- Clases dinámicas para el body -->
<!-- Wrapper -->
<div id="wrapper">
    @yield('content') <!-- Contenido específico de cada página -->
</div>

<!-- Scripts comunes -->
<script src="{{ asset('forty/assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('forty/assets/js/browser.min.js') }}"></script>
<script src="{{ asset('forty/assets/js/breakpoints.min.js') }}"></script>
<script src="{{ asset('forty/assets/js/util.js') }}"></script>
<script src="{{ asset('forty/assets/js/main.js') }}"></script>
<script src="{{ asset('forty/assets/js/jquery.scrollex.min.js') }}"></script>
<script src="{{ asset('forty/assets/js/jquery.scrolly.min.js') }}"></script>

<!-- Scripts adicionales si es necesario -->
@stack('scripts') <!-- Para agregar scripts específicos desde las vistas -->
</body>
</html>
