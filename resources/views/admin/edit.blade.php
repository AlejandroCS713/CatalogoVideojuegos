@extends('layouts.app')
@section('title', ' Actualizar juego ')
@section('body_class', 'is-preload videojuegos-bg')
@include('layouts.menu')
@section('content')
    <div class="container">
        <h1>Editar Videojuego</h1>
        <form action="{{ route('admin.update', $videojuego->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label>Nombre:</label>
            <input type="text" name="nombre" class="form-control" value="{{ $videojuego->nombre }}" required>

            <label>Descripción:</label>
            <textarea name="descripcion" class="form-control">{{ $videojuego->descripcion }}</textarea>

            <label>Fecha de Lanzamiento:</label>
            <input type="date" name="fecha_lanzamiento" class="form-control" value="{{ $videojuego->fecha_lanzamiento }}">

            <label>Desarrollador:</label>
            <input type="text" name="desarrollador" class="form-control" value="{{ $videojuego->desarrollador }}">

            <label>Publicador:</label>
            <input type="text" name="publicador" class="form-control" value="{{ $videojuego->publicador }}">

            <label>Plataformas:</label>
            <select name="plataformas[]" multiple class="form-control">
                @foreach($plataformas as $plataforma)
                    <option value="{{ $plataforma->id }}"
                        {{ $videojuego->plataformas->contains($plataforma->id) ? 'selected' : '' }}>
                        {{ $plataforma->nombre }}
                    </option>
                @endforeach
            </select>

            <label>Géneros:</label>
            <select name="generos[]" multiple class="form-control">
                @foreach($generos as $genero)
                    <option value="{{ $genero->id }}"
                        {{ $videojuego->generos->contains($genero->id) ? 'selected' : '' }}>
                        {{ $genero->nombre }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn btn-primary mt-3">Actualizar</button>
        </form>
    </div>
@endsection
