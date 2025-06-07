<?php

namespace App\Livewire\Foros;

use App\Http\Requests\Foro\ForoRequest;
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
    public array $videojuegosConRoles = [];

    public int $page = 1;

    #[On('videojuegosConRolSeleccionados')]
    public function updateSelectedVideojuegosConRol(array $selectedData): void
    {
        $validGameIds = Videojuego::whereIn('id', array_keys($selectedData))->pluck('id')->toArray();
        $this->videojuegosConRoles = collect($selectedData)
            ->filter(fn ($role, $id) => in_array($id, $validGameIds))
            ->all();
    }

    protected function rules(): array
    {
        return (new ForoRequest())->rules();
    }

    protected function messages(): array
    {
        return [
            'videojuegosConRoles.*.required' => __('The role for each selected game is required.'),
            'videojuegosConRoles.*.string' => __('The role for each selected game must be a string.'),
            'videojuegosConRoles.*.in' => __('The role for each selected game must be one of: Main, Secondary, Optional.'),
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
                session()->flash('message', __('The forum you were viewing has been deleted'));
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
        $this->dispatch('inicializarJuegosConRol', []);
    }

    public function openEditModal(int $id): void
    {
        $foro = Foro::with('videojuegos')->findOrFail($id);
        $this->authorize('update', $foro);

        $this->selectedId = $id;
        $this->titulo = $foro->titulo;
        $this->descripcion = $foro->descripcion;

        $this->videojuegosConRoles = $foro->videojuegos->mapWithKeys(function ($videojuego) {
            return [$videojuego->id => $videojuego->pivot->rol_videojuego ?? 'principal'];
        })->toArray();

        $this->editMode = true;
        $this->modalOpen = true;
        $this->confirmingDeletion = false;
        $this->dispatch('inicializarJuegosConRol', $this->videojuegosConRoles);
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
            session()->flash('message', __('Foro updated successfully!'));
        } else {
            $foroData['usuario_id'] = auth()->id();
            $foro = Foro::create($foroData);
            session()->flash('message', __('Foro created successfully!'));
        }

        $videojuegoSyncData = [];
        if (!empty($this->videojuegosConRoles)) {
            foreach ($this->videojuegosConRoles as $videojuego_id => $rol) {
                if (is_int($videojuego_id) && Videojuego::find($videojuego_id)) {
                    $videojuegoSyncData[$videojuego_id] = ['rol_videojuego' => $rol];
                }
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
            'titulo', 'descripcion', 'videojuegosConRoles',
            'selectedId', 'editMode', 'confirmingDeletion',
            'foroIdToDelete'
        ]);
        $this->resetValidation();
        $this->dispatch('inicializarJuegosConRol', []);
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

        if ($this->foroId && $this->foroId == $this->foroIdToDelete) {
            $this->redirect(route('foro.index'), navigate:true);
        }
    }

    public function render()
    {
        $forosPaginados = null;

        if (is_null($this->foroId)) {
            $query = Foro::query();
            $query->orderBy('created_at', 'desc');
            $forosPaginados = $query->paginate(10);
        }

        return view('livewire.foros.forum-index', [
            'foros' => $forosPaginados,
            'currentForo' => $this->currentForo
        ]);
    }
}
