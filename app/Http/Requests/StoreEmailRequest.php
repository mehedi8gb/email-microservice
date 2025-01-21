<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'company_id' => 'required|exists:companies,id',
            'subject' => 'required|string',
            'from_email' => 'required|email',
            'to_email' => 'required|email',
            'message' => 'required|string',
            'other_data' => 'nullable|array',
        ];
    }
}

