<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="Foro",
 *     type="object",
 *     @OA\Property(property="id", type="integer", description="ID del foro"),
 *     @OA\Property(property="titulo", type="string", description="Título del foro"),
 *     @OA\Property(property="descripcion", type="string", description="Descripción del foro"),
 *     @OA\Property(property="usuario", type="string", description="Nombre del usuario creador del foro"),
 *     @OA\Property(property="mensajes", type="array", @OA\Items(ref="#/components/schemas/MensajeForo"))
 * )
 */
class ForoResource extends JsonResource
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
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'usuario' => $this->usuario->name,
            'mensajes' => MensajeForoResource::collection($this->mensajes),
        ];
    }
}
