<?php

namespace Modules\TransactionManagement\Http\Controllers\Api\New\Driver;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\TransactionManagement\Service\Interface\TransactionServiceInterface;
use Modules\TransactionManagement\Transformers\TransactionResource;

class DriverTransactionController extends Controller
{

    protected $transactionService;

    public function __construct(TransactionServiceInterface $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    public function list(Request $request)
    {

        $criteria = ['user_id' => auth()->user()->id];
        if (!is_null($request->type)) {
            $criteria['account'] = $request->type;
        }
        $data = $this->transactionService->getBy(criteria: $criteria, relations: ['user'], limit: $request->limit, offset: $request->offset,orderBy:['updated_at'=>'desc']);
        $transactions = TransactionResource::collection($data);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $transactions, limit: $request->limit, offset: $request->offset));
    }

    public function payableTransactionHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|integer',
            'offset' => 'required|integer',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $criteria = [
            'user_id' => auth()->user()->id,
            'attribute' => 'admin_cash_collect',
            'account' => 'payable_balance',
        ];
        $data = $this->transactionService->getBy(criteria: $criteria, relations: ['user'], limit: $request->limit, offset: $request->offset,orderBy:['updated_at'=>'desc']);
        $transactions = TransactionResource::collection($data);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $transactions, limit: $request->limit, offset: $request->offset));
    }
    public function walletTransactionHistory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|integer',
            'offset' => 'required|integer',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $criteria = [
            'user_id' => auth()->user()->id,
            'attribute' => 'level_reward',
        ];
        $data = $this->transactionService->getBy(criteria: $criteria, relations: ['user'], limit: $request->limit, offset: $request->offset,orderBy:['updated_at'=>'desc']);
        $transactions = TransactionResource::collection($data);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $transactions, limit: $request->limit, offset: $request->offset));
    }
}
