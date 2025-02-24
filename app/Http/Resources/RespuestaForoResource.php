<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="RespuestaForo",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="ID de la respuesta"),
 *     @OA\Property(property="contenido", type="string", description="Contenido de la respuesta"),
 *     @OA\Property(property="usuario", type="string", description="Nombre del usuario que creó la respuesta")
 * )
 */
class RespuestaForoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'contenido' => $this->contenido,
            'usuario' => $this->usuario->name,
        ];
    }
}
