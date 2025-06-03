<?php

namespace App\Http\Requests\Foro;

use App\Models\Foro\MensajeForo;
use Illuminate\Foundation\Http\FormRequest;

class MensajeForoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if ($this->isMethod('post')) {
            return $user->can('create', MensajeForo::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $user->can('update', $this->route('mensajeForo'));
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
