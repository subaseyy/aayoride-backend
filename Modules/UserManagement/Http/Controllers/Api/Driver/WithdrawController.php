<?php

namespace Modules\UserManagement\Http\Controllers\Api\Driver;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\TransactionManagement\Traits\TransactionTrait;
use Modules\UserManagement\Interfaces\WithdrawalMethodInterface;
use Modules\UserManagement\Interfaces\WithdrawRequestInterface;
use Modules\UserManagement\Transformers\WithdrawMethodResource;

class WithdrawController extends Controller
{
    use TransactionTrait;
    public function __construct(
        private WithdrawalMethodInterface $method,
        private WithdrawRequestInterface $withdraw_request
    )
    {
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
        $method = $this->method->get(limit: $request->limit, offset: $request->offset, dynamic_page: true, attributes: ['query' => 'is_active', 'value' => 1]);
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

        $method = $this->method->getBy(column: 'id', value: $request->withdraw_method);
        $fields = array_column($method->method_fields, 'input_name');
        $values = $request->all();

        $data = [];
        foreach ($fields as $field) {
            if(key_exists($field, $values)) {
                $data[$field] = $values[$field];
            }
        }
        $user = auth('api')->user();
        $account = $user->userAccount;
        if ($account->receivable_balance < $request->amount)  {

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
                $attributes['note'] = $request->note;
            }
            $attribute = $this->withdraw_request->store($attributes);
            $this->withdrawRequestTransaction($user, $request->amount, $attribute);
        DB::commit();

        return response()->json(responseFormatter(WITHDRAW_REQUEST_200));
    }

}
