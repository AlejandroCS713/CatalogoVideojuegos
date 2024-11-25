<!DOCTYPE html>
<html>
<head>
    <title>Forty Template</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('forty/css/main.css') }}" />
    <noscript><link rel="stylesheet" href="{{ asset('forty/css/noscript.css') }}" /></noscript>
</head>
<body class="is-preload">

<!-- Wrapper -->
<div id="wrapper">

    <!-- Main -->
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

    <!-- Footer -->
    <footer id="footer">
        <p>&copy; Forty by HTML5 UP. Adaptado para Laravel.</p>
    </footer>

</div>

<!-- Scripts -->
<script src="{{ asset('forty/js/jquery.min.js') }}"></script>
<script src="{{ asset('forty/js/browser.min.js') }}"></script>
<script src="{{ asset('forty/js/breakpoints.min.js') }}"></script>
<script src="{{ asset('forty/js/util.js') }}"></script>
<script src="{{ asset('forty/js/main.js') }}"></script>
<script src="{{ asset('forty/js/jquery.scrollex.min.js') }}"></script>
<script src="{{ asset('forty/js/jquery.scrolly.min.js') }}"></script>
</body>
</html>
