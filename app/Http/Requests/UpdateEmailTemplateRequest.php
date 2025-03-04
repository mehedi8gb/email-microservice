<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Implement policies if needed
    }

    public function rules(): array
    {
        return [
            'name'       => 'nullable|required|string|max:255',
            'body'       => 'nullable|required|string',
        ];
    }
}
