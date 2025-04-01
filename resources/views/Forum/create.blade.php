@extends('layouts.app')
@section('title', __('Create Forum'))

@section('content')
    <h1>{{ __('Create Forum') }}</h1>
    <div class="game-container" style=" display: flex;justify-content: center; margin-bottom: 40px;">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('forum.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <p>{{ __('Title') }}</p>
                <input style="color: black; margin-bottom: 20px;" type="text" name="titulo" id="titulo"  class="form-control" value="{{ old('titulo') }}" required >
                @error('titulo')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <p>{{ __('Description') }}</p>
                <textarea name="descripcion" id="descripcion" class="form-control" rows="5" required style="color: black; margin-bottom: 20px;">{{ old('descripcion') }}</textarea>
                @error('descripcion')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div style="margin-bottom: 50px">
                <label for="videojuegos">{{ __('Select Games') }}</label>
                @livewire('buscar-videojuego')
            </div>

            <div class="form-group">
                <label for="rol_videojuego">{{ __('Role of the Video Game in Forum') }}</label>
                <select name="rol_videojuego" id="rol_videojuego" class="form-control" style="color: black">
                    <option value="principal" {{ old('rol_videojuego') === 'principal' ? 'selected' : '' }}>{{ __('Main') }}</option>
                    <option value="secundario" {{ old('rol_videojuego') === 'secundario' ? 'selected' : '' }}>{{ __('Secondary') }}</option>
                    <option value="opcional" {{ old('rol_videojuego') === 'opcional' ? 'selected' : '' }}>{{ __('Optional') }}</option>
                </select>
                @error('rol_videojuego')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" >{{ __('Create') }}</button>
        </form>
    </div>
@endsection

