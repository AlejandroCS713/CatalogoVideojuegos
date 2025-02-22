<div>
    <input type="text"  wire:model="searchTerm" wire:keydown.debounce.100ms="search" placeholder="{{ __('Search video game...') }}" class="form-control" style="color: black; margin-bottom: 10px;">

@if($message)
        <div class="error-message" style="color: red; margin-top: 10px;">
            {{ $message }}
        </div>
    @else
        <div class="videojuegos-group2">
            <div class="videojuegos-grid2">
                @foreach ($videojuegos as $videojuego)
                    <div class="videojuego-card2" wire:click="seleccionarVideojuego({{ $videojuego->id }})">
                        @php
                            $imagen = $videojuego->multimedia->isNotEmpty()
                                ? asset($videojuego->multimedia->first()->url)
                                : asset('images/default-game.png');
                        @endphp

                        <img style="width: 200px; position: relative; z-index: 2; padding-right: 20px; padding-bottom: 40px"
                             class="imagenes"
                             src="{{ $imagen }}"
                             alt="{{ __('Image of') }} {{ $videojuego->nombre }}"
                             onerror="this.onerror=null; this.src='{{ asset('images/default-game.png') }}';"
                        />

                        @if ($videojuego->multimedia->isEmpty())
                            <p style="color: gray; font-size: 14px;">{{ __('Image not available') }}</p>
                        @endif
                        <p>{{ $videojuego->nombre }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
