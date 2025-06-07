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
            <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.6); z-index: 999; display: flex; align-items: center; justify-content: center; padding: 20px;" wire:click.self.prevent="closeModal">
                <div class="modal-content" style="background: #333; color: white; padding: 20px; border-radius: 8px; width: 90%; max-width: 600px; max-height: 85vh; overflow-y: auto; position: relative; z-index: 1000;">
                    <span wire:click.prevent="closeModal" class="close" style="position: absolute; top: 10px; right: 15px; font-size: 24px; cursor: pointer; color: #aaa; line-height: 1;" onmouseover="this.style.color='white'" onmouseout="this.style.color='#aaa'">&times;</span>
                    <h2>{{ $editMode ? __('Edit Video Game') : __('Create Video Game') }}</h2>

                    @if ($errors->any() && !$errors->has('imagen'))
                        <div style="background-color: #e74c3c; color: white; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    @if ($error != $errors->first('imagen'))
                                        <li>{{ $error }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form wire:submit.prevent="save">
                        <div style="margin-bottom: 15px;">
                            <x-form.label for="modal_nombre">{{ __('Name') }}</x-form.label>
                            <x-form.text-input id="modal_nombre" wire-model="nombre" placeholder="{{ __('Name') }}" />
                            @error('nombre') <span style="color: #e74c3c; font-size: 0.9em;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom: 15px;">
                            <x-form.label for="modal_descripcion">{{ __('Description') }}</x-form.label>
                            <x-form.text-area id="modal_descripcion" wire-model="descripcion" placeholder="{{ __('Description') }}"></x-form.text-area>
                            @error('descripcion') <span style="color: #e74c3c; font-size: 0.9em;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom: 15px;">
                            <x-form.label for="modal_fecha_lanzamiento">{{ __('Release Date') }}</x-form.label>
                            <x-form.date-input id="modal_fecha_lanzamiento" wire-model="fecha_lanzamiento" />
                            @error('fecha_lanzamiento') <span style="color: #e74c3c; font-size: 0.9em;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom: 15px;">
                            <x-form.label for="modal_desarrollador">{{ __('Developer') }}</x-form.label>
                            <x-form.text-input id="modal_desarrollador" wire-model="desarrollador" placeholder="{{ __('Developer') }}" />
                            @error('desarrollador') <span style="color: #e74c3c; font-size: 0.9em;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom: 15px;">
                            <x-form.label for="modal_publicador">{{ __('Publisher') }}</x-form.label>
                            <x-form.text-input id="modal_publicador" wire-model="publicador" placeholder="{{ __('Publisher') }}" />
                            @error('publicador') <span style="color: #e74c3c; font-size: 0.9em;">{{ $message }}</span> @enderror
                        </div>

                        <div style="margin-bottom: 15px;">
                            <label for="modal_imagen" class="form-label">{{ __('Cover Image') }} (Max 2MB: jpg, jpeg, png, webp)</label>
                            <input type="file" id="modal_imagen" wire:model="imagen" accept=".jpg,.jpeg,.png,.webp" class="form-input" style="width: 100%; padding: 8px; color: white; background-color: #555; border-radius: 4px; border: 1px solid #ccc;">
                            <div wire:loading wire:target="imagen" style="color: #3498db; margin-top: 5px;">{{ __('Uploading image...') }}</div>
                            @error('imagen') <span style="color: #e74c3c; font-size: 0.9em; display: block; margin-top: 5px;">{{ $message }}</span> @enderror
                            <div style="margin-top: 10px;">
                                @if ($imagen && !$errors->has('imagen'))
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
                            <x-form.label for="modal_plataformas">{{ __('Platforms') }}:</x-form.label>
                            <div wire:ignore>
                                <x-form.select-input id="modal_plataformas" wire-model="plataformas" :options="$allPlataformas" />
                            </div>
                            @error('plataformas') <span style="color: #e74c3c; font-size: 0.9em;">{{ $message }}</span>@enderror
                        </div>

                        <div style="margin-bottom: 15px;">
                            <x-form.label for="modal_generos">{{ __('Genres') }}:</x-form.label>
                            <div wire:ignore>
                                <x-form.select-input id="modal_generos" wire-model="generos" :options="$allGeneros" />
                            </div>
                            @error('generos') <span style="color: #e74c3c; font-size: 0.9em;">{{ $message }}</span> @enderror
                        </div>

                        <div style="text-align: right; margin-top: 20px;">
                            <button type="submit" class="button primary" style="padding: 10px 20px; margin-right: 10px;">
                                <span wire:loading wire:target="save">{{ $editMode ? __('Updating...') : __('Saving...') }}</span>
                                <span wire:loading.remove wire:target="save">{{ $editMode ? __('Update') : __('Save') }}</span>
                            </button>
                            <button type="button" wire:click.prevent="closeModal" class="button" style="padding: 10px 20px;">{{ __('Cancel') }}</button>
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
                    <p>{{ __('Are you sure you want to delete this video game? This action cannot be undone.') }}</p>
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
</div>
