<?php

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;

class ForoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'imagen' => 'nullable|image|max:2048',
            'videojuegos' => 'nullable|array',
            'videojuegos.*' => 'exists:videojuegos,id',
            'rol_videojuego' => 'nullable|in:principal,secundario,opcional',
        ];
    }
}
