<?php

namespace Modules\VehicleManagement\Http\Controllers\Api\Driver;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\VehicleManagement\Interfaces\VehicleBrandInterface;
use Modules\VehicleManagement\Transformers\VehicleBrandResource;

class VehicleBrandController extends Controller
{
    private VehicleBrandInterface $brand;

    public function __construct(VehicleBrandInterface $brand)
    {
        $this->brand = $brand;
    }
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function brandList(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric|min:1|max:200',
            'offset' => 'required|numeric|min:1|max:100000'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(DEFAULT_400, null, null, null, errorProcessor($validator)), 400);
        }

        $attributes = ['query' => 'is_active', 'value' => 'active'];
        $relations = ['vehicleModels'];
        $list = $this->brand->get(
            limit:$request['limit'],
            offset:$request['offset'],
            dynamic_page:true,
            attributes:$attributes,
            relations:$relations);
        $brandList = VehicleBrandResource::collection($list);

        return response()->json(responseFormatter(constant: DEFAULT_200,content:$brandList, limit:$request['limit'], offset: $request['offset']), 200);
    }

}
