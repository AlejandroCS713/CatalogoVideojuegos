<?php
namespace App\Http\Controllers\games;
use App\Events\LogroDesbloqueado;
use App\Http\Controllers\Controller;
use App\Jobs\NotificarLogroDesbloqueado;
use App\Models\games\Genero;
use App\Models\games\Multimedia;
use App\Models\games\Plataforma;
use App\Models\games\Precio;
use App\Models\games\Reseña;
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

    public function index(Request $request)
    {
        $sort = $request->input('sort', 'newest');

        $sortMap = [
            'oldest' => 'oldest',
            'alphabetical' => 'alphabetically',
            'reverse_alphabetical' => 'reverseAlphabetically',
            'newest' => 'newest',
            'top_rated_aaa' => 'TopRatedAAA',
            'exclusive_games'=> 'ExclusiveGames'
        ];

        $scope = $sortMap[$sort] ?? 'newest';

        $videojuegos = Videojuego::with('multimedia')->{$scope}();

        $videojuegos = $videojuegos->paginate(30);

        return view('videojuegos.index', compact('videojuegos'));
    }

    public function show($id)
    {
        $videojuego = Videojuego::with('multimedia')->findOrFail($id);

        return view('videojuegos.show', compact('videojuego'));
    }
}
