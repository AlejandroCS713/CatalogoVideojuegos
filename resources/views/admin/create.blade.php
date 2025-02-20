@extends('layouts.app')
@section('title', ' Crear Juego ')
@section('body_class', 'is-preload videojuegos-bg')
@include('layouts.menu')

@section('content')

    <h2 class="form-title">Crear Nuevo Videojuego</h2>
    <div class="form-container">
        <div class="">
            <form action="{{ route('admin.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Nombre:</label>
                    <input type="text" name="nombre" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Descripción:</label>
                    <textarea name="descripcion" class="form-textarea"></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Fecha de Lanzamiento:</label>
                    <input type="date" name="fecha_lanzamiento" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Desarrollador:</label>
                    <input type="text" name="desarrollador" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Publicador:</label>
                    <input type="text" name="publicador" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Plataformas:</label>
                    <select name="plataformas[]" multiple class="form-select">
                        @foreach($plataformas as $plataforma)
                            <option value="{{ $plataforma->id }}">{{ $plataforma->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Géneros:</label>
                    <select name="generos[]" multiple class="form-select">
                        @foreach($generos as $genero)
                            <option value="{{ $genero->id }}">{{ $genero->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
