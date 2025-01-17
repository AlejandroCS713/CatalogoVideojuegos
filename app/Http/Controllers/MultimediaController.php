<?php

namespace App\Http\Controllers;

use App\Models\Multimedia;
use App\Models\Videojuego;
use Illuminate\Http\Request;

class MultimediaController extends Controller
{
    public function store(Request $request, $videojuegoId)
    {
        // Validar los datos
        $request->validate([
            'type' => 'required|string',  // Tipo de multimedia (imagen, video, etc.)
            'url' => 'required|file|mimes:jpg,jpeg,png,mp4',  // Validación del archivo
        ]);

        // Subir el archivo multimedia (por ejemplo, imagen o video)
        $path = $request->file('url')->store('public/multimedia');  // Guardar archivo en almacenamiento público

        // Crear una nueva multimedia para el videojuego especificado
        $multimedia = new Multimedia([
            'type' => $request->type,
            'url' => basename($path),  // Guardar solo el nombre del archivo
        ]);

        // Relacionar la multimedia con el videojuego
        $videojuego = Videojuego::findOrFail($videojuegoId);
        $videojuego->multimedia()->save($multimedia);  // Relacionamos con el videojuego

        // Redirigir a la vista del videojuego
        return redirect()->route('videojuegos.show', $videojuegoId);
    }
}
