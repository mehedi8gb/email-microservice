<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
        ];
    }


    public function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => auth()->id(),
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name field must be a string.',
            'name.max' => 'The name field must not exceed 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email field must be a valid email address.',
            'email.unique' => 'The email address has already been taken.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
        ];
    }
}
