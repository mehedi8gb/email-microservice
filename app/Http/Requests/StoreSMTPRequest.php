<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSMTPRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:companies,id',
            'host' => 'required|string',
            'port' => 'required|integer',
            'from_email' => 'required|email',
            'from_name' => 'nullable|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'encryption' => 'required|string|in:ssl,tls',
        ];
    }
}
