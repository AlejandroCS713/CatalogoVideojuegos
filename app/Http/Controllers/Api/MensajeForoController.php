<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Foro\MensajeForoRequest;
use App\Http\Resources\MensajeForoResource;
use App\Models\Foro\Foro;
use App\Models\Foro\MensajeForo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Tag(
 * name="Mensajes de Foro",
 * description="Operaciones CRUD para Mensajes dentro de un Foro"
 * )
 */
class MensajeForoController extends Controller
{
    /**
     * @OA\Get(
     * path="/foros/{foro}/mensajes",
     * operationId="getMensajesForo",
     * tags={"Mensajes de Foro"},
     * summary="Obtener mensajes de un foro específico",
     * @OA\Parameter(name="foro", in="path", required=true, description="ID del foro", @OA\Schema(type="integer")),
     * @OA\Parameter(name="per_page", in="query", description="Paginado", @OA\Schema(type="integer")),
     * @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/MensajeForo"))),
     * @OA\Response(response=404, description="Foro no encontrado")
     * )
     */
    public function index(Foro $foro, Request $request)
    {
        $mensajes = $foro->mensajes()
            ->with(['usuario', 'respuestas.usuario'])
            ->orderBy('created_at', $request->input('sort_direction', 'desc'))
            ->paginate($request->input('per_page', 15));

        return MensajeForoResource::collection($mensajes);
    }

    /**
     * @OA\Post(
     * path="/foros/{foro}/mensajes",
     * operationId="storeMensajeForo",
     * tags={"Mensajes de Foro"},
     * summary="Crear un nuevo mensaje en un foro",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(name="foro", in="path", required=true, description="ID del foro donde crear el mensaje", @OA\Schema(type="integer")),
     * @OA\RequestBody(
     * required=true,
     * description="Contenido del mensaje",
     * @OA\JsonContent(ref="#/components/schemas/MensajeForoStoreRequest")
     * ),
     * @OA\Response(response=201, description="Mensaje creado", @OA\JsonContent(ref="#/components/schemas/MensajeForo")),
     * @OA\Response(response=401, description="No autenticado"),
     * @OA\Response(response=403, description="No autorizado para crear mensajes en este foro (si aplica policy)"),
     * @OA\Response(response=404, description="Foro no encontrado"),
     * @OA\Response(response=422, description="Error de validación", @OA\JsonContent(ref="#/components/schemas/ErrorValidation"))
     * )
     */
    public function store(MensajeForoRequest $request, Foro $foro)
    {
        $mensaje = $foro->mensajes()->create([
            'contenido' => $request->validated('contenido'),
            'usuario_id' => Auth::id(),
        ]);

        $mensaje->load(['usuario', 'respuestas.usuario']);
        return new MensajeForoResource($mensaje);
    }

    /**
     * @OA\Get(
     * path="/mensajes/{mensajeForo}",
     * operationId="getMensajeForoById",
     * tags={"Mensajes de Foro"},
     * summary="Obtener un mensaje específico de un foro",
     * description="Retorna los datos de un mensaje de foro.",
     * @OA\Parameter(
     * name="mensajeForo",
     * in="path",
     * required=true,
     * description="ID del mensaje de foro",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Operación exitosa",
     * @OA\JsonContent(ref="#/components/schemas/MensajeForo")
     * ),
     * @OA\Response(
     * response=404,
     * description="Mensaje no encontrado"
     * )
     * )
     */
    public function show(MensajeForo $mensajeForo)
    {
        $mensajeForo->load(['usuario', 'respuestas.usuario']);
        return new MensajeForoResource($mensajeForo);
    }

    /**
     * @OA\Put(
     * path="/mensajes/{mensajeForo}",
     * operationId="updateMensajeForo",
     * tags={"Mensajes de Foro"},
     * summary="Actualizar un mensaje de foro existente",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="mensajeForo",
     * in="path",
     * required=true,
     * description="ID del mensaje a actualizar",
     * @OA\Schema(type="integer")
     * ),
     * @OA\RequestBody(
     * required=true,
     * description="Datos del mensaje a actualizar. 'contenido' es el campo principal.",
     * @OA\JsonContent(ref="#/components/schemas/MensajeForoStoreRequest")
     * ),
     * @OA\Response(
     * response=200,
     * description="Mensaje actualizado exitosamente",
     * @OA\JsonContent(ref="#/components/schemas/MensajeForo")
     * ),
     * @OA\Response(response=401, description="No autenticado"),
     * @OA\Response(response=403, description="No autorizado"),
     * @OA\Response(response=404, description="Mensaje no encontrado"),
     * @OA\Response(response=422, description="Error de validación", @OA\JsonContent(ref="#/components/schemas/ErrorValidation"))
     * )
     */
    public function update(MensajeForoRequest $request, MensajeForo $mensajeForo)
    {

        $mensajeForo->update($request->validated());
        $mensajeForo->load(['usuario', 'respuestas.usuario']);
        return new MensajeForoResource($mensajeForo);
    }

    /**
     * @OA\Delete(
     * path="/mensajes/{mensajeForo}",
     * operationId="deleteMensajeForo",
     * tags={"Mensajes de Foro"},
     * summary="Eliminar un mensaje de foro",
     * security={{"bearerAuth":{}}},
     * @OA\Parameter(
     * name="mensajeForo",
     * in="path",
     * required=true,
     * description="ID del mensaje a eliminar",
     * @OA\Schema(type="integer")
     * ),
     * @OA\Response(
     * response=200,
     * description="Mensaje eliminado exitosamente",
     * @OA\JsonContent(@OA\Property(property="message", type="string", example="Mensaje eliminado correctamente."))
     * ),
     * @OA\Response(response=401, description="No autenticado"),
     * @OA\Response(response=403, description="No autorizado"),
     * @OA\Response(response=404, description="Mensaje no encontrado")
     * )
     */
    public function destroy(MensajeForo $mensajeForo)
    {

        $mensajeForo->delete();

        return response()->json(['message' => 'Mensaje eliminado correctamente.'], 200);
    }
}
