<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VideojuegoConRolResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'imagen_url' => $this->imagen_url,
            'rol_en_foro' => $this->whenPivotLoaded('foro_videojuego', function () {
                return $this->pivot->rol_videojuego;
            }),
        ];
    }
}
