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
}
