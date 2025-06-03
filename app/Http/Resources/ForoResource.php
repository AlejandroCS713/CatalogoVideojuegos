<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;


class ForoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'creado_por' => new UserLiteResource($this->whenLoaded('usuario')),
            'videojuegos_asociados' => VideojuegoConRolResource::collection($this->whenLoaded('videojuegos')),
            'mensajes' => MensajeForoResource::collection($this->whenLoaded('mensajes')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
