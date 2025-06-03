<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Foro\MensajeForoRequest;
use App\Http\Resources\MensajeForoResource;
use App\Models\Foro\Foro;
use App\Models\Foro\MensajeForo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MensajeForoController extends Controller
{
    public function index(Foro $foro, Request $request)
    {
        $mensajes = $foro->mensajes()
            ->with(['usuario', 'respuestas.usuario'])
            ->withCount('respuestas')
            ->orderBy('created_at', $request->input('sort_direction', 'desc'))
            ->paginate($request->input('per_page', 15));

        return MensajeForoResource::collection($mensajes);
    }

    public function store(MensajeForoRequest $request, Foro $foro)
    {
        $mensaje = $foro->mensajes()->create([
            'contenido' => $request->validated('contenido'),
            'usuario_id' => Auth::id(),
        ]);

        $mensaje->load(['usuario', 'respuestas.usuario']);
        return new MensajeForoResource($mensaje);
    }


    public function show(MensajeForo $mensajeForo)
    {
        $mensajeForo->load(['usuario', 'respuestas.usuario']);
        return new MensajeForoResource($mensajeForo);
    }

    public function update(MensajeForoRequest $request, MensajeForo $mensajeForo)
    {

        $mensajeForo->update($request->validated());
        $mensajeForo->load(['usuario', 'respuestas.usuario']);
        return new MensajeForoResource($mensajeForo);
    }

    public function destroy(MensajeForo $mensajeForo)
    {

        $mensajeForo->delete();

        return response()->json(['message' => 'Mensaje eliminado correctamente.'], 200);
    }
}
