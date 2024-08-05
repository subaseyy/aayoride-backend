<?php

namespace Modules\UserManagement\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Entities\AppNotification;
use Modules\UserManagement\Interfaces\AppNotificationInterface;
use Modules\UserManagement\Transformers\AppNotificationResource;

class AppNotificationController extends Controller
{

    public function __construct(
        private AppNotificationInterface $notification)
    {
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric',
            'offset' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }
        $notifications = $this->notification->get(
            limit: $request->limit,
            offset: $request->offset,
            dynamic_page: true,
            attributes: [
                'column' => 'user_id',
                'value' => auth('api')->id()
            ]);
        $notifications = AppNotificationResource::collection($notifications);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $notifications, limit: $request->limit, offset: $request->offset));
    }

}
