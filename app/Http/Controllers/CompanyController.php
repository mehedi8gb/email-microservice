<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CompanyController extends Controller
{

    public function index(Request $request): array|JsonResponse
    {
        $query = Company::query();
        $query->where('user_id', auth()->id());

        try {
            return handleApiRequest($request, $query, ['smtpConfigs', 'emails', 'user']);
        } catch (\Exception $e) {
            return sendErrorResponse($e);
        }
    }

    public function show(Company $company): JsonResponse
    {
        try {
            Gate::authorize('show', $company);
            return sendSuccessResponse(
                'Company retrieved',
                CompanyResource::make($company->load(['smtpConfigs', 'emails', 'user']))
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
        Gate::authorize('update', $company);
        $company->update($request->validated());

        return sendSuccessResponse('Company updated', CompanyResource::make($company));
    }


    public function destroy(Company $company): JsonResponse
    {
        Gate::authorize('destroy', $company);

        $company->delete();
        return sendErrorResponse('Company deleted', 204);
    }
}
