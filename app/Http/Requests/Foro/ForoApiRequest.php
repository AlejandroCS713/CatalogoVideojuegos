<?php

namespace App\Http\Requests\Foro;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Foro\Foro;
use App\Models\games\Videojuego;
use App\Models\users\User;

class ForoApiRequest extends FormRequest
{

    public function authorize(): bool
    {
        $user = $this->user();

        if ($this->isMethod('post')) {
            return $user->can('create', Foro::class);
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $user->can('update', $this->route('foro'));
        }

        return true;
    }

    public function rules(): array
    {
        $rules = [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string|max:5000',
            'videojuegosConRoles' => 'nullable|array',
            'videojuegosConRoles.*' => [
                'required',
                'string',
                Rule::in(['principal', 'secundario', 'opcional']),
            ],
            'videojuegosConRoles.*.id' => 'numeric',
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['titulo'] = 'sometimes|' . $rules['titulo'];
            $rules['descripcion'] = 'sometimes|' . $rules['descripcion'];
            $rules['videojuegosConRoles'] = 'sometimes|' . $rules['videojuegosConRoles'];
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $videojuegosConRoles = $this->input('videojuegosConRoles', []);

            if (!is_array($videojuegosConRoles)) {
                $validator->errors()->add('videojuegosConRoles', 'El campo videojuegosConRoles debe ser un array.');
                return;
            }

            foreach (array_keys($videojuegosConRoles) as $videojuegoId) {
                if (!is_numeric($videojuegoId)) {
                    $validator->errors()->add(
                        "videojuegosConRoles",
                        "El ID de videojuego '{$videojuegoId}' proporcionado no es numérico."
                    );
                    continue;
                }

                $idEntero = (int)$videojuegoId;
                if (!Videojuego::where('id', $idEntero)->exists()) {
                    $validator->errors()->add(
                        "videojuegosConRoles",
                        "El videojuego con ID {$idEntero} no existe."
                    );
                }
            }
        });
    }
}
