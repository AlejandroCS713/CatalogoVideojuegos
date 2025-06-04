<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Foro\RespuestaForoRequest;
use App\Http\Resources\RespuestaForoResource;
use App\Models\Foro\MensajeForo;
use App\Models\Foro\RespuestaForo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 * name="Respuestas de Mensaje",
 * description="Operaciones CRUD para Respuestas a Mensajes"
 * )
 */
class RespuestaForoController extends Controller
{
    /**
     * @OA\Get(
     * path="/mensajes/{mensajeForo}/respuestas",
     * operationId="getRespuestasDeMensaje",
     * tags={"Respuestas de Mensaje"},
     * summary="Obtener respuestas de un mensaje específico",
     * @OA\Parameter(name="mensajeForo", in="path", required=true, description="ID del mensaje padre", @OA\Schema(type="integer")),
     * @OA\Parameter(name="per_page", in="query", description="Paginado", @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/RespuestaForo"))),
     * @OA\Response(response=404, description="Mensaje no encontrado")
     * )
     */
    public function index(MensajeForo $mensajeForo, Request $request)
    {
        $respuestas = $mensajeForo->respuestas()
            ->with('usuario')
            ->orderBy('created_at', $request->input('sort_direction', 'asc'))
            ->paginate($request->input('per_page', 15));

        return RespuestaForoResource::collection($respuestas);
    }

    /**
     * @OA\Post(
     * path="/mensajes/{mensajeForo}/respuestas",
     * operationId="storeRespuestaForo",
     * tags={"Respuestas de Mensaje"},
     * summary="Crear una nueva respuesta a un mensaje",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="mensajeForo", in="path", required=true, description="ID del mensaje al que responder", @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * description="Contenido de la respuesta",
     * @OA\JsonContent(ref="#/components/schemas/RespuestaForoStoreRequest")
     * ),
     * @OA\Response(response=201, description="Respuesta creada", @OA\JsonContent(ref="#/components/schemas/RespuestaForo")),
     * @OA\Response(response=401, description="No autenticado"),
     * @OA\Response(response=403, description="No autorizado (si aplica policy)"),
     * @OA\Response(response=404, description="Mensaje padre no encontrado"),
     * @OA\Response(response=422, description="Error de validación", @OA\JsonContent(ref="#/components/schemas/ErrorValidation"))
     * )
     */
    public function store(RespuestaForoRequest $request, MensajeForo $mensajeForo)
    {
        $respuesta = $mensajeForo->respuestas()->create([
            'contenido' => $request->validated('contenido'),
            'usuario_id' => Auth::id(),
        ]);

        $respuesta->load('usuario');
        return new RespuestaForoResource($respuesta);
    }
    /**
     * @OA\Get(
     * path="/respuestas/{respuestaForo}",
     * operationId="getRespuestaForoById",
     * tags={"Respuestas de Mensaje"},
     * summary="Obtener una respuesta específica",
     * @OA\Parameter(
     * name="respuestaForo",
     * in="path",
     * required=true,
     * description="ID de la respuesta",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Operación exitosa",
     * @OA\JsonContent(ref="#/components/schemas/RespuestaForo")
     * ),
     * @OA\Response(
     * response=404,
     * description="Respuesta no encontrada"
     * )
     * )
     */
    public function show(RespuestaForo $respuestaForo)
    {
        $respuestaForo->load('usuario');
        return new RespuestaForoResource($respuestaForo);
    }

    /**
     * @OA\Put(
     * path="/respuestas/{respuestaForo}",
     * operationId="updateRespuestaForo",
     * tags={"Respuestas de Mensaje"},
     * summary="Actualizar una respuesta existente",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="respuestaForo",
     * in="path",
     * required=true,
     * description="ID de la respuesta a actualizar",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Datos de la respuesta a actualizar. 'contenido' es el campo principal.",
     * @OA\JsonContent(ref="#/components/schemas/RespuestaForoStoreRequest")
     * ),
     * @OA\Response(
     * response=200,
     * description="Respuesta actualizada exitosamente",
     * @OA\JsonContent(ref="#/components/schemas/RespuestaForo")
     * ),
     * @OA\Response(response=401, description="No autenticado"),
     * @OA\Response(response=403, description="No autorizado"),
     * @OA\Response(response=404, description="Respuesta no encontrada"),
     * @OA\Response(response=422, description="Error de validación", @OA\JsonContent(ref="#/components/schemas/ErrorValidation"))
     * )
     */
    public function update(RespuestaForoRequest $request, RespuestaForo $respuestaForo)
    {

        $respuestaForo->update($request->validated());
        $respuestaForo->load('usuario');
        return new RespuestaForoResource($respuestaForo);
    }
    /**
     * @OA\Delete(
     * path="/respuestas/{respuestaForo}",
     * operationId="deleteRespuestaForo",
     * tags={"Respuestas de Mensaje"},
     * summary="Eliminar una respuesta",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="respuestaForo",
     * in="path",
     * required=true,
     * description="ID de la respuesta a eliminar",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Respuesta eliminada exitosamente",
     * @OA\JsonContent(@OA\Property(property="message", type="string", example="Respuesta eliminada correctamente."))
     * ),
     * @OA\Response(response=401, description="No autenticado"),
     * @OA\Response(response=403, description="No autorizado"),
     * @OA\Response(response=404, description="Respuesta no encontrada")
     * )
     */
    public function destroy(RespuestaForo $respuestaForo)
    {

        $respuestaForo->delete();

        return response()->json(['message' => 'Respuesta eliminada correctamente.'], 200);
    }
}
