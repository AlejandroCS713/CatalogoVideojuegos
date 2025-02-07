<?php
namespace App\Http\Controllers\games;

use App\Http\Controllers\Controller;
use App\Models\games\Videojuego;
use Illuminate\Http\Request;

class VideojuegoController extends Controller
{
    // Método para crear un nuevo videojuego
    public function store(Request $request)
    {
        // Validación de los datos del formulario
        $request->validate([
            'nombre' => 'required|string|max:255|unique:videojuegos,nombre', // Nombre único, máximo 255 caracteres
            'descripcion' => 'nullable|string', // Descripción opcional, tipo string
        ]);

        // Crear el videojuego
        $videojuego = Videojuego::create([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion'),
        ]);

        // Retornar la respuesta, por ejemplo, con el videojuego recién creado
        return response()->json($videojuego, 201);
    }
    public function mejoresValoraciones()
    {
        // Obtener los 6 videojuegos mejor valorados por los usuarios (rating de usuario)
        $videojuegos = Videojuego::with('multimedia') // Traemos la relación multimedia
        ->where('fecha_lanzamiento', '>=', '2020-01-01')
        ->orderBy('rating_usuario', 'desc') // Ordenamos por rating de usuario
        ->take(6) // Limitar a los 6 videojuegos mejor valorados
        ->get();

        // Pasamos los videojuegos a la vista welcome
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
        // Obtener todos los videojuegos paginados (puedes ajustar el número de elementos por página)
        $videojuegos = Videojuego::with('multimedia')->paginate(9);

        // Retornar la vista index con los videojuegos
        return view('videojuegos.index', compact('videojuegos'));
    }

    public function show($id)
    {
        // Buscar el videojuego por ID junto con su multimedia
        $videojuego = Videojuego::with('multimedia')->findOrFail($id);

        // Retornar la vista show con el videojuego
        return view('videojuegos.show', compact('videojuego'));
    }
}
