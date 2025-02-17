<?php

namespace App\Http\Controllers\forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\MensajeForoRequest;
use App\Models\Forum\MensajeForo;
use Illuminate\Http\Request;

class MensajeForoController extends Controller
{
    public function store(MensajeForoRequest $request)
    {
        $mensaje = MensajeForo::create([
            'contenido' => $request->contenido,
            'imagen' => $request->imagen,
            'foro_id' => $request->foro_id,
            'usuario_id' => auth()->id(),
        ]);

        return response()->json(['message' => 'Mensaje enviado', 'mensaje' => $mensaje], 201);
    }

}
