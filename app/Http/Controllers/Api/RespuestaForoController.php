<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Foro\RespuestaForoRequest;
use App\Http\Resources\RespuestaForoResource;
use App\Models\Foro\MensajeForo;
use App\Models\Foro\RespuestaForo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RespuestaForoController extends Controller
{
    public function index(MensajeForo $mensajeForo, Request $request)
    {
        $respuestas = $mensajeForo->respuestas()
            ->with('usuario')
            ->orderBy('created_at', $request->input('sort_direction', 'asc'))
            ->paginate($request->input('per_page', 15));

        return RespuestaForoResource::collection($respuestas);
    }


    public function store(RespuestaForoRequest $request, MensajeForo $mensajeForo)
    {
        $respuesta = $mensajeForo->respuestas()->create([
            'contenido' => $request->validated('contenido'),
            'usuario_id' => Auth::id(),
        ]);

        $respuesta->load('usuario');
        return new RespuestaForoResource($respuesta);
    }

    public function show(RespuestaForo $respuestaForo)
    {
        $respuestaForo->load('usuario');
        return new RespuestaForoResource($respuestaForo);
    }


    public function update(RespuestaForoRequest $request, RespuestaForo $respuestaForo)
    {

        $respuestaForo->update($request->validated());
        $respuestaForo->load('usuario');
        return new RespuestaForoResource($respuestaForo);
    }

    public function destroy(RespuestaForo $respuestaForo)
    {

        $respuestaForo->delete();

        return response()->json(['message' => 'Respuesta eliminada correctamente.'], 200);
    }
}
