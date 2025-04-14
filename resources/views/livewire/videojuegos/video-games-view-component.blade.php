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
        </div>
    @else
        <div class="videojuegos-container">
            <h1 class="text-center mb-5 title-games">{{ __('Available Video Games') }}</h1>

            <form wire:submit.prevent class="mb-4">
                <label for="sort">{{ __('Sort by:') }}</label>
                <select wire:model.live="sort" id="sort" style="color: black;">
                    <option value="newest">{{ __('Newest') }}</option>
                    <option value="oldest">{{ __('Oldest') }}</option>
                    <option value="alphabetical">{{ __('A to Z') }}</option>
                    <option value="reverse_alphabetical">{{ __('Z to A') }}</option>
                    <option value="top_rated_aaa">{{ __('Top Rated AAA Games') }}</option>
                    <option value="exclusive_games">{{ __('Exclusive Games') }}</option>
                </select>
            </form>

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
                            $imageUrl = asset('images/default-game.png');
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
