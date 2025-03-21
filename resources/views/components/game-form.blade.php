@props(['action', 'method' => 'POST', 'videojuego' => null, 'plataformas', 'generos'])

<form action="{{ $action }}" method="POST">
    @csrf
    @if($method === 'PUT')
        @method('PUT')
    @endif

    <div class="form-group">
        <label class="form-label">{{ __('Name') }}</label>
        <input type="text" name="nombre" class="form-input" value="{{ old('nombre', $videojuego->nombre ?? '') }}" required>
    </div>

    <div class="form-group">
        <label class="form-label">{{ __('Description') }}</label>
        <textarea name="descripcion" class="form-textarea">{{ old('descripcion', $videojuego->descripcion ?? '') }}</textarea>
    </div>

    <div class="form-group">
        <label class="form-label">{{ __('Release Date') }}</label>
        <input type="date" name="fecha_lanzamiento" class="form-input" value="{{ old('fecha_lanzamiento', $videojuego->fecha_lanzamiento ?? '') }}">
    </div>

    <div class="form-group">
        <label class="form-label">{{ __('Developer') }}</label>
        <input type="text" name="desarrollador" class="form-input" value="{{ old('desarrollador', $videojuego->desarrollador ?? '') }}">
    </div>

    <div class="form-group">
        <label class="form-label">{{ __('Publisher') }}</label>
        <input type="text" name="publicador" class="form-input" value="{{ old('publicador', $videojuego->publicador ?? '') }}">
    </div>

    <div class="form-group">
        <label class="form-label">{{ __('Platforms') }}</label>
        <select name="plataformas[]" multiple class="form-select">
            @foreach($plataformas as $plataforma)
                <option value="{{ $plataforma->id }}"
                    {{ isset($videojuego) && $videojuego->plataformas->contains($plataforma->id) ? 'selected' : '' }}>
                    {{ $plataforma->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label class="form-label">{{ __('Genres') }}</label>
        <select name="generos[]" multiple class="form-select">
            @foreach($generos as $genero)
                <option value="{{ $genero->id }}"
                    {{ isset($videojuego) && $videojuego->generos->contains($genero->id) ? 'selected' : '' }}>
                    {{ $genero->nombre }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <button type="submit" class="form-button">{{ $method === 'PUT' ? __('Update') : __('Save') }}</button>
    </div>
</form>
