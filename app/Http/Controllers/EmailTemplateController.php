<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailTemplateRequest;
use App\Http\Requests\UpdateEmailTemplateRequest;
use App\Http\Resources\EmailTemplateResource;
use App\Models\EmailTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EmailTemplateController extends Controller
{
    public function index(Request $request): array|JsonResponse
    {
        $query = EmailTemplate::query();

        $query->where('company_id', $request->company_id);
        $result = handleApiRequest($request, $query);

        try {
            return sendSuccessResponse('Email templates retrieved', $result);
        } catch (\Exception $e) {
            return sendErrorResponse($e);
        }
    }

    public function show(EmailTemplate $emailTemplate): JsonResponse
    {
        try {
            Gate::authorize('view', $emailTemplate);
            return sendSuccessResponse(
                'Email template retrieved',
                EmailTemplateResource::make($emailTemplate)
            );
        } catch (\Exception $e) {
            return sendErrorResponse($e);
        }
    }

    public function store(StoreEmailTemplateRequest $request): JsonResponse
    {
        $emailTemplate = EmailTemplate::create($request->validated());
        return sendSuccessResponse('Email template created', EmailTemplateResource::make($emailTemplate), 201);
    }

    public function update(UpdateEmailTemplateRequest $request, $emailTemplateId): JsonResponse
    {
//        Gate::authorize('update', $emailTemplate);
        $emailTemplate = EmailTemplate::findOrFail($emailTemplateId);
        $emailTemplate->update($request->validated());

        return sendSuccessResponse('Email template updated', EmailTemplateResource::make($emailTemplate));
    }

    public function destroy($emailTemplateId): JsonResponse
    {
//        Gate::authorize('delete', $emailTemplate);
        $emailTemplate = EmailTemplate::findOrFail($emailTemplateId);
        $emailTemplate->delete();
        return sendSuccessResponse('Email template deleted', null, 204);
    }
}


