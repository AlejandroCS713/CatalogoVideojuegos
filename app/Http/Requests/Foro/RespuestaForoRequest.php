<?php

namespace App\Http\Requests\Foro;

use App\Models\Foro\RespuestaForo;
use Illuminate\Foundation\Http\FormRequest;

class RespuestaForoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if ($this->isMethod('post')) {
            return $user->can('create', RespuestaForo::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $user->can('update', $this->route('respuestaForo'));
        }

        return true;
    }

    public function rules()
    {
        $rules = [
            'contenido' => 'required|string|max:2000',
        ];

        return $rules;
    }
}
