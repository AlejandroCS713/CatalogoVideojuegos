<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $foro->titulo }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1, h2, h3 { color: #333; }
        .mensaje { margin-bottom: 15px; padding: 10px; border: 1px solid #ddd; }
        .respuesta { margin-left: 20px; font-size: 14px; color: #555; }
    </style>
</head>
<body>
<h1>{{ $foro->titulo }}</h1>
<p>{{ $foro->descripcion }}</p>

<h2>Videojuegos Relacionados:</h2>
<ul>
    @foreach($foro->videojuegos as $videojuego)
        <li>
            <strong>{{ $videojuego->nombre }}</strong><br>
            @if ($videojuego->multimedia->isNotEmpty())
                @php
                    $imageUrl = $videojuego->multimedia->first()->url;
                    $imageData = @file_get_contents($imageUrl);
                @endphp
                @if ($imageData)
                    <img src="data:image/jpeg;base64,{{ base64_encode($imageData) }}"
                         alt="Imagen de {{ $videojuego->nombre }}"
                         style="width:150px; height:auto; display:block; margin-top:10px;">
                @else
                    <p>(Imagen no disponible)</p>
                @endif
            @endif
        </li>
    @endforeach
</ul>

<h2>Mensajes:</h2>
@foreach($foro->mensajes as $mensaje)
    <div class="mensaje">
        <p><strong>{{ $mensaje->usuario->name }}</strong>: {{ $mensaje->contenido }}</p>
        <small>Publicado el {{ $mensaje->created_at->format('d/m/Y H:i') }}</small>

        @if($mensaje->respuestas->count())
            <h4>Respuestas:</h4>
            @foreach($mensaje->respuestas as $respuesta)
                <div class="respuesta">
                    <p><strong>{{ $respuesta->usuario->name }}</strong>: {{ $respuesta->contenido }}</p>
                    <small>Respondido el {{ $respuesta->created_at->format('d/m/Y H:i') }}</small>
                </div>
            @endforeach
        @endif
    </div>
@endforeach
</body>
</html>
