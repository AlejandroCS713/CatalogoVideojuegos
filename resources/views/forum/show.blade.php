@extends('layouts.app')
@section('title', 'Forum')
@include('layouts.menu')

@section('content')
    <div class="container">
        <h1>{{ $foro->titulo }}</h1>
        <p>{{ $foro->descripcion }}</p>

        <h3>Mensajes en este foro:</h3>

        @foreach($foro->mensajes as $mensaje)
            <div class="card mb-3">
                <div class="card-body">
                    <p class="card-text">{{ $mensaje->contenido }}</p>
                    <small class="text-muted">Publicado por {{ $mensaje->usuario->name }} el {{ $mensaje->created_at }}</small>

                    <h5>Respuestas:</h5>
                    @foreach($mensaje->respuestas as $respuesta)
                        <div class="ml-3">
                            <p>{{ $respuesta->contenido }}</p>
                            <small class="text-muted">- {{ $respuesta->usuario->name }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <form action="{{ route('mensajes.store', $foro->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="contenido">Nuevo Mensaje:</label>
                <textarea class="form-control" name="contenido" required></textarea>
            </div>
            <button type="submit" class="btn btn-success mt-2">Enviar</button>
        </form>
    </div>
@endsection
