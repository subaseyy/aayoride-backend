<?php

namespace Modules\VehicleManagement\Http\Controllers\Api\Driver;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\VehicleManagement\Interfaces\VehicleModelInterface;
use Modules\VehicleManagement\Transformers\VehicleModelResource;

class VehicleModelController extends Controller
{
    private VehicleModelInterface $model;

    public function __construct(VehicleModelInterface $model)
    {
        $this->model = $model;
    }
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function modelList(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(DEFAULT_400, null, null, null, errorProcessor($validator)), 400);
        }

        if (empty($request->header('zoneId'))) {

            return response()->json(responseFormatter(ZONE_404), 200);
        }

        $attributes = ['query' => 'is_active', 'value' => 'active'];
        $list = $this->model->get(limit:$request['limit'], offset:$request['offset'], dynamic_page:true, attributes:$attributes);
        $modelList = VehicleModelResource::collection($list);

        return response()->json(responseFormatter(constant: DEFAULT_200,content:$modelList, limit:$request['limit'], offset: $request['offset']), 200);
    }
}
