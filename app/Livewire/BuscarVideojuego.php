<?php

namespace App\Livewire;

use App\Models\games\Videojuego;
use Livewire\Component;

class BuscarVideojuego extends Component
{
    public $searchTerm = '';
   public array $videojuegosConRol = [];

    protected $listeners = ['inicializarJuegosConRol'];

    public function inicializarJuegosConRol(array $juegos)
    {
        $this->videojuegosConRol = $juegos;
    }


    public function seleccionarVideojuego($id)
    {
        $videojuego = Videojuego::find($id);

        if ($videojuego && !array_key_exists($id, $this->videojuegosConRol)) {
            $this->videojuegosConRol[$id] = 'principal';
        }

        $this->searchTerm = '';

        $this->dispatch('videojuegosConRolSeleccionados', $this->videojuegosConRol);
    }

    public function eliminarVideojuego($id)
    {
        unset($this->videojuegosConRol[$id]);
        $this->dispatch('videojuegosConRolSeleccionados', $this->videojuegosConRol);
    }

    public function updatedVideojuegosConRol()
    {
        $this->dispatch('videojuegosConRolSeleccionados', $this->videojuegosConRol);
    }

    public function render()
    {
        $resultadosBusqueda = [];

        if (strlen($this->searchTerm) >= 2) {
            $resultadosBusqueda = Videojuego::where('nombre', 'LIKE', "%{$this->searchTerm}%")
                ->orderBy('nombre', 'asc')
                ->limit(10)
                ->get();
        }

        $videojuegosSeleccionadosModels = collect();
        if (!empty($this->videojuegosConRol)) {
            $ids = array_keys($this->videojuegosConRol);
            if (!empty($ids)) {
                $videojuegosSeleccionadosModels = Videojuego::whereIn('id', $ids)->get()->keyBy('id');
            }
        }

        return view('livewire.buscar-videojuego', [
            'resultadosBusqueda' => $resultadosBusqueda,
            'videojuegosSeleccionadosModels' => $videojuegosSeleccionadosModels,
        ]);
    }

}
