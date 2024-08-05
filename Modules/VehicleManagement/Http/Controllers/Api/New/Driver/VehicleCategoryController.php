<?php

namespace Modules\VehicleManagement\Http\Controllers\Api\New\Driver;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\VehicleManagement\Service\Interface\VehicleCategoryServiceInterface;
use Modules\VehicleManagement\Transformers\VehicleCategoryResource;

class VehicleCategoryController extends Controller
{

    protected $vehicleCategoryService;
    public function __construct(VehicleCategoryServiceInterface $category)
    {
        $this->vehicleCategoryService = $category;
    }

    public function list(Request $request)
    {
        $criteria['is_active'] =  1;

        $categories = $this->vehicleCategoryService->getBy(criteria: $criteria, limit: $request['limit'], offset: $request['offset']);
        $data = VehicleCategoryResource::collection($categories);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $data, limit: $request['limit'], offset: $request['offset']), 200);
    }
}
