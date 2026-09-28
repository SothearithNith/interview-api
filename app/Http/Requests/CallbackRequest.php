<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reference' => ['required', 'string'],
            'event' => ['required', 'string'],
            'status' => ['required', 'string'],
            'signature' => ['required', 'string'],
        ];
    }
}
