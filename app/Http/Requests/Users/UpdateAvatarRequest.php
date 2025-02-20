<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
{
    public function authorize(): bool
{
    return true;
}

    public function rules(): array
    {
        return [
            'avatar' => 'required|string',
        ];
    }
}
