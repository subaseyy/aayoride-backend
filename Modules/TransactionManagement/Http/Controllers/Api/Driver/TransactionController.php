<?php

namespace Modules\TransactionManagement\Http\Controllers\Api\Driver;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\TransactionManagement\Interfaces\TransactionInterface;
use Modules\TransactionManagement\Traits\TransactionTrait;
use Modules\TransactionManagement\Transformers\TransactionResource;
use Modules\UserManagement\Interfaces\CustomerInterface;
use Modules\UserManagement\Interfaces\UserAccountInterface;
use Modules\UserManagement\Repositories\DriverRepository;

class TransactionController extends Controller
{

    public function __construct(
        private TransactionInterface $transaction,
    )
    {
    }


    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric',
            'offset' => 'required|numeric',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $attributes = ['user_id' => auth('api')->id()];
        if (!is_null($request->type)) {
            $attributes['column'] = 'account';
            $attributes['value'] = $request->type;
        }
        $data = $this->transaction->get(limit: $request->limit, offset: $request->offset, dynamic_page: true, attributes: $attributes, relations: ['user']);
        $transactions = TransactionResource::collection($data);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $transactions, limit: $request->limit, offset: $request->offset));
    }

}
