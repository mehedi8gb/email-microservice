<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{

    public function index(): array|JsonResponse
    {
        $query = Company::query();

        try {
            return handleApiRequest(request(), $query, ['smtpConfig', 'emails']);
        } catch (\Exception $e) {
            return sendErrorResponse($e);
        }
    }

    public function show(Company $company): JsonResponse
    {
        try {
            return sendSuccessResponse(
                'Company retrieved',
                CompanyResource::make($company->load(['smtpConfig', 'emails']))
            );
        } catch (\Exception $e) {
            return sendErrorResponse($e);
        }
    }

    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $company = Company::create($request->validated());

        return sendSuccessResponse('Company created', CompanyResource::make($company), 201);
    }

    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $company->update($request->validated());

        return sendSuccessResponse('Company updated', CompanyResource::make($company));
    }


    public function destroy(Company $company): JsonResponse
    {
        $company->delete();
        return sendErrorResponse('Company deleted', 204);
    }
}
