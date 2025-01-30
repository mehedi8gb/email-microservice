<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSmtpConfigRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'host' => 'sometimes|string',
            'port' => 'sometimes|integer',
            'from_email' => 'sometimes|email',
            'from_name' => 'sometimes|string',
            'username' => 'sometimes|string',
            'password' => 'sometimes|string',
            'encryption' => 'sometimes|string|in:ssl,tls',
        ];
    }
}
