<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendEmailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Change this if you have authentication checks
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'company_id' => 'required|integer|exists:companies,id',
            'smtp_config_id' => 'required|integer|exists:smtp_configs,id',
            'email_draft_id' => 'required_without_all:subject,message|nullable|integer|exists:emails,id',
            'emails' => 'required|array',
            'emails.*' => 'email',
            'subject' => 'required_without:email_draft_id|string',
            'message' => 'required_without:email_draft_id|string',
            'template_type' => 'nullable|exists:email_templates,name',
            'variables' => 'nullable|array',
            'notify_users' => 'nullable|array',
        ];
    }
}
