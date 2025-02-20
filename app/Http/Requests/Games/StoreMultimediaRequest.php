<?php

namespace App\Http\Requests\Games;

use Illuminate\Foundation\Http\FormRequest;

class StoreMultimediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;    }

    public function rules(): array
    {
        return [
            'type' => 'required|string',
            'url' => 'required|file|mimes:jpg,jpeg,png,mp4',
        ];
    }
}
