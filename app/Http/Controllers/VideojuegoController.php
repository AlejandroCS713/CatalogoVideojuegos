<?php
namespace App\Http\Controllers;

use App\Models\Videojuego;
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
    public function index()
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
}
