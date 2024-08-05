<?php

namespace Modules\TransactionManagement\Http\Controllers\Api\New\Customer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\TransactionManagement\Service\Interface\TransactionServiceInterface;
use Modules\TransactionManagement\Transformers\TransactionResource;


class TransactionController extends Controller
{

    protected $transactionService;

    public function __construct(TransactionServiceInterface $transactionService)
    {
        $this->transactionService = $transactionService;
    }


    public function list(Request $request)
    {

        $criteria = ['user_id' => auth('api')->id()];
        if (!is_null($request->type)) {
            $criteria['account'] = $request->type;
        }
        $data = $this->transactionService->getBy(criteria: $criteria, relations: ['user'], limit: $request->limit, offset: $request->offset,orderBy:['updated_at'=>'desc']);
        $transactions = TransactionResource::collection($data);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $transactions, limit: $request->limit, offset: $request->offset));
    }
}
