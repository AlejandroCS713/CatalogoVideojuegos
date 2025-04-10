<div>
    @if (session()->has('message'))
        <div style="background-color: #2ecc71; color: white; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center;">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div style="background-color: #e74c3c; color: white; padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center;">
            {{ session('error') }}
        </div>
    @endif

    @if ($currentGame)
        <div class="game-container">
            <h1 class="game-title">{{ $currentGame->nombre }}</h1>
            <div class="game-images">
                @foreach ($currentGame->multimedia as $media)
                    @if ($media->tipo === 'imagen')
                        @php
                            $mediaUrl = asset('images/default-game.png');
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
                <button wire:click="openCreateModal" class="button fit mb-4" style="width: auto ;padding: 0 10px 20px; margin-top: 10px;">
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

                        <a href="{{ route('videojuegos.show', $videojuego->id) }}" style="background: none; border: none; cursor: pointer; display: block; position: relative;">
                            <img class="game-image" src="{{ $imageUrl }}" alt="{{ __('Image of ') }} {{ $nombreJuego }}">
                            <span style="position: absolute; bottom: 5px; left: 5px; background: rgba(0,0,0,0.7); color: white; padding: 2px 5px; border-radius: 3px; font-size: 0.9em;">{{ $nombreJuego }}</span>
                        </a>

                        <div style="position: absolute; top: 5px; right: 5px; display: flex; gap: 5px;">
                            @can('Actualizar Videojuegos')
                                <button wire:click="openEditModal({{ $videojuego->id }})"  style="padding: 2px 6px; font-size: 0.8em;">{{ __('Edit') }}</button>
                            @endcan
                            @can('Eliminar Videojuegos')
                                <button wire:click="confirmDeleteAttempt({{ $videojuego->id }})" style="padding: 2px 6px; font-size: 0.8em; background-color: rgba(231, 76, 60, 0.8); border-color: rgba(192, 57, 43, 0.8);">{{ __('Delete') }}</button>
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
                        {{ $videojuegos->links('vendor.pagination.default') }}
                    </ul>
                @endif
            </div>

            @if ($modalOpen)
                <div class="modal" style="display: block;">
                    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 999;" wire:click="closeModal"></div>
                    <div class="modal-content" style="z-index: 1000; position: relative; background: #333; color: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 600px; margin: 50px auto;">
                        <span wire:click="closeModal" class="close" style="position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer; color: white;">&times;</span>
                        <h2>{{ $editMode ? __('Edit Video Game') : __('Create Video Game') }}</h2>
                        @if ($errors->any())
                            <div style="background-color: #e74c3c; color: white; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form wire:submit.prevent="save">
                            <div style="margin-bottom: 15px;">
                                <label for="modal_nombre">{{ __('Name') }}</label>
                                <input type="text" id="modal_nombre" wire:model="nombre" placeholder="{{ __('Name') }}" class="form-input" style="width: 100%; padding: 8px; color: black; border-radius: 4px; border: 1px solid #ccc;">
                                @error('nombre') <span style="color: #e74c3c; font-size: 0.9em;">{{ $message }}</span> @enderror
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label for="modal_descripcion">{{ __('Description') }}</label>
                                <textarea id="modal_descripcion" wire:model="descripcion" placeholder="{{ __('Description') }}" class="form-textarea" style="width: 100%; padding: 8px; color: black; border-radius: 4px; border: 1px solid #ccc; min-height: 100px;"></textarea>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label for="modal_fecha_lanzamiento">{{ __('Release Date') }}</label>
                                <input type="date" id="modal_fecha_lanzamiento" wire:model="fecha_lanzamiento" class="form-input" style="width: 100%; padding: 8px; color: black; border-radius: 4px; border: 1px solid #ccc;">
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label for="modal_desarrollador">{{ __('Developer') }}</label>
                                <input type="text" id="modal_desarrollador" wire:model="desarrollador" placeholder="{{ __('Developer') }}" class="form-input" style="width: 100%; padding: 8px; color: black; border-radius: 4px; border: 1px solid #ccc;">
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label for="modal_publicador">{{ __('Publisher') }}</label>
                                <input type="text" id="modal_publicador" wire:model="publicador" placeholder="{{ __('Publisher') }}" class="form-input" style="width: 100%; padding: 8px; color: black; border-radius: 4px; border: 1px solid #ccc;">
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label for="modal_imagen" class="form-label">{{ __('Cover Image') }} (Max 2MB: jpg, png, webp)</label>

                                <input type="file" id="modal_imagen" wire:model="imagen" class="form-input" style="width: 100%; padding: 8px; color: white; background-color: #555; border-radius: 4px; border: 1px solid #ccc;">

                                <div wire:loading wire:target="imagen" style="color: #3498db; margin-top: 5px;">
                                    {{ __('Uploading image...') }}
                                </div>

                                @error('imagen')
                                <span style="color: #e74c3c; font-size: 0.9em; display: block; margin-top: 5px;">{{ $message }}</span>
                                @enderror

                                <div style="margin-top: 10px;">
                                    @if ($imagen)
                                        <p style="font-size: 0.9em;">{{ __('New image preview:') }}</p>
                                        <img src="{{ $imagen->temporaryUrl() }}" alt="Preview" style="max-width: 150px; max-height: 150px; height: auto; border: 1px solid #ccc; border-radius: 4px;">
                                    @elseif ($existingImageUrl)
                                        <p style="font-size: 0.9em;">{{ __('Current image:') }}</p>
                                        <img src="{{ $existingImageUrl }}" alt="Current Image" style="max-width: 150px; max-height: 150px; height: auto; border: 1px solid #ccc; border-radius: 4px;">
                                    @elseif ($editMode)
                                        <p style="font-size: 0.9em; color: #aaa;">{{ __('No current image.') }}</p>
                                    @endif
                                </div>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label for="modal_plataformas">{{ __('Platforms') }}:</label>
                                <select multiple wire:model="plataformas" id="modal_plataformas" class="form-select" style="width: 100%; color: black; min-height: 100px;">
                                    @if($allPlataformas)
                                    @foreach($allPlataformas as $plataforma)
                                        <option value="{{ $plataforma->id }}">{{ $plataforma->nombre }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @error('plataformas') <span style="color: #e74c3c; font-size: 0.9em;">{{ $message }}</span> @enderror
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label for="modal_generos">{{ __('Genres') }}:</label>
                                <select multiple wire:model="generos" id="modal_generos" class="form-select" style="width: 100%; color: black; min-height: 100px;">
                                    @if($allGeneros)
                                    @foreach($allGeneros as $genero)
                                        <option value="{{ $genero->id }}">{{ $genero->nombre }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                @error('generos') <span style="color: #e74c3c; font-size: 0.9em;">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit"  style="padding: 0 10px 20px;">
                                <span wire:loading wire:target="save">{{ $editMode ? __('Updating...') : __('Saving...') }}</span>
                                <span wire:loading.remove wire:target="save">{{ $editMode ? __('Update') : __('Save') }}</span>
                            </button>
                            <button type="button" wire:click="closeModal" style="padding: 0 10px 20px;">{{ __('Cancel') }}</button>
                        </form>
                    </div>
                </div>
            @endif

            @if ($confirmingDeletion)
                <div class="modal" style="display: block;">
                    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); z-index: 1001;" wire:click="cancelDelete"></div> {{-- z-index más alto --}}
                    <div class="modal-content" style="z-index: 1002; position: relative; background: #e74c3c; color: white; padding: 25px; border-radius: 8px; width: 90%; max-width: 450px; margin: 15% auto; text-align: center;">
                       <h2 style="color: white; margin-bottom: 15px;">{{ __('Confirm Deletion') }}</h2>
                        <p>{{ __('Are you sure you want to delete this video game? This action cannot be undone.') }}</p>

                        <div style="margin-top: 25px;">
                            <button wire:click="deleteConfirmed" class="button" style="background-color: #c0392b; border-color: #a93226; color: white; margin-right: 15px;margin-bottom: 15px; padding: 10px 25px;">
                                <span wire:loading wire:target="deleteConfirmed">{{ __('Deleting...') }}</span>
                                <span wire:loading.remove wire:target="deleteConfirmed">{{ __('Yes, Delete') }}</span>
                            </button>
                            <button wire:click="cancelDelete" class="button" style="background-color: #bdc3c7; border-color: #a1a6a9; color: #333; padding: 10px 25px;">
                                {{ __('No, Cancel') }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif

        </div>

    @endif
</div>
