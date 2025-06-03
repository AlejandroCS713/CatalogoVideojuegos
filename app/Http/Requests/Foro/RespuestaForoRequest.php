<?php

namespace App\Http\Requests\Foro;

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
            'mensaje_id' => 'required|exists:mensaje_foros,id',
        ];
    }
}
