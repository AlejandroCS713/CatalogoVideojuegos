<?php

namespace App\Http\Controllers\Foro;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\MensajeForoRequest;
use App\Models\Foro\MensajeForo;

class MensajeForoController extends Controller
{
    public function store(MensajeForoRequest $request)
    {
        $validated = $request->validated();

        $mensaje = MensajeForo::create([
            'contenido' => $validated['contenido'],
            'imagen' => $request->imagen,
            'foro_id' => $request->foro_id,
            'usuario_id' => auth()->id(),
        ]);

        return redirect()->route('forum.show', $mensaje->foro_id)->with('success', '¡Mensaje enviado!');
    }

}
