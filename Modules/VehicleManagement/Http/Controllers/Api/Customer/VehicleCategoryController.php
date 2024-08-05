<?php

namespace Modules\VehicleManagement\Http\Controllers\Api\Customer;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\VehicleManagement\Interfaces\VehicleCategoryInterface;
use Modules\VehicleManagement\Transformers\VehicleCategoryResource;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VehicleCategoryController extends Controller
{
    private VehicleCategoryInterface $category;

    public function __construct(VehicleCategoryInterface $category)
    {
        $this->category = $category;
    }

    /**
     * Category List with Fares by Zone
     * @param Request $request
     * @return JsonResponse
     */
    public function categoryFareList(Request $request):JsonResponse
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
        $attributes = ['column_name' => 'zone_id', 'column_value' => $request->header('zoneId'), 'whereHas' => 'tripFares'];
        $data = $this->category->get(limit: $request['limit'], offset: $request['offset'],dynamic_page: true, attributes: $attributes, relations: ['tripFares']);

        $data = VehicleCategoryResource::collection($data);

        return response()->json(responseFormatter(constant: DEFAULT_200,content: $data, limit:$request['limit'], offset: $request['offset']));

    }

}
