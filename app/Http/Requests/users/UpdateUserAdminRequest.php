<?php

namespace App\Http\Requests\users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserAdminRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->hasRole('admin');
    }
    public function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:videojuegos,nombre,' . $this->route('id'),
            'descripcion' => 'nullable|string',
            'fecha_lanzamiento' => 'nullable|date',
            'rating_usuario' => 'nullable|numeric|min:0|max:10',
            'rating_criticas' => 'nullable|numeric|min:0|max:10',
            'desarrollador' => 'nullable|string|max:255',
            'publicador' => 'nullable|string|max:255',
            'plataformas' => 'required|array',
            'generos' => 'required|array',
        ];
    }
}
