@php use Illuminate\Support\Str; @endphp
<div>
    <input type="text" wire:model.live.debounce.100ms="searchTerm"
           placeholder="{{ __('Search video game...') }}" class="form-control"
           style="color: black; margin-bottom: 10px;">

    @if(!empty($resultadosBusqueda) && strlen($searchTerm) >= 2)
        <div class="videojuegos-group2" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; margin-bottom: 10px;">
            <div class="videojuegos-grid2">
                @forelse ($resultadosBusqueda as $videojuego)
                    <div class="videojuego-card2" wire:click="seleccionarVideojuego({{ $videojuego->id }})" style="cursor: pointer; padding: 5px; border-bottom: 1px solid #eee; display: flex; align-items: center;">
                        @php
                            $imagenUrl = $videojuego->multimedia->where('tipo', 'imagen')->first()?->url;
                            $imagen = $imagenUrl
                                ? (Str::startsWith($imagenUrl, ['http://', 'https://']) ? $imagenUrl : asset($imagenUrl))
                                : asset('images/default-game.jpeg');
                        @endphp
                        <img style="width: 50px; height: auto; vertical-align: middle; margin-right: 10px;"
                             class="imagenes"
                             src="{{ $imagen }}"
                             alt="{{ __('Image of') }} {{ $videojuego->nombre }}"
                             onerror="this.onerror=null; this.src='{{ asset('images/default-game.jpeg') }}';"
                        />
                        <span>{{ $videojuego->nombre }}</span>
                    </div>
                @empty
                    <div style="padding: 10px; color: gray;">{{ __('No games found matching your search.') }}</div>
                @endforelse
            </div>
        </div>
    @elseif(strlen($searchTerm) >= 2)
        <div style="padding: 10px; color: gray;">{{ __('No games found matching your search.') }}</div>
    @endif

    @if (!empty($videojuegosConRol))
        <h3 style="margin-top: 15px; font-size: 1.1em;">{{ __('Selected Games') }}</h3>
        <div class="videojuegos-seleccionados" style="margin-bottom: 15px;">
            @foreach ($videojuegosConRol as $videojuegoId => $rol)
                @php
                    $videojuego = $videojuegosSeleccionadosModels->get($videojuegoId);
                @endphp
                @if ($videojuego)
                    <div class="seleccionado-item" style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px; background-color: #444; padding: 8px; border-radius: 4px;">
                        <span style="flex-grow: 1; color: white;">{{ $videojuego->nombre }}</span>
                        <select wire:model.live="videojuegosConRol.{{ $videojuegoId }}" style="color: black; padding: 5px; border-radius: 3px;">
                            <option value="principal">{{ __('Main') }}</option>
                            <option value="secundario">{{ __('Secondary') }}</option>
                            <option value="opcional">{{ __('Optional') }}</option>
                        </select>
                        <button type="button" wire:click="eliminarVideojuego({{ $videojuegoId }})"
                                title="{{ __('Remove') }}"
                                style="color: red; border: none; background: none; cursor: pointer; font-size: 1.3em; line-height: 1; padding: 0 5px;">
                            &times;
                        </button>
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
