<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSmtpConfigRequest;
use App\Http\Requests\UpdateSmtpConfigRequest;
use App\Http\Resources\SmtpConfigResource;
use App\Models\Company;
use App\Models\SmtpConfig;
use Egulias\EmailValidator\Parser\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class SmtpConfigController extends Controller
{
    public function store(StoreSmtpConfigRequest $request): JsonResponse
    {
        $smtp = SmtpConfig::create($request->validated());

        return sendSuccessResponse('SMTP Config created', SmtpConfigResource::make($smtp), 201);
    }

    public function update(UpdateSmtpConfigRequest $request, SmtpConfig $smtp): JsonResponse
    {
        Gate::authorize('update', $smtp);
        $smtp->update($request->validated());

        return sendSuccessResponse('SMTP Config updated', SmtpConfigResource::make($smtp));
    }


    /**
     * @throws \Exception
     */
    public function show($smtpConfigId): JsonResponse
    {
        request()->validate([
            'company_id' => 'required|integer|exists:companies,id',
        ]);

        // Ensure the SMTP Config exists
        $data = SmtpConfig::findOrCustomFail($smtpConfigId);
        $data->where('company_id', request()->company_id);

        // Authorization check
        Gate::authorize('show', $data);

        return sendSuccessResponse('SMTP Config retrieved', new SmtpConfigResource($data));
    }

    public function destroy(SmtpConfig $smtp): JsonResponse
    {
        Gate::authorize('destroy', $smtp);
        $smtp->delete();

        return sendSuccessResponse('SMTP Config deleted');
    }

    public function index(): JsonResponse
    {
        request()->validate([
            'company_id' => 'required|integer',
        ]);
        $query = SmtpConfig::with('company')
            ->where('company_id', request()->company_id)
            ->whereHas('company', function ($q) {
                $q->where('user_id', auth()->id());
            });

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

