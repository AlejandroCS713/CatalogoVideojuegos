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

    @if ($modalOpen)
        <div class="modal" style="display: block;">
            <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); z-index: 999; display: flex; align-items: center; justify-content: center; padding: 20px;" wire:click.self.prevent="closeModal">
                <div class="modal-content" style="background: #333; color: white; padding: 25px; border-radius: 8px; width: 90%; max-width: 700px; max-height: 90vh; overflow-y: auto; position: relative; z-index: 1000;">
                    <span wire:click.prevent="closeModal" class="close" style="position: absolute; top: 10px; right: 15px; font-size: 28px; cursor: pointer; color: #aaa; line-height: 1;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#aaa'">&times;</span>
                    <h2>{{ $editMode ? __('Edit Foro') : __('Create Foro') }}</h2>

                    @if ($errors->any())
                        <div style="background-color: #e74c3c; color: white; padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 0.9em;">
                            <strong>{{ __('Errors found:') }}</strong>
                            <ul>
                                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                            </ul>
                        </div>
                    @endif

                    <form wire:submit.prevent="save">
                        <div style="margin-bottom: 15px;">
                            <label for="modal_titulo">{{ __('Title') }}</label>
                            <input type="text" id="modal_titulo" wire:model="titulo" placeholder="{{ __('Foro Title') }}" class="form-input" style="width: 100%; padding: 8px; color: black; border-radius: 4px; border: 1px solid #ccc;">
                            @error('titulo') <span style="color: #e74c3c; font-size: 0.9em;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom: 15px;">
                            <label for="modal_descripcion">{{ __('Description') }}</label>
                            <textarea id="modal_descripcion" wire:model="descripcion" placeholder="{{ __('Foro Description') }}" class="form-textarea" style="width: 100%; padding: 8px; color: black; border-radius: 4px; border: 1px solid #ccc; min-height: 100px;"></textarea>
                            @error('descripcion') <span style="color: #e74c3c; font-size: 0.9em;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom: 20px; border: 1px solid #555; padding: 15px; border-radius: 5px;">
                            <label style="display: block; margin-bottom: 10px;">{{ __('Select Related Games and their Roles') }}</label>
                            @livewire('buscar-videojuego', ['juegosIniciales' => $videojuegosConRoles])
                            @error('videojuegosConRoles') <span style="color: #e74c3c; font-size: 0.9em;">{{ $message }}</span> @enderror
                        </div>

                        <div style="text-align: right; margin-top: 25px;">
                            <button type="submit" class="button primary" style="padding: 10px 20px; margin-right: 10px;">
                                <span wire:loading wire:target="save">{{ $editMode ? __('Updating...') : __('Saving...') }}</span>
                                <span wire:loading.remove wire:target="save">{{ $editMode ? __('Update Foro') : __('Create Foro') }}</span>
                            </button>
                            <button type="button" wire:click.prevent="closeModal" class="button" style="padding: 10px 20px; background-color: #666; border-color: #555;">{{ __('Cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($confirmingDeletion)
        <div class="modal" style="display: block;">
            <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.7); z-index: 1001; display: flex; align-items: center; justify-content: center; padding: 20px;" wire:click.self.prevent="cancelDelete">
                <div class="modal-content" style="background: #e74c3c; color: white; padding: 25px; border-radius: 8px; width: 90%; max-width: 450px; text-align: center; z-index: 1002; position: relative;">
                    <h2 style="color: white; margin-bottom: 15px;">{{ __('Confirm Deletion') }}</h2>
                    <p>{{ __('Are you sure you want to delete this forum? Related messages and replies may not be deleted automatically.') }}</p>
                    <div style="margin-top: 25px;">
                        <button wire:click.prevent="deleteConfirmed" class="button" style="background-color: #c0392b; border-color: #a93226; color: white; margin-right: 15px; margin-bottom: 15px; padding: 10px 25px;">
                            <span wire:loading wire:target="deleteConfirmed">{{ __('Deleting...') }}</span>
                            <span wire:loading.remove wire:target="deleteConfirmed">{{ __('Yes, Delete') }}</span>
                        </button>
                        <button wire:click.prevent="cancelDelete" class="button" style="background-color: #bdc3c7; border-color: #a1a6a9; color: #333; padding: 10px 25px; margin-bottom: 15px;">
                            {{ __('No, Cancel') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($currentForo)
        <div class="game-container">
            <h1 class="game-title">{{ $currentForo->titulo }}</h1>
            <div class="game-info">
                <p class="game-description">{{ $currentForo->descripcion }}</p>
                <small>{{ __('Created by') }}: {{ $currentForo->usuario->name ?? __('Unknown') }}</small>
            </div>

            <div class="game-genres" style="margin-top: 30px;">
                <h2>{{ __('Related Games') }}</h2>
                <ul>
                    @forelse($currentForo->videojuegos as $videojuego)
                        <li>
                            <strong>{{ $videojuego->nombre }}</strong> (Rol: {{ $videojuego->pivot->rol_videojuego ?? __('N/A') }})
                            <br>
                            @php
                                $imgUrl = $videojuego->multimedia->where('tipo', 'imagen')->first()?->url;
                                $displayUrl = asset('images/default-game.png');
                                if ($imgUrl) {
                                    if (str_starts_with($imgUrl, 'http')) { $displayUrl = $imgUrl; }
                                    else { $displayUrl = asset($imgUrl); }
                                }
                            @endphp
                            <a style="background: none; border: none;cursor: pointer;" href="{{ route('videojuegos.show', $videojuego->id) }}">
                                <img style="width:150px; margin-top: 5px; margin-bottom: 15px; border-radius: 4px;" class="imagenes" src="{{ $displayUrl }}" alt="{{ __('Image of') }} {{ $videojuego->nombre }}"/>
                            </a>
                        </li>
                    @empty
                        <li>{{ __('No related games for this forum.') }}</li>
                    @endforelse
                </ul>
            </div>

            <div style="margin-top: 30px; margin-bottom: 20px; border-top: 1px solid #444; padding-top: 20px;">
                <a href="{{ route('foro.pdf', $currentForo) }}" class="button" target="_blank">
                    <i class="fas fa-file-pdf"></i> {{ __('Download PDF') }}
                </a>
            </div>

            <div class="game-genres" style="margin-top: 30px;">
                <h2><i class="fas fa-comments"></i> {{ __('Messages') }}</h2>

                @auth
                    <div style="margin-bottom: 30px; border-bottom: 1px solid #444; padding-bottom: 20px;">
                        <h3>{{ __('New Message') }}</h3>
                        <form action="{{ route('mensajes.store', $currentForo->id) }}" method="POST">
                            @csrf
                            <div>
                                <textarea name="contenido" required style="color: black; margin-bottom: 10px; width: 100%; min-height: 80px;" placeholder="{{ __('Write your message...') }}"></textarea>
                            </div>
                            <input type="hidden" name="foro_id" value="{{ $currentForo->id }}">
                            <button type="submit" class="button primary">{{ __('Send Message') }}</button>
                        </form>
                    </div>
                @endauth

                @forelse($currentForo->mensajes as $mensaje)
                    <div style="border: 1px solid #555; border-radius: 5px; padding: 15px; margin-bottom: 20px;">
                        <div>
                            <p style="margin-bottom: 5px;">{{ $mensaje->contenido }}</p>
                            <small style="color: #ccc;">
                                {{ __('Posted by') }} {{ $mensaje->usuario->name }} {{ $mensaje->created_at->diffForHumans() }} ({{ $mensaje->created_at->format('d/m/Y H:i') }})
                            </small>

                            @can('delete', $mensaje)
                                <form action="{{ route('mensaje-foro.destroy', $mensaje->id) }}" method="POST" style="display: inline; margin-left: 10px;" onsubmit="return confirm('{{ __('Are you sure you want to delete this message and its replies?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            @endcan

                            <div style="margin-left: 20px; margin-top: 15px; border-left: 2px solid #444; padding-left: 15px;">
                                <h5 style="font-size: 0.9em; color: #ddd; margin-bottom: 10px;">{{ __('Replies') }} ({{ $mensaje->respuestas->count() }})</h5>
                                @forelse($mensaje->respuestas as $respuesta)
                                    <div style="margin-bottom: 10px; font-size: 0.95em;">
                                        <p style="margin-bottom: 3px;">{{ $respuesta->contenido }}</p>
                                        <small style="color: #aaa;">- {{ $respuesta->usuario->name }} {{ $respuesta->created_at->diffForHumans() }}</small>
                                    </div>
                                @empty
                                    <p style="font-size: 0.9em; color: #888;">{{ __('No replies yet.') }}</p>
                                @endforelse

                                @auth
                                    <form action="{{ route('respuestas.store', $mensaje->id) }}" method="POST" style="margin-top: 15px;">
                                        @csrf
                                        <div>
                                            <textarea name="contenido" required style="color: black; margin-bottom: 5px; width: 100%; font-size: 0.9em; min-height: 50px;" placeholder="{{ __('Write a reply...') }}"></textarea>
                                            <input type="hidden" name="mensaje_id" value="{{ $mensaje->id }}">
                                        </div>
                                        <button type="submit" class="button small">{{ __('Reply') }}</button>
                                    </form>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="text-align: center; color: #999;">{{ __('No messages in this forum yet.') }}</p>
                @endforelse
            </div>
        </div>

    @else

        <div class="videojuegos-container">
            <h1 class="text-center mb-5 title-games">{{ __('Forums') }}</h1>

            @can('create', App\Models\Foro\Foro::class)
                <div style="display: flex; justify-content: center; margin-bottom: 40px;">
                    <button wire:click="openCreateModal" class="button fit" style="width: 250px;">
                        <i class="fas fa-plus"></i> {{ __('Create Foro') }}
                    </button>
                </div>
            @endcan

            <div wire:loading class="mb-4" style="color: white; text-align: center;">{{ __('Loading...') }}</div>

            @forelse($foros as $foro)
                <div class="game-container" style="margin-bottom: 20px; padding: 15px; border: 1px solid #555; border-radius: 5px; position: relative;">
                    <div class="card-body">
                        <h5 class="card-title">{{ $foro->titulo }}</h5>
                        <p class="card-text">{{ Str::limit($foro->descripcion, 150) }}</p>
                        <a href="{{ route('foro.show', $foro->id) }}" wire:navigate class="button" style="margin-right: 10px;">{{ __('View Foro') }}</a>
                        <div style="position: absolute; top: 10px; right: 10px; display: flex; gap: 5px;">
                            @can('update', $foro)
                                <button wire:click="openEditModal({{ $foro->id }})" class="button small" style="padding: 5px 8px;" title="{{ __('Edit') }}"><i class="fas fa-edit"></i></button>
                            @endcan
                            @can('delete', $foro)
                                <button wire:click="confirmDeleteAttempt({{ $foro->id }})" class="button small alt" style="padding: 5px 8px;" title="{{ __('Delete') }}"><i class="fas fa-trash-alt"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <p style="color: white; text-align: center;">{{ __('No forums found.') }}</p>
            @endforelse

            <div class="pagination-container" style="margin-top: 30px;">
                @if($foros && $foros->hasPages())
                    <ul class="pagination">
                        {{ $foros->links() }}
                    </ul>
                @endif
            </div>
        </div>
    @endif
</div>
