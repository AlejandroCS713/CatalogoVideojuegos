@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Crear Nuevo Videojuego</h1>
        <form action="{{ route('videojuegos.store') }}" method="POST">
            @csrf

            <label>Nombre:</label>
            <input type="text" name="nombre" class="form-control" required>

            <label>Descripción:</label>
            <textarea name="descripcion" class="form-control"></textarea>

            <label>Fecha de Lanzamiento:</label>
            <input type="date" name="fecha_lanzamiento" class="form-control">

            <label>Desarrollador:</label>
            <input type="text" name="desarrollador" class="form-control">

            <label>Publicador:</label>
            <input type="text" name="publicador" class="form-control">

            <label>Plataformas:</label>
            <select name="plataformas[]" multiple class="form-control">
                @foreach($plataformas as $plataforma)
                    <option value="{{ $plataforma->id }}">{{ $plataforma->nombre }}</option>
                @endforeach
            </select>

            <label>Géneros:</label>
            <select name="generos[]" multiple class="form-control">
                @foreach($generos as $genero)
                    <option value="{{ $genero->id }}">{{ $genero->nombre }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary mt-3">Guardar</button>
        </form>
    </div>
@endsection
