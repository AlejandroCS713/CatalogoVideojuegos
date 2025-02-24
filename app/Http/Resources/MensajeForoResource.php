<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="MensajeForo",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="ID del mensaje"),
 *     @OA\Property(property="contenido", type="string", description="Contenido del mensaje"),
 *     @OA\Property(property="usuario", type="string", description="Nombre del usuario creador del mensaje"),
 *     @OA\Property(property="respuestas", type="array", @OA\Items(ref="#/components/schemas/RespuestaForo"))
 * )
 */
class MensajeForoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'contenido' => $this->contenido,
            'usuario' => $this->usuario->name,
            'respuestas' => RespuestaForoResource::collection($this->respuestas),
        ];
    }
}
