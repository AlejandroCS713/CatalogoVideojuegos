<?php
namespace App\Http\Controllers\games;
use App\Http\Controllers\Controller;
use App\Jobs\NotificarLogroDesbloqueado;
use App\Models\games\Genero;
use App\Models\games\Multimedia;
use App\Models\games\Plataforma;
use App\Models\games\Precio;
use App\Models\games\Videojuego;
use App\Models\users\Logro;
use Illuminate\Http\Request;

class VideojuegoController extends Controller
{
    public function mejoresValoraciones()
    {
        $videojuegos = Videojuego::with('multimedia')
        ->where('fecha_lanzamiento', '>=', '2020-01-01')
        ->orderBy('rating_usuario', 'desc')
        ->take(6)
        ->get();
        return view('welcome', compact('videojuegos'));
    }

    public function index()
    {
        return view('videojuegos.index');
    }

    public function show($id)
    {
        $videojuego = Videojuego::with('multimedia')->findOrFail($id);

        return view('videojuegos.show', compact('videojuego'));
    }
}
