<?php

namespace App\Livewire\Videojuegos;

use AllowDynamicProperties;
use App\Models\games\Genero;
use App\Models\games\Plataforma;
use App\Models\games\Videojuego;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

use Livewire\WithFileUploads;
use App\Models\games\Multimedia;

#[AllowDynamicProperties]
class IndexComponent extends Component
{
    use WithPagination;
    use WithFileUploads;

    public $videojuegoId = null;
    public $currentGame = null;

    public $sort = 'newest';
    public $modalOpen = false;
    public $editMode = false;
    public $selectedId = null;
    public $nombre, $descripcion, $fecha_lanzamiento, $desarrollador, $publicador, $plataformas = [], $generos = [];
    public $allPlataformas = [];
    public $allGeneros = [];

    public $imagen;
    public $existingImageUrl = null;

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
            'imagen' => 'nullable|image|max:2048|mimes:jpg,jpeg,png,webp',
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


    protected function queryString()
    {
        if (is_null($this->videojuegoId)) {
            return ['sort', 'page'];
        }
        return [];
    }

    public function mount($videojuegoId = null)
    {
        $this->videojuegoId = $videojuegoId;
        if ($this->videojuegoId) {
            $this->loadCurrentGame();
            $this->selectedId = $this->videojuegoId;
        } else {
            $this->loadModalData();
        }
    }

    public function loadModalData() {
        $this->allPlataformas = Plataforma::orderBy('nombre')->get();
        $this->allGeneros = Genero::orderBy('nombre')->get();
    }

    public function loadCurrentGame()
    {
        if ($this->videojuegoId) {
            $this->currentGame = Videojuego::with([
                'multimedia',
                'generos',
                'plataformas',
                'precios'
            ])->find($this->videojuegoId);

            if (!$this->currentGame) {
                abort(404);
            }

            $this->existingImageUrl = $this->currentGame->multimedia->where('tipo', 'imagen')->first()?->url;
            if ($this->existingImageUrl && !str_starts_with($this->existingImageUrl, 'http')) {
                $this->existingImageUrl = asset($this->existingImageUrl);
            }
        }
    }

    public function render()
    {
        $videojuegos = null;
        if (is_null($this->videojuegoId)) {
            $query = Videojuego::with('multimedia');
            switch ($this->sort) {
                default: $query->orderBy('created_at', 'desc'); break;
            }
            $videojuegos = $query->paginate(30);
        }

        return view('livewire.videojuegos.index-component', [
            'videojuegos' => $videojuegos,
            'currentGame' => $this->currentGame
        ]);
    }


    public function openCreateModal()
    {
        if (!$this->allPlataformas->count() || !$this->allGeneros->count()) {
            $this->loadModalData();
        }
        $this->resetFields();
        $this->editMode = false;
        $this->modalOpen = true;
    }

    public function openEditModal($id)
    {
        if (!$this->allPlataformas->count() || !$this->allGeneros->count()) {
            $this->loadModalData();
        }

        try {
            $videojuego = Videojuego::with(['plataformas', 'generos', 'multimedia'])->findOrFail($id);

            $this->selectedId = $id;
            $this->nombre = $videojuego->nombre;
            $this->descripcion = $videojuego->descripcion;
            $this->fecha_lanzamiento = $videojuego->fecha_lanzamiento ? \Carbon\Carbon::parse($videojuego->fecha_lanzamiento)->format('Y-m-d') : null;
            $this->desarrollador = $videojuego->desarrollador;
            $this->publicador = $videojuego->publicador;
            $this->plataformas = $videojuego->plataformas->pluck('id')->toArray();
            $this->generos = $videojuego->generos->pluck('id')->toArray();

            $imagenActual = $videojuego->multimedia->where('tipo', 'imagen')->first();
            if ($imagenActual && $imagenActual->url) {
                if (str_starts_with($imagenActual->url, 'http')) {
                    $this->existingImageUrl = $imagenActual->url;
                } elseif (Storage::disk('public')->exists(str_replace('storage/', '', $imagenActual->url))) {

                    $this->existingImageUrl = asset($imagenActual->url);
                } else {
                    $this->existingImageUrl = null;
                }
            } else {
                $this->existingImageUrl = null;
            }
            $this->imagen = null;
            $this->resetValidation('imagen');

            $this->editMode = true;
            $this->modalOpen = true;
            $this->confirmingDeletion = false;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Videojuego no encontrado.');
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error al intentar editar el videojuego.');
        }
    }

    public function save()
    {
        $validated = $this->validate();

        $imageData = null;
        if ($this->imagen) {
            $imageData = $validated['imagen'];
        }
        unset($validated['imagen']);

        try {
            if ($this->editMode) {
                $videojuego = Videojuego::findOrFail($this->selectedId);
                $videojuego->update($validated);
                session()->flash('message', 'Videojuego actualizado correctamente.');
            } else {
                $videojuego = Videojuego::create($validated); // Crea nuevo videojuego
                session()->flash('message', 'Videojuego creado correctamente.');
            }
            $videojuego->plataformas()->sync($this->plataformas ?? []);
            $videojuego->generos()->sync($this->generos ?? []);

            if ($imageData) {
                if ($this->editMode) {
                    $imagenActual = Multimedia::where('videojuego_id', $videojuego->id)
                        ->where('tipo', 'imagen')
                        ->first();

                    if ($imagenActual) {
                        $relativePath = str_replace('storage/', '', $imagenActual->url);
                        if (Storage::disk('public')->exists($relativePath)) {
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

            if ($this->videojuegoId && $this->editMode && $this->videojuegoId == $videojuego->id) {
                $this->loadCurrentGame();
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Error: Videojuego no encontrado durante la operación.');
            $this->closeModal();
        } catch (\Exception $e) {
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
        $this->reset(['nombre', 'descripcion', 'fecha_lanzamiento', 'desarrollador', 'publicador', 'plataformas', 'generos', 'imagen', 'selectedId', 'editMode', 'existingImageUrl']);
        $this->resetValidation();
        if ($this->videojuegoId) { $this->selectedId = $this->videojuegoId; }
    }


    public function confirmDeleteAttempt($id)
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
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
                $imagen->delete();
            }
            $videojuego->plataformas()->detach();
            $videojuego->generos()->detach();

            $videojuego->delete();

            session()->flash('message', 'Videojuego eliminado correctamente.');

            if ($this->videojuegoId && $this->videojuegoId == $this->gameIdToDelete) {
                $this->redirect(route('videojuegos.index'));
            }

            $this->cancelDelete();

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            session()->flash('error', 'Error: Videojuego no encontrado al intentar eliminar.');
            $this->cancelDelete();
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error al eliminar el videojuego: ' . $e->getMessage());
            $this->cancelDelete();
        }
    }
}
