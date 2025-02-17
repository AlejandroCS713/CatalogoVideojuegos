<?php

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;

class RespuestaForoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'contenido' => 'required|string',
            'imagen' => 'nullable|image|max:2048',
            'mensaje_id' => 'required|exists:mensaje_foros,id',
        ];
    }
}
