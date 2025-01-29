<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSMTPRequest;
use App\Http\Requests\UpdateSMTPRequest;
use App\Http\Resources\SmtpResource;
use App\Models\SmtpConfig;
use Illuminate\Http\JsonResponse;

class SMTPController extends Controller
{
    public function store(StoreSMTPRequest $request): JsonResponse
    {
        $smtp = SmtpConfig::create($request->validated());

        return sendSuccessResponse('SMTP Config created', SmtpResource::make($smtp), 201);
    }

    public function update(UpdateSMTPRequest $request, SmtpConfig $smtp): JsonResponse
    {
        $smtp->update($request->validated());

        return sendSuccessResponse('SMTP Config updated', SmtpResource::make($smtp));
    }


    public function show($company_id): JsonResponse
    {
        $smtp = SmtpConfig::where('company_id', $company_id)->firstOrFail();
        return sendSuccessResponse('SMTP Config retrieved', SmtpResource::make($smtp));
    }
}

