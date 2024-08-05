<?php

namespace Modules\ParcelManagement\Http\Controllers\Api\New\Customer;

use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ParcelManagement\Service\Interface\ParcelWeightServiceInterface;
use Modules\VehicleManagement\Service\Interface\VehicleCategoryServiceInterface;
use Modules\VehicleManagement\Service\Interface\VehicleModelServiceInterface;

class ParcelController extends Controller
{

    protected $vehicleCategoryService;
    protected $vehicleModelService;
    protected $parcelWeightService;
    public function __construct(VehicleCategoryServiceInterface $vehicleCategoryService, VehicleModelServiceInterface $vehicleModelService, ParcelWeightServiceInterface $parcelWeightService)
    {
        $this->vehicleCategoryService = $vehicleCategoryService;
        $this->vehicleModelService = $vehicleModelService;
        $this->parcelWeightService = $parcelWeightService;
    }

    public function vehicleList(Request $request)
    {
        if (empty($request->header('zoneId'))) {

            return response()->json(responseFormatter(ZONE_404), 200);
        }

        $parcelWeight = $this->parcelWeightService->findOne(id: $request['weight_id']);
        if (!$parcelWeight) {
            return response()->json(responseFormatter(PARCEL_WEIGHT_400), 403);
        }

        $criteria = [
            ['maximum_weight', '>=', $parcelWeight->max_weight],
            ['is_active', 1],
        ];

        $vehicleModels = $this->vehicleModelService->getBy(criteria: $criteria);
        if (count($vehicleModels) < 1) {
            return response()->json(responseFormatter(PARCEL_WEIGHT_400), 403);
        }


        $relations = [
            'vehicles.driver',
            'vehicles.model' => [
                ['maximum_weight', '>', $parcelWeight->max_weight],
            ]
        ];

        $categories = $this->vehicleCategoryService->getBy(criteria: ['is_active' => 1],relations: $relations, limit: $request['limit'], offset: $request['offset']);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $categories, limit: $request['limit'], offset: $request['offset']));
    }
}
