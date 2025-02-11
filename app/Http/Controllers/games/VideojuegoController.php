<?php
namespace App\Http\Controllers\games;

use App\Http\Controllers\Controller;
use App\Models\games\Videojuego;
use Illuminate\Http\Request;

class VideojuegoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:videojuegos,nombre',
            'descripcion' => 'nullable|string',
        ]);
        $videojuego = Videojuego::create([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
        ]);

        return response()->json($videojuego, 201);
    }
    public function mejoresValoraciones()
    {
        $videojuegos = Videojuego::with('multimedia')
        ->where('fecha_lanzamiento', '>=', '2020-01-01')
        ->orderBy('rating_usuario', 'desc')
        ->take(6)
        ->get();

        //dd($videojuegos);
        //dd($videojuegos->toArray());
        /**
        foreach ($videojuegos as $videojuego) {
            dd($videojuego->multimedia); // Muestra la relación multimedia
        }
         */
        return view('welcome', compact('videojuegos'));
    }

    public function index()
    {
        $videojuegos = Videojuego::with('multimedia')->paginate(9);

        return view('videojuegos.index', compact('videojuegos'));
    }

    public function show($id)
    {
        $videojuego = Videojuego::with('multimedia')->findOrFail($id);

        return view('videojuegos.show', compact('videojuego'));
    }
}
