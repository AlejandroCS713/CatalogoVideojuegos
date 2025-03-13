@extends('layouts.app')
@section('title', __('Update Forum'))

@section('content')
    <h1>{{ __('Update Forum') }}</h1>

    <div class="game-container" style="display: flex; justify-content: center; margin-bottom: 40px;">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

            <form action="{{ route('forum.update', $foro) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <p>{{ __('Title') }}</p>
                <input
                    style="color: black; margin-bottom: 20px;"
                    type="text"
                    name="titulo"
                    id="titulo"
                    class="form-control"
                    value="{{ old('titulo', $foro->titulo) }}"
                    required
                >
                @error('titulo')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <p>{{ __('Description') }}</p>
                <textarea
                    name="descripcion"
                    id="descripcion"
                    class="form-control"
                    rows="5"
                    required
                    style="color: black; margin-bottom: 20px;"
                >{{ old('descripcion', $foro->descripcion) }}</textarea>
                @error('descripcion')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div style="margin-bottom: 50px">
                <label for="videojuego_id">{{ __('Select a Game') }}</label>
                @livewire('buscar-videojuego')
                <input
                    type="hidden"
                    name="videojuego_id"
                    id="videojuego_id"
                    wire:model="videojuego_id"
                    value="{{ old('videojuego_id', $foro->videojuego_id) }}"
                >
            </div>
            <button type="submit">{{ __('Update') }}</button>
        </form>
    </div>
@endsection

