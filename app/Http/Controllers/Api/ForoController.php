<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Foro\ForoApiRequest;
use App\Http\Resources\ForoResource;
use App\Models\Foro\Foro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForoController extends Controller
{
    public function index(Request $request)
    {
        $foros = Foro::with([
            'usuario',
            'videojuegos',
            'mensajes' => function ($query) {
                $query->with(['usuario', 'respuestas' => function($qResp) {
                    $qResp->with('usuario')->orderBy('created_at', 'asc');
                }])->withCount('respuestas')->orderBy('created_at', 'desc');
            }
        ])
            ->withCount('mensajes')
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 15));

        return ForoResource::collection($foros);
    }

    public function store(ForoApiRequest $request)
    {

        $validatedData = $request->validated();
        $foro = null;

        DB::transaction(function () use ($validatedData, $request, &$foro) {
            $foroData = [
                'titulo' => $validatedData['titulo'],
                'descripcion' => $validatedData['descripcion'],
                'usuario_id' => $request->user()->id,
            ];

            $foro = Foro::create($foroData);

            if (!empty($validatedData['videojuegosConRoles'])) {
                $syncData = [];
                foreach ($validatedData['videojuegosConRoles'] as $videojuegoId => $rol) {
                    $syncData[(int)$videojuegoId] = ['rol_videojuego' => $rol];
                }
                $foro->videojuegos()->sync($syncData);
            }
        });

        $foro->load(['usuario', 'videojuegos', 'mensajes.usuario', 'mensajes.respuestas.usuario']);
        return new ForoResource($foro);
    }

    public function show(Foro $foro)
    {
        $foro->load([
            'usuario',
            'videojuegos',
            'mensajes' => function ($query) {
                $query->with(['usuario', 'respuestas' => function($qResp) {
                    $qResp->with('usuario')->orderBy('created_at', 'asc');
                }])->orderBy('created_at', 'desc');
            }
        ]);
        return new ForoResource($foro);
    }

    public function update(ForoApiRequest $request, Foro $foro)
    {

        $validatedData = $request->validated();

        DB::transaction(function () use ($validatedData, $request, $foro) {
            $foro->fill($validatedData);
            $foro->save();

            if ($request->has('videojuegosConRoles')) {
                $syncData = [];
                if (!empty($validatedData['videojuegosConRoles'])) {
                    foreach ($validatedData['videojuegosConRoles'] as $videojuegoId => $rol) {
                        $syncData[(int)$videojuegoId] = ['rol_videojuego' => $rol];
                    }
                }
                $foro->videojuegos()->sync($syncData);
            }
        });

        $foro->load(['usuario', 'videojuegos', 'mensajes.usuario', 'mensajes.respuestas.usuario']);
        return new ForoResource($foro);
    }

    public function destroy(Foro $foro)
    {

        DB::transaction(function () use ($foro) {
            $foro->videojuegos()->detach();
            $foro->delete();
        });

        return response()->json(['message' => 'Foro eliminado correctamente.'], 200);
    }
}
