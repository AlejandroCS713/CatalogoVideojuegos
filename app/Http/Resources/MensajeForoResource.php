<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MensajeForoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'contenido' => $this->contenido,
            'usuario' => new UserLiteResource($this->whenLoaded('usuario')),
            'respuestas' => RespuestaForoResource::collection($this->whenLoaded('respuestas')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
