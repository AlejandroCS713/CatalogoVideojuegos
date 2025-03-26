<?php

namespace App\Livewire;

use App\Models\games\Videojuego;
use Livewire\Component;

class BuscarVideojuego extends Component
{
    public $searchTerm = '';
    public $videojuegos = [];
    public $videojuegosSeleccionados = [];

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

        if ($videojuego && !in_array($id, $this->videojuegosSeleccionados)) {
            $this->videojuegosSeleccionados[] = $id;
        }

        $this->searchTerm = '';
        $this->videojuegos = [];

        $this->dispatch('videojuegosSeleccionados', $this->videojuegosSeleccionados);
    }

    public function eliminarVideojuego($id)
    {
        $this->videojuegosSeleccionados = array_filter($this->videojuegosSeleccionados, fn($videojuegoId) => $videojuegoId != $id);

        $this->dispatch('videojuegosSeleccionados', $this->videojuegosSeleccionados);
    }

    public function render()
    {
        return view('livewire.buscar-videojuego', [
            'videojuegos' => $this->videojuegos,
            'videojuegosSeleccionados' => $this->videojuegosSeleccionados,
        ]);
    }

}
