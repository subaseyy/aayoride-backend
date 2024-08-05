<?php

namespace Modules\UserManagement\Http\Controllers\Api\New;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Service\Interface\AppNotificationServiceInterface;
use Modules\UserManagement\Transformers\AppNotificationResource;

class AppNotificationController extends Controller
{
    protected $appNotificationService;
    public function __construct(AppNotificationServiceInterface $appNotificationService)
    {
        $this->appNotificationService = $appNotificationService;
    }

    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric',
            'offset' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }
        $notifications = $this->appNotificationService->getBy(criteria: ['user_id'=>auth('api')->id()], limit: $request->limit, offset: $request->offset,);
        $notifications = AppNotificationResource::collection($notifications);
        return response()->json(responseFormatter(constant: DEFAULT_200, content: $notifications, limit: $request->limit, offset: $request->offset));
    }
}
