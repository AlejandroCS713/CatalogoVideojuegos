<!DOCTYPE html>
<html>
<head>
    <title>@yield('title', 'Forty Template')</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <link rel="stylesheet" href="{{ asset('forty/css/main.css') }}" />
    <noscript><link rel="stylesheet" href="{{ asset('forty/css/noscript.css') }}" /></noscript>
</head>
<body class="@yield('body_class', 'is-preload')">
<!-- Wrapper -->
<div id="wrapper">
    @yield('content')
</div>

<!-- Scripts -->
<script src="{{ asset('forty/js/jquery.min.js') }}"></script>
<script src="{{ asset('forty/js/browser.min.js') }}"></script>
<script src="{{ asset('forty/js/breakpoints.min.js') }}"></script>
<script src="{{ asset('forty/js/util.js') }}"></script>
<script src="{{ asset('forty/js/main.js') }}"></script>
</body>
</html>
