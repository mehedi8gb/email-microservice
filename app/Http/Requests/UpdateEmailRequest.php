<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'from_email' => 'sometimes|email',
            'to_email' => 'sometimes|email',
            'message' => 'sometimes|string',
            'other_data' => 'sometimes|array',
        ];
    }
}

