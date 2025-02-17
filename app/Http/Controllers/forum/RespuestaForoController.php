<?php

namespace App\Http\Controllers\forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\RespuestaForoRequest;
use App\Models\Forum\RespuestaForo;
use Illuminate\Http\Request;

class RespuestaForoController extends Controller
{
    public function store(RespuestaForoRequest $request)
    {
        $respuesta = RespuestaForo::create([
            'contenido' => $request->contenido,
            'imagen' => $request->imagen,
            'mensaje_id' => $request->mensaje_id,
            'usuario_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Respuesta enviada', 'respuesta' => $respuesta], 201);
    }
}
