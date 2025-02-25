<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información del Proyecto</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

<nav class="bg-blue-600 text-white p-4 shadow-md sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-xl font-bold">Game Quest</h1>
        <ul class="flex space-x-4">
            <li><a href="{{ route('welcome') }}" class="transition-colors duration-1000 text-gray-800 hover:text-white hover:underline" aria-label="Ir a la página de inicio">Inicio</a></li>
            <li><a href="{{ route('videojuegos.index') }}" class="transition-colors duration-1000 text-gray-800 hover:text-white hover:underline" aria-label="Ir al catálogo de videojuegos">Catálogo</a></li>
            <li><a href="{{ route('forum.index') }}" class="transition-colors duration-1000 text-gray-800 hover:text-white hover:underline" aria-label="Ir al foro de la comunidad">Foro</a></li>
            <li><a href="{{ route('register') }}" class="transition-colors duration-1000 text-gray-800 hover:text-white hover:underline" aria-label="Ir a la página de registro">Registro</a></li>
        </ul>
    </div>
</nav>

<header class="bg-blue-500 text-white text-center py-20">
    <h2 class="text-4xl font-extrabold">Bienvenido a nuestro Proyecto</h2>
    <p class="mt-4 text-lg">Descubre el catálogo de videojuegos, únete a la comunidad y disfruta de todas las ventajas.</p>
</header>

<section id="catalogo" class="container mx-auto my-12 p-6">
    <h3 class="text-3xl font-bold text-center mb-6">Catálogo de Videojuegos</h3>
    <p class="text-center text-gray-700 mb-6">Explora nuestro catálogo con una amplia variedad de títulos.</p>

    <div class="grid md:grid-cols-3 gap-6">
        @foreach ($videojuegos as $videojuego)
            <div class="bg-white p-4 rounded-lg shadow-md flex flex-col items-center">
                <div class="flex flex-col items-center justify-between md:flex-row md:items-start md:justify-start w-full">
                    @if ($videojuego->multimedia->isNotEmpty())
                        <a href="{{ route('videojuegos.show', $videojuego->id) }}" class="w-full md:w-64 h-auto">
                            <img src="{{ asset($videojuego->multimedia->first()->url) }}"
                                 alt="Imagen de {{ $videojuego->nombre }}"
                                 class="w-full h-auto rounded-lg m-1 transition-transform duration-300 transform hover:scale-105">
                        </a>
                    @else
                        <div class="w-full md:w-64 h-auto bg-gray-300 rounded-lg m-1 flex items-center justify-center">
                            <span class="text-gray-600">Sin imagen</span>
                        </div>
                    @endif

                    <div class="mt-4 md:mt-0 md:ml-4 flex flex-col items-center md:items-start">
                        <h4 class="text-xl font-semibold text-center md:text-left">{{ $videojuego->nombre }}</h4>
                        <p class="text-gray-600 text-center md:text-left">{{ Str::limit($videojuego->descripcion ?? 'Descripción no disponible.', 100) }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>


<section id="foro" class="bg-gray-200 py-12">
    <div class="container mx-auto p-6">
        <h3 class="text-3xl font-bold text-center mb-6">Foro de la Comunidad</h3>
        <p class="text-center text-gray-700 mb-6">Participa en debates y comparte tu experiencia con otros jugadores.</p>

        <!-- Contenedor del video -->
        <div class="flex justify-center">
            <video class="w-full max-w-2xl rounded-lg shadow-lg" controls>
                <source src="{{ asset('forty/images/oso.mp4') }}" type="video/mp4">
                Tu navegador no soporta la reproducción de video.
            </video>
        </div>
    </div>
</section>
<section id="registro" class="container mx-auto my-12 p-6">
    <h3 class="text-3xl font-bold text-center mb-6">Regístrate y disfruta de más ventajas</h3>
    <form class="bg-white p-6 rounded-lg shadow-md max-w-lg mx-auto" action="{{ route('register') }}" method="POST" autocomplete="off">
        @csrf
        <label for="nombre" class="block mb-2 text-gray-700">Nombre</label>
        <input type="text" id="nombre" name="name" class="w-full p-2 border rounded mb-4" placeholder="Ingresa tu nombre" aria-describedby="nombre-ayuda" value="{{ old('name') }}" required>
        <span id="nombre-ayuda" class="text-sm text-gray-500">Ingresa tu nombre completo.</span>
        @error('name')
        <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        <label for="email" class="block mb-2 text-gray-700">Correo Electrónico</label>
        <input type="email" id="email" name="email" class="w-full p-2 border rounded mb-4" placeholder="Ingresa tu correo" aria-describedby="email-ayuda" value="{{ old('email') }}" required>
        <span id="email-ayuda" class="text-sm text-gray-500">Escribe un correo electrónico válido.</span>
        @error('email')
        <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        <label for="contraseña" class="block mb-2 text-gray-700">Contraseña</label>
        <input type="password" id="contraseña" name="password" class="w-full p-2 border rounded mb-4" placeholder="Ingresa tu contraseña" aria-describedby="contraseña-ayuda" required>
        <span id="contraseña-ayuda" class="text-sm text-gray-500">Debe tener al menos 8 caracteres.</span>
        @error('password')
        <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        <label for="confirmar-contraseña" class="block mb-2 text-gray-700">Confirmar Contraseña</label>
        <input type="password" id="confirmar-contraseña" name="password_confirmation" class="w-full p-2 border rounded mb-4" placeholder="Confirma tu contraseña" aria-describedby="confirmar-contraseña-ayuda" required>
        <span id="confirmar-contraseña-ayuda" class="text-sm text-gray-500">Asegúrate de que las contraseñas coincidan.</span>
        @error('password_confirmation')
        <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror

        <button class="bg-blue-600 text-white px-4 py-2 rounded w-full transition-all duration-500 transform hover:bg-blue-700 hover:scale-105 hover:shadow-lg" aria-label="Registrarse en el sitio">Registrarse</button>
    </form>
</section>

<footer class="bg-blue-600 text-white text-center p-4 mt-12">
    <p>&copy; 2025 Game Quest - Todos los derechos reservados</p>
</footer>
</body>
</html>
