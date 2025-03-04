<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailRequest;
use App\Http\Requests\StoreEmailRequest;
use App\Http\Requests\UpdateEmailRequest;
use App\Models\Email;
use App\Jobs\SendEmailJob;
use App\Models\EmailLog;
use App\Models\EmailTemplate;
use App\Models\SmtpConfig;
use Exception;
use Illuminate\Http\JsonResponse;

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

        // Fetch SMTP Configuration
        $smtpConfig = SmtpConfig::find($validated['smtp_config_id']);
        if (!$smtpConfig) {
            return response()->json(['error' => 'SMTP configuration not found.'], 400);
        }

        // Fetch Email Draft or Template
        $emailDraft = null;
        if (isset($validated['email_draft_id'])) {
            $emailDraft = Email::find($validated['email_draft_id']);
        } elseif (isset($validated['template_type'])) {
            $emailDraft = EmailTemplate::where('name', $validated['template_type'])
                ->where('company_id', $validated['company_id'])
                ->first();
        }

        if (!$emailDraft) {
            return response()->json(['error' => 'No valid email draft or template found.'], 400);
        }

        // Replace placeholders in template if provided
        $emailMessage = $emailDraft->body ?? $validated['message'];
        if (isset($validated['template_type'])) {
            $template = EmailTemplate::where('name', $validated['template_type'])->first();
        }

        if (isset($template)) {
            $emailMessage = str_replace(
                array_map(fn($key) => "{{" . $key . "}}", array_keys($validated['variables'])),
                array_values($validated['variables']),
                $template->body
            );
        }


        // Dispatch Jobs for Each Email
        foreach ($validated['emails'] as $toEmail) {
            try {
                $log = EmailLog::create([
                    'company_id' => $validated['company_id'],
                    'smtp_config_id' => $validated['smtp_config_id'],
                    'email_draft_id' => $emailDraft->id ?? null,
                    'to' => $toEmail,
                    'subject' => $emailDraft->subject ?? $validated['subject'],
                    'message' => $emailMessage,
                    'status' => 'pending',
                    'created_at' => now(),
                ]);

                dispatch(new SendEmailJob(
                    $validated['company_id'],
                    $smtpConfig,
                    $toEmail,
                    $emailDraft->subject ?? $validated['subject'],
                    $emailMessage,
                    $log
                ));
            } catch (Exception $e) {
                EmailLog::create([
                    'company_id' => $validated['company_id'],
                    'smtp_config_id' => $validated['smtp_config_id'],
                    'email_draft_id' => $emailDraft->id ?? null,
                    'to' => $toEmail,
                    'subject' => $emailDraft->subject ?? $validated['subject'],
                    'message' => $emailMessage,
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                    'created_at' => now(),
                ]);
            }
        }

        // Handle Notifications
        if (isset($validated['notify_users'])) {
            foreach ($validated['notify_users'] as $notifyEmail) {
                dispatch(new SendEmailJob(
                    $validated['company_id'],
                    $smtpConfig,
                    $notifyEmail,
                    "Email Notification",
                    "An email has been sent to: " . implode(", ", $validated['emails']),
                    null
                ));
            }
        }

        return sendSuccessResponse('Email job(s) dispatched successfully!');
    }
}
