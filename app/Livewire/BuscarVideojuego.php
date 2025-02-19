<?php

namespace App\Livewire;

use App\Models\games\Videojuego;
use Livewire\Component;

class BuscarVideojuego extends Component
{
    public $searchTerm = '';
    public $videojuegos = [];

    public function search()
    {
        if (strlen($this->searchTerm) >= 2) {
            $this->videojuegos = Videojuego::where('nombre', 'LIKE', "%{$this->searchTerm}%")
                ->orderBy('nombre', 'asc')
                ->get();
        } else {
            $this->videojuegos = [];
        }
    }

    public function seleccionarVideojuego($id)
    {
        $videojuego = Videojuego::find($id);
        $this->searchTerm = $videojuego->nombre;
        $this->videojuegos = [];
        $this->dispatch('videojuegoSeleccionado', $id);
    }

    public function render()
    {
        return view('livewire.buscar-videojuego', [
            'videojuegos' => $this->videojuegos,
            'message' => empty($this->videojuegos) ? 'No se encontraron videojuegos' : null,
        ]);
    }

}
