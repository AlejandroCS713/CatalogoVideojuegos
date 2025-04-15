<?php

namespace App\Livewire\Videojuegos;

use Livewire\Component;

use AllowDynamicProperties;
use App\Models\games\Genero;
use App\Models\games\Plataforma;
use App\Models\games\Videojuego;
use App\Models\games\Multimedia;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

#[AllowDynamicProperties]
class ManageGameAdminComponent extends Component
{
    use WithFileUploads;

    public $modalOpen = false;
    public $editMode = false;
    public $selectedId = null;
    public $nombre, $descripcion, $fecha_lanzamiento, $desarrollador, $publicador, $plataformas = [], $generos = [];
    public $imagen;
    public $existingImageUrl = null;

    public $allPlataformas = [];
    public $allGeneros = [];

    public $confirmingDeletion = false;
    public $gameIdToDelete = null;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_lanzamiento' => 'nullable|date',
            'desarrollador' => 'nullable|string|max:255',
            'publicador' => 'nullable|string|max:255',
            'plataformas' => 'nullable|array',
            'generos' => 'nullable|array',
            'imagen' => ($this->editMode && $this->imagen)
                ? 'nullable'
                : 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
        ];
    }

    protected $messages = [
        'imagen.image' => 'El archivo debe ser una imagen.',
        'imagen.max' => 'La imagen no debe pesar más de 2MB.',
        'imagen.mimes' => 'La imagen debe ser de tipo: jpg, jpeg, png, webp.',
    ];

    public function updatedImagen()
    {
        $this->validateOnly('imagen');
    }

    public function loadModalData()
    {
        if (empty($this->allPlataformas)) {
            $this->allPlataformas = Plataforma::orderBy('nombre')->get();
        }
        if (empty($this->allGeneros)) {
            $this->allGeneros = Genero::orderBy('nombre')->get();
        }
    }

    #[On('openCreateModalEvent')]
    public function openCreateModal()
    {
        $this->loadModalData();
        $this->resetFields();
        $this->editMode = false;
        $this->modalOpen = true;
        $this->confirmingDeletion = false;
    }

    #[On('openEditModalEvent')]
    public function openEditModal(int $id)
    {
        $this->loadModalData();

        try {
            $videojuego = Videojuego::with(['plataformas', 'generos', 'multimedia'])->findOrFail($id);

            $this->selectedId = $id;
            $this->nombre = $videojuego->nombre;
            $this->descripcion = $videojuego->descripcion;
            $this->fecha_lanzamiento = $videojuego->fecha_lanzamiento;
            $this->desarrollador = $videojuego->desarrollador;
            $this->publicador = $videojuego->publicador;
            $this->plataformas = $videojuego->plataformas->pluck('id')->toArray();
            $this->generos = $videojuego->generos->pluck('id')->toArray();

            $imagen = $videojuego->multimedia->where('tipo', 'imagen')->first();
            $this->existingImageUrl = $imagen ? $imagen->url : null;

            $this->editMode = true;
            $this->modalOpen = true;
            $this->confirmingDeletion = false;

        } catch (ModelNotFoundException $e) {
            session()->flash('error', 'Videojuego no encontrado.');
            $this->closeModal();
        } catch (Exception $e) {
            session()->flash('error', 'Ocurrió un error al intentar editar el videojuego: ' . $e->getMessage());
            $this->closeModal();
        }
    }

    public function save()
    {
        $validated = $this->validate();

        $imageData = null;
        if ($this->imagen) {
            $imageData = $validated['imagen'];
            unset($validated['imagen']);
        }

        try {
            if ($this->editMode) {
                $videojuego = Videojuego::findOrFail($this->selectedId);
                $videojuego->update($validated);
                session()->flash('message', 'Videojuego actualizado correctamente.');
            } else {
                $videojuego = Videojuego::create($validated);
                session()->flash('message', 'Videojuego creado correctamente.');
            }

            $videojuego->plataformas()->sync($this->plataformas ?? []);
            $videojuego->generos()->sync($this->generos ?? []);

            if ($imageData) {
                if ($this->editMode) {
                    $imagenActual = Multimedia::where('videojuego_id', $videojuego->id)->where('tipo', 'imagen')->first();

                    if ($imagenActual) {
                        $relativePath = str_replace('storage/', '', $imagenActual->url);
                        if (!str_starts_with($imagenActual->url, 'http') && Storage::disk('public')->exists($relativePath)) {
                            Storage::disk('public')->delete($relativePath);
                        }
                        $imagenActual->delete();
                    }
                }

                $path = $imageData->store('videojuegos', 'public');
                Multimedia::create([
                    'videojuego_id' => $videojuego->id,
                    'tipo' => 'imagen',
                    'url' => 'storage/' . $path
                ]);
            }

            $this->closeModal();
            $this->dispatch('gameSaved');

        } catch (ModelNotFoundException $e) {
            session()->flash('error', 'Error: Videojuego no encontrado durante la operación.');
            $this->closeModal();
        } catch (Exception $e) {
            session()->flash('error', 'Ocurrió un error al guardar el videojuego: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->modalOpen = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->reset([
            'nombre', 'descripcion', 'fecha_lanzamiento', 'desarrollador', 'publicador',
            'plataformas', 'generos', 'imagen', 'selectedId', 'editMode',
            'existingImageUrl', 'confirmingDeletion', 'gameIdToDelete'
        ]);
        $this->resetValidation();
    }

    #[On('confirmDeleteEvent')]
    public function confirmDeleteAttempt(int $id)
    {
        $this->gameIdToDelete = $id;
        $this->confirmingDeletion = true;
        $this->modalOpen = false;
    }

    public function cancelDelete()
    {
        $this->confirmingDeletion = false;
        $this->gameIdToDelete = null;
    }

    public function deleteConfirmed()
    {
        if (!$this->gameIdToDelete) {
            $this->cancelDelete();
            return;
        }

        try {
            $videojuego = Videojuego::with('multimedia')->findOrFail($this->gameIdToDelete);

            $imagen = $videojuego->multimedia->where('tipo', 'imagen')->first();
            if ($imagen) {
                $relativePath = str_replace('storage/', '', $imagen->url);
                if (!str_starts_with($imagen->url, 'http') && Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
                $imagen->delete();
            }

            $videojuego->plataformas()->detach();
            $videojuego->generos()->detach();
            $videojuego->delete();

            session()->flash('message', 'Videojuego eliminado correctamente.');
            $this->dispatch('gameDeleted');
            $this->cancelDelete();

        } catch (ModelNotFoundException $e) {
            session()->flash('error', 'Error: Videojuego no encontrado al intentar eliminar.');
            $this->cancelDelete();
        } catch (Exception $e) {
            session()->flash('error', 'Ocurrió un error al eliminar el videojuego: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }

    public function render()
    {
        return view('livewire.videojuegos.manage-game-admin-component');
    }
}
