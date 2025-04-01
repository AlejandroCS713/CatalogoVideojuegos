<div>
    <input type="text" wire:model.live.debounce.100ms="searchTerm"
    wire:keydown="search"
           placeholder="{{ __('Search video game...') }}" class="form-control"
           style="color: black; margin-bottom: 10px;">

    @if(!empty($videojuegos) && strlen($searchTerm) >= 2)
    <div class="videojuegos-group2" style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; margin-bottom: 10px;">
        <div class="videojuegos-grid2">
            @forelse ($videojuegos as $videojuego)
                <div class="videojuego-card2" wire:click="seleccionarVideojuego({{ $videojuego->id }})" style="cursor: pointer; padding: 5px; border-bottom: 1px solid #eee;">
                    @php
                        $imagen = $videojuego->multimedia->isNotEmpty()
                            ? asset($videojuego->multimedia->first()->url)
                            : asset('images/default-game.png');
                    @endphp

                    <img style="width: 50px; height: auto; vertical-align: middle; margin-right: 10px;"
                    class="imagenes"
                         src="{{ $imagen }}"
                         alt="{{ __('Image of') }} {{ $videojuego->nombre }}"
                         onerror="this.onerror=null; this.src='{{ asset('images/default-game.png') }}';"
                    />
                    <span>{{ $videojuego->nombre }}</span>
                </div>
            @empty
                <div style="padding: 10px; color: gray;">{{ __('No games found matching your search.') }}</div>
            @endforelse
        </div>
    </div>
    @endif

    @if (!empty($videojuegosSeleccionados))
        <h3 style="margin-top: 15px; font-size: 1.1em;">{{ __('Selected Games') }}</h3>
        <div class="videojuegos-seleccionados" style="margin-bottom: 15px;">
            @foreach ($videojuegosSeleccionados as $videojuegoId)
                @php
                    $videojuego = App\Models\games\Videojuego::find($videojuegoId);
                @endphp
                @if ($videojuego)
                <div class="seleccionado-item" style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px; background-color: #f0f0f0; padding: 5px; border-radius: 4px;">
                    <span style="flex-grow: 1;">{{ $videojuego->nombre }}</span>
                    <button type="button" wire:click="eliminarVideojuego({{ $videojuegoId }})"
                            title="{{ __('Remove') }}"
                            style="color: red; border: none; background: none; cursor: pointer; font-size: 1.2em; line-height: 1;">
                        &times;
                    </button>
                </div>
                @endif
            @endforeach
        </div>
    @endif

    @foreach ($videojuegosSeleccionados as $id)
        <input type="hidden" name="videojuegos[]" value="{{ $id }}">
    @endforeach

</div>
