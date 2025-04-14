<?php

namespace App\Livewire\Videojuegos;
use AllowDynamicProperties;
use App\Models\games\Videojuego;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

#[AllowDynamicProperties]
class VideoGamesViewComponent extends Component
{
    use WithPagination;

    public $videojuegoId = null;
    public $currentGame = null;

    public $sort = 'newest';
    public $page = 1;

    public function updatingSort()
    {
        $this->resetPage();
    }

    protected function queryString()
    {
        return $this->videojuegoId ? [] : ['sort' => ['except' => 'newest'], 'page' => ['except' => 1]];
    }

    public function mount($videojuegoId = null)
    {
        $this->videojuegoId = $videojuegoId;
        if ($this->videojuegoId) {
            $this->loadCurrentGame();
        }
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
        }
    }

    #[On('gameSaved')]
    #[On('gameDeleted')]
    #[On('gameSaved')]
    #[On('gameDeleted')]
    public function refreshData(): void
    {
        if ($this->videojuegoId) {
            $gameExists = Videojuego::find($this->videojuegoId);

            if ($gameExists) {
                $this->loadCurrentGame();
            } else {
                session()->flash('message', 'El videojuego que estabas viendo ha sido eliminado.');
                $this->redirect(route('videojuegos.index'), navigate: true);
                return;
            }
        }
    }

    public function render()
    {
        $videojuegos = null;
        if (is_null($this->videojuegoId)) {
            $query = Videojuego::with('multimedia');

            $sortActions = [
                'newest' => fn($q) => $q->newest(),
                'oldest' => fn($q) => $q->oldest(),
                'alphabetical' => fn($q) => $q->alphabetically(),
                'reverse_alphabetical' => fn($q) => $q->reverseAlphabetically(),
                'top_rated_aaa' => fn($q) => $q->topRatedAAA(),
                'exclusive_games' => fn($q) => $q->exclusiveGames(),
            ];
            $action = $sortActions[$this->sort] ?? $sortActions['newest'];
            $action($query);

            $videojuegos = $query->paginate(30);
        }

        return view('livewire.videojuegos.video-games-view-component', [
            'videojuegos' => $videojuegos,
            'currentGame' => $this->currentGame
        ]);
    }
}
