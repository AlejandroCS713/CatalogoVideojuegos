@extends('layouts.app')
@section('title', ' Actualizar juego ')
@section('body_class', 'is-preload videojuegos-bg')
@include('layouts.menu')
@section('content')
    <div class="form-container">
        <div class="form-card">
            <h2 class="form-title">Editar Videojuego</h2>

            <form action="{{ route('admin.update', $videojuego->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">Nombre:</label>
                    <input type="text" name="nombre" class="form-input" value="{{ $videojuego->nombre }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción:</label>
                    <textarea name="descripcion" class="form-textarea">{{ $videojuego->descripcion }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha de Lanzamiento:</label>
                    <input type="date" name="fecha_lanzamiento" class="form-input" value="{{ $videojuego->fecha_lanzamiento }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Desarrollador:</label>
                    <input type="text" name="desarrollador" class="form-input" value="{{ $videojuego->desarrollador }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Publicador:</label>
                    <input type="text" name="publicador" class="form-input" value="{{ $videojuego->publicador }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Plataformas:</label>
                    <select name="plataformas[]" multiple class="form-select">
                        @foreach($plataformas as $plataforma)
                            <option value="{{ $plataforma->id }}"
                                {{ $videojuego->plataformas->contains($plataforma->id) ? 'selected' : '' }}>
                                {{ $plataforma->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Géneros:</label>
                    <select name="generos[]" multiple class="form-select">
                        @foreach($generos as $genero)
                            <option value="{{ $genero->id }}"
                                {{ $videojuego->generos->contains($genero->id) ? 'selected' : '' }}>
                                {{ $genero->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="form-button">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
