<?php

namespace App\Http\Requests\Foro;

use Illuminate\Foundation\Http\FormRequest;

class MensajeForoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'contenido' => 'required|string',
            'foro_id' => 'required|exists:foros,id',
        ];
    }
}
