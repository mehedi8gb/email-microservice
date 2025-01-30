<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSmtpConfigRequest;
use App\Http\Requests\UpdateSmtpConfigRequest;
use App\Http\Resources\SmtpConfigResource;
use App\Models\Company;
use App\Models\SmtpConfig;
use Egulias\EmailValidator\Parser\Comment;
use Illuminate\Http\JsonResponse;

class SmtpConfigController extends Controller
{
    public function store(StoreSmtpConfigRequest $request): JsonResponse
    {
        $smtp = SmtpConfig::create($request->validated());

        return sendSuccessResponse('SMTP Config created', SmtpConfigResource::make($smtp), 201);
    }

    public function update(UpdateSmtpConfigRequest $request, SmtpConfig $smtp): JsonResponse
    {
        $smtp->update($request->validated());

        return sendSuccessResponse('SMTP Config updated', SmtpConfigResource::make($smtp));
    }


    public function show($company_id): JsonResponse
    {
        $company = Company::with('smtpConfigs')->find($company_id);

        if (!$company) {
            return sendErrorResponse('Company not found', 404);
        }

        return sendSuccessResponse('SMTP Config retrieved', SmtpConfigResource::collection($company->smtpConfigs));
    }

    public function index(): JsonResponse
    {
        request()->validate([
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        $query = SmtpConfig::query();
        $query->where('company_id', request()->company_id);

        try {
            $results = handleApiRequest(request(), $query);

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
}

