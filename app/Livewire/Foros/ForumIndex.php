<?php

namespace App\Livewire\Foros;

use Livewire\Component;

use App\Models\Foro\Foro;
use App\Models\games\Videojuego;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\WithPagination;
use Livewire\Attributes\On;


class ForumIndex extends Component
{
    use WithPagination;
    use AuthorizesRequests;

    public ?int $foroId = null;
    public ?Foro $currentForo = null;

    public $modalOpen = false;
    public $confirmingDeletion = false;
    public $editMode = false;
    public ?int $selectedId = null;
    public ?int $foroIdToDelete = null;

    public string $titulo = '';
    public string $descripcion = '';
    public array $videojuegos = [];
    public string $rol_videojuego = 'secundario';

    public int $page = 1;

    #[On('videojuegosSeleccionados')]
    public function updateSelectedVideojuegos(array $selectedIds): void
    {
        $existingIds = Videojuego::whereIn('id', $selectedIds)->pluck('id')->toArray();
        $this->videojuegos = $existingIds;
    }

    protected function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'videojuegos' => 'nullable|array',
            'videojuegos.*' => 'required|integer|exists:videojuegos,id',
            'rol_videojuego' => 'required|string|in:principal,secundario,opcional',
        ];
    }

    protected function queryString(): array
    {
        return is_null($this->foroId)
            ? ['page' => ['except' => 1]]
            : [];
    }

    public function mount($foroId = null): void
    {
        $this->foroId = $foroId;
        if ($this->foroId) {
            $this->loadCurrentForo();
        }
    }

    public function loadCurrentForo(): void
    {
        if (!$this->foroId) return;

        $this->currentForo = Foro::with([
            'usuario',
            'mensajes.usuario',
            'mensajes.respuestas.usuario',
            'videojuegos.multimedia'
        ])->findOrFail($this->foroId);
    }

    #[On('forumSaved')]
    #[On('forumDeleted')]
    public function refreshData(): void
    {
        if ($this->foroId) {
            $foro = Foro::find($this->foroId);
            if ($foro) {
                $this->loadCurrentForo();
            } else {
                session()->flash('message', __('The forum you were viewing has been deleted.'));
                $this->redirect(route('foros.index'), navigate: true);
            }
        }
    }

    public function openCreateModal(): void
    {
        $this->authorize('create', Foro::class);
        $this->resetFields();
        $this->editMode = false;
        $this->modalOpen = true;
    }

    public function openEditModal(int $id): void
    {
        $foro = Foro::with('videojuegos')->findOrFail($id);
        $this->authorize('update', $foro);

        $this->selectedId = $id;
        $this->titulo = $foro->titulo;
        $this->descripcion = $foro->descripcion;
        $this->videojuegos = $foro->videojuegos->pluck('id')->toArray();
        $pivotData = $foro->videojuegos->first()?->pivot;
        $this->rol_videojuego = $pivotData?->rol_videojuego ?? 'secundario';

        $this->editMode = true;
        $this->modalOpen = true;
        $this->confirmingDeletion = false;
    }

    public function save(): void
    {
        $this->editMode
            ? $this->authorize('update', Foro::find($this->selectedId))
            : $this->authorize('create', Foro::class);

        $validated = $this->validate();

        $foroData = [
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
        ];

        if ($this->editMode) {
            $foro = Foro::findOrFail($this->selectedId);
            $foro->update($foroData);
            session()->flash('message', __('Forum updated successfully!'));
        } else {
            $foroData['usuario_id'] = auth()->id();
            $foro = Foro::create($foroData);
            session()->flash('message', __('Forum created successfully!'));
        }

        $videojuegoSyncData = [];
        if (!empty($this->videojuegos)) {
            foreach ($this->videojuegos as $videojuego_id) {
                $videojuegoSyncData[$videojuego_id] = ['rol_videojuego' => $this->rol_videojuego];
            }
        }
        $foro->videojuegos()->sync($videojuegoSyncData);

        $this->closeModal();
        $this->dispatch('forumSaved');
    }

    public function closeModal(): void
    {
        $this->modalOpen = false;
        $this->resetFields();
    }

    public function resetFields(): void
    {
        $this->reset([
            'titulo', 'descripcion', 'videojuegos', 'rol_videojuego',
            'selectedId', 'editMode', 'confirmingDeletion',
            'foroIdToDelete'
        ]);
        $this->resetValidation();
    }

    public function confirmDeleteAttempt(int $id): void
    {
        $foro = Foro::findOrFail($id);
        $this->authorize('delete', $foro);
        $this->foroIdToDelete = $id;
        $this->confirmingDeletion = true;
        $this->modalOpen = false;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeletion = false;
        $this->foroIdToDelete = null;
    }

    public function deleteConfirmed(): void
    {
        if (!$this->foroIdToDelete) {
            $this->cancelDelete();
            return;
        }
        $foro = Foro::findOrFail($this->foroIdToDelete);
        $this->authorize('delete', $foro);

        $foro->videojuegos()->detach();
        $foro->delete();

        session()->flash('message', __('Forum deleted successfully!'));
        $this->dispatch('forumDeleted');
        $this->cancelDelete();
    }

    public function render()
    {
        $foros = null;

        if (is_null($this->foroId)) {
            $query = Foro::query();
            $query->orderBy('created_at', 'desc');
            $foros = $query->paginate(10);
        }

        return view('livewire.foros.forum-index', [
            'foros' => $foros,
            'currentForo' => $this->currentForo
        ]);
    }
}
