<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MensajeForoResource extends JsonResource
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
            'respuestas' => RespuestaForoResource::collection($this->respuestas),
        ];
    }
}
