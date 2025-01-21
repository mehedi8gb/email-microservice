<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Models\Email;

use App\Jobs\SendEmailJob;
use App\Models\SmtpConfig;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    public function index(): array
    {
        request()->validate([
            'company_id' => 'required|integer|exists:companies,id',
        ]);
        $emails = Email::query();
        $emails->where('company_id', request('company_id'));

        return handleApiRequest(request(), $emails);
    }

    public function store(StoreEmailRequest $request): JsonResponse
    {
        $email = Email::create($request->validated());
        return sendSuccessResponse('Email created', $email, 201);
    }

    public function update(UpdateEmailRequest $request, Email $email)
    {
        $email->update($request->validated());
        return $email;
    }

    public function sendEmail(Request $request): JsonResponse
    {
        // Validate the incoming payload
        $validated = $request->validate([
            'company_id'     => 'required|integer|exists:companies,id',
            'smtp_config_id' => 'required|integer|exists:s_m_t_p_configs,id',
            'email_draft_id' => 'required|integer|exists:emails,id',
            'emails'          => 'required|array',
            'emails.*'        => 'email',
        ]);

        // Fetch the SMTP configuration based on smtp_config_id
        $smtpConfig = SmtpConfig::find($validated['smtp_config_id']);
        if (!$smtpConfig) {
            return response()->json(['error' => 'SMTP configuration not found.'], 400);
        }

        // Fetch the email draft based on email_draft_id
        $emailDraft = Email::find($validated['email_draft_id']);
        if (!$emailDraft) {
            return response()->json(['error' => 'Email draft not found.'], 400);
        }

        // Loop through the emails and dispatch jobs
        foreach ($validated['emails'] as $toEmail) {
            try {
                // Dispatch the job for each recipient
                dispatch(new SendEmailJob(
                    $validated['company_id'],
                    $smtpConfig,
                    $toEmail,
                    $emailDraft->subject,
                    $emailDraft->message
                    ));
            } catch (Exception $e) {
                // Log the failure if required
                Log::error("Failed to dispatch email job for {$toEmail}: {$e->getMessage()}");
                return response()->json(['error' => 'Failed to dispatch email job.'], 500);
            }
        }

        return response()->json(['message' => 'Email job(s) dispatched successfully!']);
    }
}
