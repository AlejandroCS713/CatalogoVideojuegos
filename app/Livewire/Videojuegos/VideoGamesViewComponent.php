<?php

namespace App\Livewire\Videojuegos;
use AllowDynamicProperties;
use App\Models\games\Videojuego;

use Carbon\Carbon;
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

    public $filterDate = null;
    public $showOnlyHighlyRated = false;
    public $filterThisYear = false;
    public function updatingSort($value)
    {
        $this->filterDate = null;
        $this->showOnlyHighlyRated = false;
        $this->filterThisYear = false;
        $this->resetPage();
    }

    public function updatingFilterDate($value)
    {
        $this->sort = 'newest';
        $this->showOnlyHighlyRated = false;
        $this->filterThisYear = false;
        $this->resetPage();
    }

    public function updatingShowOnlyHighlyRated($value)
    {
        $this->sort = 'newest';
        $this->filterDate = null;
        if ($value) {
            $this->filterThisYear = false;
        }
        $this->resetPage();
    }

    public function updatingFilterThisYear($value)
    {
        $this->sort = 'newest';
        $this->filterDate = null;
        if ($value) {
            $this->showOnlyHighlyRated = false;
        }
        $this->resetPage();
    }

    protected function queryString()
    {
        return $this->videojuegoId ? [] : [
            'sort' => ['except' => 'newest'],
            'page' => ['except' => 1],
            'filterDate' => ['except' => null],
            'showOnlyHighlyRated' => ['except' => false],
            'filterThisYear' => ['except' => false],
        ];
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
    public function refreshData(): void
    {
        if ($this->videojuegoId) {
            $gameExists = Videojuego::find($this->videojuegoId);

            if ($gameExists) {
                $this->loadCurrentGame();
            } else {
                session()->flash('message', __('The video game you were watching has been deleted'));
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

            if ($this->filterDate) {
                $query->whereDate('fecha_lanzamiento', $this->filterDate);
            } elseif ($this->showOnlyHighlyRated) {
                $query->where('rating_usuario', '>', 4.0);
            } elseif ($this->filterThisYear) {
                $currentYear = Carbon::now()->year;
                $query->whereYear('fecha_lanzamiento', $currentYear);
            } else {
                $sortActions = [
                    'newest' => fn($q) => $q->newest(),
                    'oldest' => fn($q) => $q->oldest(),
                    'alphabetical' => fn($q) => $q->alphabetically(),
                    'reverse_alphabetical' => fn($q) => $q->reverseAlphabetically(),
                    'top_rated_aaa' => fn($q) => $q->topRatedAAA(),
                    'exclusive_games' => fn($q) => $q->exclusiveGames(),
                    'highly_rated_new_exclusive_games' => fn($q) => $q->highlyRatedNewExclusiveGames(),
                ];
                $action = $sortActions[$this->sort] ?? $sortActions['newest'];
                $action($query);
            }

            $videojuegos = $query->paginate(30);
        }

        return view('livewire.videojuegos.video-games-view-component', [
            'videojuegos' => $videojuegos,
            'currentGame' => $this->currentGame
        ]);
    }
}
