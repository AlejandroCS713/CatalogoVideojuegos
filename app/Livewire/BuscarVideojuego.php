<?php

namespace App\Livewire;

use App\Models\games\Videojuego;
use Livewire\Component;

class BuscarVideojuego extends Component
{
    public $query = '';  // Input de búsqueda
    public $videojuegos = []; // Resultados de búsqueda

    public function updatedQuery()
    {
        if (strlen($this->query) >= 2) { // Buscar solo si hay más de 2 letras
            $this->videojuegos = Videojuego::where('nombre', 'like', '%' . $this->query . '%')
                ->limit(5)
                ->get();
        } else {
            $this->videojuegos = [];
        }
    }

    public function seleccionarVideojuego($id)
    {
        $videojuego = Videojuego::find($id);
        $this->query = $videojuego->nombre; // Mostrar el nombre en el input
        $this->videojuegos = []; // Limpiar resultados
        $this->emit('videojuegoSeleccionado', $id); // Emitir evento

    }

    public function render()
    {
        return view('livewire.buscar-videojuego');
    }
}
