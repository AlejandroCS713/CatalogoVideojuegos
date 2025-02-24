<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ForoResource;
use App\Models\Forum\Foro;

use Illuminate\Http\Request;
/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API de Foros",
 *     description="API para gestionar foros y mensajes",
 *     @OA\Contact(
 *         email="soporte@webgames.com"
 *     )
 * )
 *
 * @OA\Tag(
 *     name="Foros",
 *     description="Endpoints para la gestión de foros y mensajes"
 * )
 */

class ForoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/foros",
     *     summary="Obtiene todos los foros",
     *     tags={"Foros"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de foros",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Foro")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontraron foros"
     *     )
     * )
     */
    public function index()
    {
        $foros = Foro::paginate(10); // Puede cambiarse según lo que prefieras
        return ForoResource::collection($foros);
    }

    /**
     * @OA\Get(
     *     path="/api/foros/{foro}",
     *     summary="Obtiene los detalles de un foro específico",
     *     tags={"Foros"},
     *     @OA\Parameter(
     *         name="foro",
     *         in="path",
     *         required=true,
     *         description="ID del foro",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalles del foro",
     *         @OA\JsonContent(ref="#/components/schemas/Foro")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No se encontró el foro"
     *     )
     * )
     */
    public function show($id)
    {
        $foro = Foro::with(['mensajes.usuario', 'mensajes.respuestas.usuario'])->findOrFail($id);
        return new ForoResource($foro);
    }
}
