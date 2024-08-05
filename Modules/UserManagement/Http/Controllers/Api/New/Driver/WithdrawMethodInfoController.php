<?php

namespace Modules\UserManagement\Http\Controllers\Api\New\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Service\Interface\DriverWithdrawMethodInfoServiceInterface;
use Modules\UserManagement\Service\Interface\WithdrawMethodServiceInterface;
use Modules\UserManagement\Transformers\UserWithdrawMethodInfoResource;

class WithdrawMethodInfoController extends Controller
{
    protected $driverWithdrawMethodInfoService;
    protected $withdrawMethodService;

    public function __construct(DriverWithdrawMethodInfoServiceInterface $driverWithdrawMethodInfoService, WithdrawMethodServiceInterface $withdrawMethodService)
    {
        $this->driverWithdrawMethodInfoService = $driverWithdrawMethodInfoService;
        $this->withdrawMethodService = $withdrawMethodService;
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|integer',
            'offset' => 'required|integer',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $user = auth()->user();
        $withdrawMethodInfos = $this->driverWithdrawMethodInfoService->getBy(criteria: ['user_id' => $user->id, 'is_active' => 1], limit: $request->limit, offset: $request->offset);
        $withdrawMethodInfos = UserWithdrawMethodInfoResource::collection($withdrawMethodInfos);
        return response()->json(responseFormatter(constant: DEFAULT_200, content: $withdrawMethodInfos, limit: $request->limit, offset: $request->offset));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'withdraw_method' => 'required',
            'method_name' => 'required|unique:user_withdraw_method_infos',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        if ($user?->user_type != 'driver') {

            return response()->json(responseFormatter(DEFAULT_403), 403);
        }
        $account = $this->driverWithdrawMethodInfoService->findOneBy(criteria:['user_id'=>$user->id, 'method_name'=>$request->method_name]);
        if ($account) {
            return response()->json(responseFormatter(WITHDRAW_METHOD_INFO_EXIST_403), 403);
        }

        $method = $this->withdrawMethodService->findOne(id: $request->withdraw_method);
        $fields = array_column($method->method_fields, 'input_name');
        $values = $request->all();

        $data = [];
        foreach ($fields as $field) {
            if (key_exists($field, $values)) {
                $data[$field] = $values[$field];
            }
        }

        DB::beginTransaction();
        $attributes = [
            'user_id' => $user?->id,
            'withdraw_method_id' => $request->withdraw_method,
            'method_name' => $request->method_name,
            'method_info' => $data,
        ];
        $this->driverWithdrawMethodInfoService->create(data: $attributes);
        DB::commit();

        return response()->json(responseFormatter(WITHDRAW_METHOD_INFO_STORE_200));
    }

    public function update($id, Request $request)
    {

        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'withdraw_method' => 'required',
            'method_name' => 'required|unique:user_withdraw_method_infos,method_name,' . $id,
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        if ($user?->user_type != 'driver') {

            return response()->json(responseFormatter(DEFAULT_403), 403);
        }

        $withdrawMethodInfo = $this->driverWithdrawMethodInfoService->findOne(id: $id);
        if (!$withdrawMethodInfo) {
            return response()->json(responseFormatter(DEFAULT_400), 403);
        }
        $method = $this->withdrawMethodService->findOne(id: $withdrawMethodInfo->withdraw_method_id);
        $fields = array_column($method->method_fields, 'input_name');
        $values = $request->all();

        $data = [];
        foreach ($fields as $field) {
            if (key_exists($field, $values)) {
                $data[$field] = $values[$field];
            }
        }

        DB::beginTransaction();
        $attributes = [
            'method_name' => $request->method_name,
            'method_info' => $data,
        ];
        $this->driverWithdrawMethodInfoService->update(id: $id, data: $attributes);
        DB::commit();

        return response()->json(responseFormatter(WITHDRAW_METHOD_INFO_UPDATE_200));
    }
}
