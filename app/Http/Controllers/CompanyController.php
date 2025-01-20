<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{

    public function index()
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

    public function store(StoreCompanyRequest $request): Builder|Model
    {
        return Company::create($request->validated());
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $company->update($request->validated());
        return $company;
    }


    public function destroy(Company $company)
    {
        $company->delete();
        return response()->json(['message' => 'Company deleted']);
    }
}

