<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
{
    return true;
}

    public function rules(): array
    {
        return [
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ];
    }
}
