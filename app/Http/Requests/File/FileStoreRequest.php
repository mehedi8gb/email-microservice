<?php

namespace App\Http\Requests\File;

use App\Rules\Base64Image;
use Illuminate\Foundation\Http\FormRequest;

class FileStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization logic goes here (e.g., check if the user has permission)
    }

    protected function prepareForValidation(): void
    {
        // You can perform data manipulation here
    }

    public function rules(): array
    {
        return [
            'student_id' => 'required_without:session_id',

            'session_id' => 'nullable', // 'session_id' can be nullable and a string

            'file_type' => 'required|in:proof_of_address,visa,passport,bank_statement,qualification,cv,work_experience,profile',
            'files' => 'required_without:base64_files|array',
            'files.*' => 'file|max:102400', // Maximum file size is 100MB
            'base64_files' => 'required_without:files|array', // 'base64_files' should be an array if provided
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required_without' => 'The student ID field is required when session ID is not present.',
            'file_type.required' => 'The file type field is required.',
            'file_type.in' => 'The selected file type is invalid. Please choose from the following valid options: id_document, academic_record, proof_of_address, payment_receipt, photo, medical_form, visa, passport, bank_statement, recommendation_letter, transcript, application_form, admission_offer.',
            'files.required_without' => 'The files field is required when base64 files are not present.',
            'files.array' => 'The files must be an array.',
            'files.*.file' => 'Each file must be a valid file.',
            'files.*.max' => 'Each file may not be greater than 100MB.',
            'base64_files.required_without' => 'The base64 files field is required when files are not present.',
            'base64_files.array' => 'The base64 files must be an array.',
        ];
    }

    public function attributes(): array
    {
        return [
            'files' => 'uploaded files',
            'base64_files' => 'base64 encoded files',
        ];
    }


    protected function passedValidation(): void
    {
        $this->replace([
            //
        ]);
    }
}
