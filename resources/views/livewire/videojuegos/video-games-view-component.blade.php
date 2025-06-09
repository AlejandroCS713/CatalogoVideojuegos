<div>
    @if (session()->has('message') && View::hasSection('show_index_messages'))
        <div style="background-color: #2ecc71; color: white; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center;">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error') && View::hasSection('show_index_messages'))
        <div style="background-color: #e74c3c; color: white; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center;">
            {{ session('error') }}
        </div>
    @endif

    @livewire('videojuegos.manage-game-admin-component')

    @if ($currentGame)
            <div class="game-container">
                <h1 class="game-title">{{ $currentGame->nombre }}</h1>
                <div class="game-images">
                    @foreach ($currentGame->multimedia as $media)
                        @if ($media->tipo === 'imagen')
                            @php
                                $mediaUrl = asset('images/default-game.jpeg');
                                if (isset($media->url) && !empty($media->url)) {
                                    if (str_starts_with($media->url, 'http')) {
                                        $mediaUrl = $media->url;
                                    } else {
                                        $relativePath = ltrim(str_replace('storage/', '', $media->url), '/');
                                        $mediaUrl = asset('storage/' . $relativePath);
                                    }
                                }
                            @endphp
                            <img class="game-image" src="{{ $mediaUrl }}" alt="{{ __('Image of ') }} {{ $currentGame->nombre }}">
                        @endif
                    @endforeach
                </div>
                <div class="game-info">
                    <p class="game-description">{{ $currentGame->descripcion }}</p>
                    <p><strong>{{ __('Release Date:') }}</strong> {{ $currentGame->fecha_lanzamiento ? \Carbon\Carbon::parse($currentGame->fecha_lanzamiento)->format('d/m/Y') : __('Unknown') }}</p>
                    <p><strong>{{ __('User Rating:') }}</strong> ⭐ {{ $currentGame->rating_usuario ? number_format($currentGame->rating_usuario, 1) : 'N/A' }}</p>
                    <p><strong>{{ __('Reviews Rating:') }}</strong> ⭐ {{ $currentGame->rating_criticas ? number_format($currentGame->rating_criticas, 1) : 'N/A' }}</p>
                    <p><strong>{{ __('Developer:') }}</strong> {{ $currentGame->desarrollador }}</p>
                    <p><strong>{{ __('Publisher:') }}</strong> {{ $currentGame->publicador }}</p>
                </div>
                <div class="game-genres">
                    <h2>{{ __('Genres') }}</h2>
                    <ul>
                        @forelse ($currentGame->generos as $genero)
                            <li class="genre-item">{{ $genero->nombre }}</li>
                        @empty
                            <li>{{ __('No genres listed.') }}</li>
                        @endforelse
                    </ul>
                </div>
                <div class="game-platforms">
                    <h2>{{ __('Available in:') }}</h2>
                    <ul>
                        @forelse ($currentGame->plataformas as $plataforma)
                            <li>
                                <strong>{{ $plataforma->nombre }}</strong>
                                @php
                                    $precio = $currentGame->precios->where('plataforma_id', $plataforma->id)->first();
                                @endphp
                                @if ($precio)
                                    - <span class="game-price">💰 {{ number_format($precio->precio, 2) }} €</span>
                                @else
                                    - <span class="game-price">{{ __('Price not available') }}</span>
                                @endif
                            </li>
                        @empty
                            <li>{{ __('No platforms listed.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
    @else
        <div class="videojuegos-container">
            <h1 class="text-center mb-5 title-games">{{ __('Available Video Games') }}</h1>
            <div class="filters-container mb-4" style="display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">

                <div style="flex: 1; min-width: 150px;">
                    <x-form.label for="sort">{{ __('Sort by:') }}</x-form.label>
                    <select wire:model.live="sort" id="sort" class="form-select w-full p-2 text-black border border-gray-300 rounded-md">
                        <option value="newest">{{ __('Newest') }}</option>
                        <option value="oldest">{{ __('Oldest') }}</option>
                        <option value="alphabetical">{{ __('A to Z') }}</option>
                        <option value="reverse_alphabetical">{{ __('Z to A') }}</option>
                        <option value="top_rated_aaa">{{ __('Top Rated AAA Games') }}</option>
                        <option value="exclusive_games">{{ __('Exclusive Games') }}</option>
                        <option value="highly_rated_new_exclusive_games">{{ __('Highly Rated New Exclusive Games') }}</option>
                    </select>
                </div>

                <div style="flex: 1; min-width: 150px;">
                    <x-form.label for="filter_date">{{ __('Release Date Filter:') }}</x-form.label>
                    <x-form.date-input id="filter_date" wire:model.live="filterDate" />
                    @error('filterDate') <span style="color: #e74c3c; font-size: 0.9em;">{{ $message }}</span> @enderror
                </div>

                <div style="flex: 1; min-width: 200px; display: flex; align-items: center; justify-content: flex-start; padding-bottom: 8px;">
                    <label for="filter_this_year">
                        <x-form.checkbox id="filter_this_year" wire:model.live="filterThisYear" class="mr-2" />
                        <span style="font-size: 17px">{{ __('Show Games Released This Year') }}</span>
                    </label>
                </div>

                <div style="flex: 1; min-width: 200px; display: flex; align-items: center; justify-content: flex-start; padding-bottom: 8px;">
                    <label for="show_highly_rated">
                        <x-form.checkbox id="show_highly_rated" wire:model.live="showOnlyHighlyRated" class="mr-2" />
                        <span style="font-size: 18px">{{ __('Show Only Highly Rated Games') }}</span>
                    </label>
                </div>
            </div>

        @can('Crear Videojuegos')
                <button wire:click="$dispatch('openCreateModalEvent')" class="button fit mb-4" style="width: auto ;padding: 0 10px 20px; margin-top: 10px;">
                    {{ __('Create Game') }}
                </button>
            @endcan

            <div wire:loading class="mb-4" style="color: white;">{{ __('Loading...') }}</div>

            <div class="videojuegos-grid">
                @forelse($videojuegos as $videojuego)
                    <div style="position: relative;">
                        @php
                            $imageUrl = asset('images/default-game.jpeg');
                            $firstMedia = null;

                            if ($videojuego->relationLoaded('multimedia') && $videojuego->multimedia->isNotEmpty()) {
                                $firstMedia = $videojuego->multimedia->first();
                            }

                            if ($firstMedia && !empty($firstMedia->url)) {
                                $url = $firstMedia->url;
                                if (str_starts_with($url, 'http')) {
                                    $imageUrl = $url;
                                } else {
                                    $relativePath = ltrim(str_replace('storage/', '', $url), '/');
                                    $imageUrl = asset('storage/' . $relativePath);
                                }
                            }
                            $nombreJuego = $videojuego->nombre ?? __('Unnamed Game');
                        @endphp

                        <a href="{{ route('videojuegos.show', $videojuego->id) }}" wire:navigate style="background: none; border: none; cursor: pointer; display: block; position: relative;">
                            <img class="game-image" src="{{ $imageUrl }}" alt="{{ __('Image of ') }} {{ $nombreJuego }}">
                            <span style="position: absolute; bottom: 5px; left: 5px; background: rgba(0,0,0,0.7); color: white; padding: 2px 5px; border-radius: 3px; font-size: 0.9em;">{{ $nombreJuego }}</span>
                        </a>

                        <div style="position: absolute; top: 5px; right: 5px; display: flex; gap: 5px;">
                            @can('Actualizar Videojuegos')
                                <button wire:click="$dispatch('openEditModalEvent', { id: {{ $videojuego->id }} })"  style="padding: 2px 6px; font-size: 0.8em;">{{ __('Edit') }}</button>
                            @endcan
                            @can('Eliminar Videojuegos')
                                <button wire:click="$dispatch('confirmDeleteEvent', { id: {{ $videojuego->id }} })" style="padding: 2px 6px; font-size: 0.8em; background-color: rgba(231, 76, 60, 0.8); border-color: rgba(192, 57, 43, 0.8);">{{ __('Delete') }}</button>
                            @endcan
                        </div>
                    </div>
                @empty
                    <p style="color: white; grid-column: 1 / -1; text-align: center;">{{ __('No video games found.') }}</p>
                @endforelse
            </div>

            <div class="pagination-container">
                @if($videojuegos && $videojuegos->hasPages())
                    <ul class="pagination">
                        {{ $videojuegos->links() }}
                    </ul>
                @endif
            </div>
        </div>
    @endif
</div>
