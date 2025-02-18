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
        $validated = $request->validated();

        $respuesta = RespuestaForo::create([
            'contenido' => $validated['contenido'],
            'imagen' => $request->imagen,
            'mensaje_id' => $request->mensaje_id,
            'usuario_id' => auth()->id(),
        ]);

        return redirect()->route('forum.show', $respuesta->mensaje->foro_id)->with('success', '¡Respuesta enviada!');
    }

}
