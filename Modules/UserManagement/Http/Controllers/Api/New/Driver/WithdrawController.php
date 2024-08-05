<?php

namespace Modules\UserManagement\Http\Controllers\Api\New\Driver;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\TransactionManagement\Traits\TransactionTrait;
use Modules\UserManagement\Interfaces\WithdrawRequestInterface;
use Modules\UserManagement\Service\Interface\UserAccountServiceInterface;
use Modules\UserManagement\Service\Interface\WithdrawMethodServiceInterface;
use Modules\UserManagement\Service\Interface\WithdrawRequestServiceInterface;
use Modules\UserManagement\Transformers\WithdrawMethodResource;
use Modules\UserManagement\Transformers\WithdrawRequestResource;

class WithdrawController extends Controller
{
    use TransactionTrait;

    protected $withdrawMethodService;
    protected $withdrawRequestService;
    protected $userAccountService;

    public function __construct(WithdrawMethodServiceInterface $withdrawMethodService, WithdrawRequestServiceInterface $withdrawRequestService, UserAccountServiceInterface $userAccountService)
    {
        $this->withdrawMethodService = $withdrawMethodService;
        $this->withdrawRequestService = $withdrawRequestService;
        $this->userAccountService = $userAccountService;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function methods(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|integer',
            'offset' => 'required|integer',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $method = $this->withdrawMethodService->getBy(criteria: ['is_active' => 1], limit: $request->limit, offset: $request->offset);
        $method = WithdrawMethodResource::collection($method);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $method, limit: $request->limit, offset: $request->offset));

    }

    /**
     * Show the form for creating a new resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'withdraw_method' => 'required',
            'amount' => 'required',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        if (auth()->user()->user_type != 'driver') {

            return response()->json(responseFormatter(DEFAULT_403), 403);
        }
        $method = $this->withdrawMethodService->findOne(id: $request->withdraw_method);
        $fields = array_column($method?->method_fields, 'input_name');
        $values = $request->all();

        $data = [];
        foreach ($fields as $field) {
            if (key_exists($field, $values)) {
                $data[$field] = $values[$field];
            }
        }
        $user = auth('api')->user();
        $account = $user->userAccount;
        if (($account?->receivable_balance - $account?->payable_balance) <= $request->amount) {
            return response()->json(responseFormatter(INSUFFICIENT_FUND_403), 403);
        }

        DB::beginTransaction();
        $attributes = [
            'user_id' => $user->id,
            'amount' => $request->amount,
            'method_id' => $request->withdraw_method,
            'method_fields' => $data,
        ];
        if (!is_null($request->note)) {
            $attributes['driver_note'] = $request->note;
        }
        $attribute = $this->withdrawRequestService->create(data: $attributes);
        if ($account?->payable_balance == 0){
            $this->withdrawRequestWithoutAdjustTransaction($user, $request->amount, $attribute);
        }
        if ($account?->payable_balance > 0 && $account?->receivable_balance > $account?->payable_balance){
            $this->withdrawRequestWithAdjustTransaction($user, $request->amount, $attribute);
        }
        DB::commit();

        return response()->json(responseFormatter(WITHDRAW_REQUEST_200));
    }

    public function getPendingWithdrawRequests(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|integer',
            'offset' => 'required|integer',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $criteria = [
            'user_id' => auth()->id()
        ];
        $whereInCriteria = [
            'status' => [PENDING, APPROVED, DENIED]
        ];
        $withdrawRequests = $this->withdrawRequestService->getBy(criteria: $criteria, whereInCriteria: $whereInCriteria, orderBy: ['created_at' => 'desc'], limit: $request->limit, offset: $request->offset);
        $withdrawRequests = WithdrawRequestResource::collection($withdrawRequests);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $withdrawRequests, limit: $request->limit, offset: $request->offset,));
    }

    public function getSettledWithdrawRequests(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|integer',
            'offset' => 'required|integer',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $criteria = [
            'user_id' => auth()->id(),
            'status' => SETTLED
        ];
        $withdrawRequests = $this->withdrawRequestService->getBy(criteria: $criteria, orderBy: ['created_at' => 'desc'], limit: $request->limit, offset: $request->offset);
        $withdrawRequests = WithdrawRequestResource::collection($withdrawRequests);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $withdrawRequests, limit: $request->limit, offset: $request->offset));
    }
}
