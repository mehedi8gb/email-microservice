<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailRequest;
use App\Http\Requests\StoreEmailRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Models\Email;
use App\Jobs\SendEmailJob;
use App\Models\EmailLog;
use App\Models\SmtpConfig;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    public function index(): JsonResponse
    {
        request()->validate([
            'company_id' => 'required|integer|exists:companies,id',
        ]);
        $emails = Email::query();
        $emails->where('company_id', request('company_id'));
        try {
            $results = handleApiRequest(request(), $emails);

            // Convert $results to a collection if it's an array
            $results = collect($results);
            if ($results->isEmpty()) {
                return sendErrorResponse('No records found', 404);
            }

            return sendSuccessResponse('Records retrieved successfully', $results);
        } catch (\Exception $e) {
            return sendErrorResponse($e);
        }
    }

    public function emailLogs(): JsonResponse
    {
        request()->validate([
            'company_id' => 'required|integer|exists:companies,id',
        ]);
        $emails = EmailLog::query();
        $emails->where('company_id', request('company_id'));

        try {
            $results = handleApiRequest(request(), $emails);

            // Convert $results to a collection if it's an array
            $results = collect($results);
            if ($results->isEmpty()) {
                return sendErrorResponse('No records found', 404);
            }

            return sendSuccessResponse('Records retrieved successfully', $results);
        } catch (\Exception $e) {
            return sendErrorResponse($e);
        }
    }

    public function store(StoreEmailRequest $request): JsonResponse
    {
        $email = Email::create($request->validated());
        return sendSuccessResponse('Email created', $email, 201);
    }

    public function update(UpdateEmailRequest $request, Email $email): Email
    {
        $email->update($request->validated());
        return $email;
    }

    public function sendEmail(SendEmailRequest $request): JsonResponse
    {
        // Validate the incoming payload
        $validated = $request->validated();

        // Fetch the SMTP configuration based on smtp_config_id
        $smtpConfig = SmtpConfig::find($validated['smtp_config_id']);
        if (!$smtpConfig) {
            return response()->json(['error' => 'SMTP configuration not found.'], 400);
        }

        $emailDraft = null;
        // Fetch the email draft based on email_draft_id
        if (isset($validated['email_draft_id'])) {
            $emailDraft = Email::find($validated['email_draft_id']);
        }
        if (!$emailDraft && isset($validated['email_draft_id'])) {
            return response()->json(['error' => 'Email draft not found.'], 400);
        }

        if (!$emailDraft) {
            $emailDraft = [
                'id' => null,
                'subject' => request()->subject,
                'body' => request()->message
            ];
        }


        // Loop through the emails and dispatch jobs
        foreach ($validated['emails'] as $toEmail) {
            try {
                $log = EmailLog::create([
                    'company_id' => $validated['company_id'],
                    'smtp_config_id' => $validated['smtp_config_id'],
                    'email_draft_id' => $emailDraft['id'],
                    'to' => $toEmail,
                    'subject' => $emailDraft['subject'],
                    'message' => $emailDraft['body'],
                    'status' => 'pending',
                    'created_at' => now(),
                ]);
                // Dispatch the job for each recipient
                dispatch(new SendEmailJob(
                    $validated['company_id'],
                    $smtpConfig,
                    $toEmail,
                    $request->subject ?? $emailDraft->subject,
                    $request->message ?? $emailDraft->message,
                    $log
                ));
            } catch (Exception $e) {
                // Log the failure if required
                EmailLog::create([
                    'company_id' => $validated['company_id'],
                    'smtp_config_id' => $validated['smtp_config_id'],
                    'email_draft_id' => $emailDraft['id'],
                    'to' => $toEmail,
                    'subject' => $emailDraft['subject'],
                    'message' => $emailDraft['body'],
                    'status' => 'failed',
                    'created_at' => now(),
                ]);
            }
        }

        return response()->json(['message' => 'Email job(s) dispatched successfully!']);
    }
}
