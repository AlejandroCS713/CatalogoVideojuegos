<?php

namespace App\Http\Controllers\forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\MensajeForoRequest;
use App\Models\Forum\Foro;
use App\Models\Forum\MensajeForo;
use App\Models\users\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
