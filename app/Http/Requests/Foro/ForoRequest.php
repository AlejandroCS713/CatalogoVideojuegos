<?php

namespace App\Http\Requests\Foro;

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
            'descripcion' => 'required|string',
            'videojuegosConRoles' => 'nullable|array',
            'videojuegosConRoles.*' => 'required|string|in:principal,secundario,opcional',
        ];
    }
}
