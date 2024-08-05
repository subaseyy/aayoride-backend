<?php

namespace Modules\ReviewModule\Http\Controllers\Api\New;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\ReviewModule\Http\Requests\ReviewRequest;
use Modules\ReviewModule\Service\Interface\ReviewServiceInterface;
use Modules\ReviewModule\Transformers\ReviewResource;
use Modules\TripManagement\Service\TripRequestService;
use Modules\UserManagement\Lib\LevelHistoryManagerTrait;
use Modules\UserManagement\Lib\LevelUpdateCheckerTrait;

class ReviewController extends Controller
{
//    use LevelHistoryManagerTrait;
    use LevelUpdateCheckerTrait;
    protected $reviewService;
    protected $tripService;
    public function __construct(ReviewServiceInterface $reviewService, TripRequestService $tripService)
    {
        $this->reviewService = $reviewService;
        $this->tripService = $tripService;
    }


    public function index(Request $request): JsonResponse
    {
        $criteria = [
            'received_by' => auth()->id(),
        ];
        if (!is_null($request->is_saved)) {
            $criteria= array_merge($criteria,[
                'is_saved' => $request->is_saved
            ]);
        }
        $whereHasRelation = [
            'trip'=>[]
        ];
        $review = $this->reviewService->getBy(criteria: $criteria,whereHasRelations: $whereHasRelation, relations: ['givenUser', 'trip'], orderBy: ['created_at'=>'desc'], limit: $request->limit, offset: $request->offset);

        $review = ReviewResource::collection($review);
        return response()->json(responseFormatter(constant: DEFAULT_200, content: $review, limit: $request->limit, offset: $request->offset));
    }


    public function store(ReviewRequest $request): JsonResponse
    {

        $route = str_contains($request->route()?->getPrefix(), 'customer');
        $key = $route ? 'customer_review' : 'driver_review';
        if (!businessConfig($key)->value ?? 0) {

            return response()->json(responseFormatter(REVIEW_SUBMIT_403), 403);
        }

        $tripRequest = $this->tripService->findOne($request['ride_request_id']);
        $user = auth('api')->user();
        if ($tripRequest && ($tripRequest->customer_id == $user->id || $tripRequest->driver_id == $user->id)) {
            $review = $this->reviewService->findOneBy(criteria: [['trip_request_id', $tripRequest->id], ['given_by', $request->user()->id]]);
            if (!$review) {
                DB::beginTransaction();
                $this->reviewService->apiReviewStore($user, $tripRequest, $request->all());
//                $this->reviewCountChecker($user);
                DB::commit();
                if ($user->user_type == 'driver'){
                    $this->driverLevelUpdateChecker($user);
                    $push = getNotification('review_from_driver');
                    sendDeviceNotification(fcm_token: $tripRequest->customer->fcm_token,
                        title: translate('you_got_a_new_review'),
                        description: translate($push['description']),
                        ride_request_id: $request['trip_request_id'],
                        type: $tripRequest->type,
                        action: 'review_submit',
                        user_id: $tripRequest->customer->id
                    );
                }else{
                    $this->customerLevelUpdateChecker($user);
                    $push = getNotification('review_from_customer');
                    sendDeviceNotification(fcm_token: $tripRequest->driver->fcm_token,
                        title: translate('you_got_a_new_review'),
                        description: translate($push['description']),
                        ride_request_id: $request['trip_request_id'],
                        type: $tripRequest->type,
                        action: 'review_submit',
                        user_id: $tripRequest->driver->id
                    );
                }

                return response()->json(responseFormatter(DEFAULT_STORE_200));
            }
            return response()->json(responseFormatter(REVIEW_403));
        }
        return response()->json(responseFormatter(DEFAULT_404), 403);
    }


    public function save($id): JsonResponse
    {
        $review = $this->reviewService->findOne(id: $id);
        if ($review && $review->received_by == auth('api')->id()) {
            $isSaved = $review->is_saved == 0 ? 1 : 0;
            $this->reviewService->update(id: $review->id, data: ['is_saved' => $isSaved]);

            return response()->json(responseFormatter(DEFAULT_UPDATE_200));
        }

        return response()->json(responseFormatter(DEFAULT_404), 403);
    }


    public function checkSubmission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }

        $review = $this->reviewService->getBy(criteria: [
            'given_by' => auth('api')->id(),
            'trip_request_id' => $request->trip_request_id
        ]);

        if (!$review) {

            return response()->json(responseFormatter(DEFAULT_200));
        }

        return response()->json(responseFormatter(constant: DEFAULT_200, content: ReviewResource::collection($review)));
    }
}
