<?php

namespace App\Http\Controllers\Foro;

use App\Http\Controllers\Controller;
use App\Http\Requests\Foro\MensajeForoRequest;
use App\Models\Foro\MensajeForo;
use Illuminate\Support\Facades\DB;

class MensajeForoController extends Controller
{
    public function store(MensajeForoRequest $request)
    {
        $validated = $request->validated();

        $mensaje = MensajeForo::create([
            'contenido' => $validated['contenido'],
            'foro_id' => $request->foro_id,
            'usuario_id' => auth()->id(),
        ]);

        return redirect()->route('foro.show', $mensaje->foro_id)->with('success', '¡Mensaje enviado!');
    }

    public function destroy(MensajeForo $mensaje)
    {
        DB::transaction(function () use ($mensaje) {
            $mensaje->respuestas()->delete();

            $mensaje->delete();
        });

        return redirect()->back()->with('success', 'Mensaje eliminado correctamente.');
    }

}
