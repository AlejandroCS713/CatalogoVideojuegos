@extends('layouts.app')
@section('title', __('Update Game'))
@section('body_class', 'is-preload videojuegos-bg')
@include('layouts.menu')
@section('content')
    <div class="form-container">
        <div class="form-card">
            <h2 class="form-title">{{ __('Edit Game') }}</h2>

            <form action="{{ route('admin.update', $videojuego->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">{{ __('Name') }}</label>
                    <input type="text" name="nombre" class="form-input" value="{{ $videojuego->nombre }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('Description') }}:</label>
                    <textarea name="descripcion" class="form-textarea">{{ $videojuego->descripcion }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('Release Date') }}:</label>
                    <input type="date" name="fecha_lanzamiento" class="form-input" value="{{ $videojuego->fecha_lanzamiento }}">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('Developer') }}:</label>
                    <input type="text" name="desarrollador" class="form-input" value="{{ $videojuego->desarrollador }}">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('Publisher') }}:</label>
                    <input type="text" name="publicador" class="form-input" value="{{ $videojuego->publicador }}">
                </div>

                <div class="form-group">
                    <label class="form-label">{{ __('Platforms') }}:</label>
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
                    <label class="form-label">{{ __('Genres') }}:</label>
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
                    <button type="submit" class="form-button">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection
