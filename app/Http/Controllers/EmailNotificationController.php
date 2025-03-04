<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmailNotificationRequest;
use App\Http\Requests\UnsubscribeEmailNotificationRequest;
use App\Http\Resources\EmailNotificationResource;
use App\Models\EmailNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailNotificationController extends Controller
{
    public function subscribe(StoreEmailNotificationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['enabled'] = true;

        $emailNotification = EmailNotification::create($data);
        return sendSuccessResponse('Successfully subscribed for notifications.', EmailNotificationResource::make($emailNotification), 201);
    }

    public function unsubscribe(UnsubscribeEmailNotificationRequest $request): JsonResponse
    {
        EmailNotification::where('email', $request->validated()['email'])->delete();
        return sendSuccessResponse('Successfully unsubscribed from notifications.');
    }

    public function listSubscribers(Request $request): array|JsonResponse
    {
        $query = EmailNotification::query();
        $query->where('company_id', $request->company_id);
        $result = handleApiRequest($request, $query);

        try {
            return sendSuccessResponse('Subscribers retrieved', $result);
        } catch (\Exception $e) {
            return sendErrorResponse($e);
        }
    }
}
