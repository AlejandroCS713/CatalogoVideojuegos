<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Foro\ForoApiRequest;
use App\Http\Resources\ForoResource;
use App\Models\Foro\Foro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 * name="Foros",
 * description="Operaciones CRUD para Foros"
 * )
 */
class ForoController extends Controller
{
    /**
     * @OA\Get(
     * path="/foros",
     * operationId="getForosList",
     * tags={"Foros"},
     * summary="Obtener lista de foros",
     * description="Retorna una lista paginada de foros.",
     * @OA\Parameter(
     * name="per_page",
     * in="query",
     * description="Número de página para la paginación",
     * required=false,
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Operación exitosa",
     * @OA\JsonContent(
     * type="array",
     * @OA\Items(ref="#/components/schemas/Foro")
     * )
     * )
     * )
     */
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

    /**
     * @OA\Post(
     * path="/foros",
     * operationId="storeForo",
     * tags={"Foros"},
     * summary="Crear un nuevo foro",
     * description="Crea un nuevo foro y retorna los datos del foro creado.",
     * security={{"bearerAuth":{}}},
     * @OA\RequestBody(
     * required=true,
     * description="Datos del foro a crear",
     * @OA\JsonContent(ref="#/components/schemas/ForoApiRequest")
     * ),
     * @OA\Response(
     * response=201,
     * description="Foro creado exitosamente",
     * @OA\JsonContent(ref="#/components/schemas/Foro")
     * ),
     * @OA\Response(
     * response=401,
     * description="No autenticado"
     * ),
     * @OA\Response(
     * response=403,
     * description="No autorizado"
     * ),
     * @OA\Response(
     * response=422,
     * description="Error de validación",
     * @OA\JsonContent(ref="#/components/schemas/ErrorValidation")
     * )
     * )
     */
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

    /**
     * @OA\Get(
     * path="/foros/{foro}",
     * operationId="getForoById",
     * tags={"Foros"},
     * summary="Obtener información de un foro específico",
     * description="Retorna los datos de un foro.",
     * @OA\Parameter(
     * name="foro",
     * in="path",
     * required=true,
     * description="ID del foro",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Operación exitosa",
     * @OA\JsonContent(ref="#/components/schemas/Foro")
     * ),
     * @OA\Response(
     * response=404,
     * description="Foro no encontrado"
     * )
     * )
     */
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

    /**
     * @OA\Put(
     * path="/foros/{foro}",
     * operationId="updateForo",
     * tags={"Foros"},
     * summary="Actualizar un foro existente",
     * description="Actualiza los datos de un foro y retorna el foro actualizado.",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="foro",
     * in="path",
     * required=true,
     * description="ID del foro a actualizar",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Datos del foro a actualizar",
     * @OA\JsonContent(ref="#/components/schemas/ForoApiRequest")
     * ),
     * @OA\Response(
     * response=200,
     * description="Foro actualizado exitosamente",
     * @OA\JsonContent(ref="#/components/schemas/Foro")
     * ),
     * @OA\Response(response=401, description="No autenticado"),
     * @OA\Response(response=403, description="No autorizado"),
     * @OA\Response(response=404, description="Foro no encontrado"),
     * @OA\Response(response=422, description="Error de validación", @OA\JsonContent(ref="#/components/schemas/ErrorValidation"))
     * )
     */
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

    /**
     * @OA\Delete(
     * path="/foros/{foro}",
     * operationId="deleteForo",
     * tags={"Foros"},
     * summary="Eliminar un foro",
     * description="Elimina un foro existente.",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="foro",
     * in="path",
     * required=true,
     * description="ID del foro a eliminar",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Foro eliminado exitosamente",
     * @OA\JsonContent(@OA\Property(property="message", type="string", example="Foro eliminado correctamente."))
     * ),
     * @OA\Response(response=204, description="Foro eliminado exitosamente (sin contenido)"),
     * @OA\Response(response=401, description="No autenticado"),
     * @OA\Response(response=403, description="No autorizado"),
     * @OA\Response(response=404, description="Foro no encontrado")
     * )
     */
    public function destroy(Foro $foro)
    {

        DB::transaction(function () use ($foro) {
            $foro->videojuegos()->detach();
            $foro->delete();
        });

        return response()->json(['message' => 'Foro eliminado correctamente.'], 200);
    }
}
