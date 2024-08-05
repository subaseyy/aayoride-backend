<?php

namespace Modules\VehicleManagement\Http\Controllers\Api\New\Driver;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\VehicleManagement\Http\Requests\VehicleApiStoreUpdateRequest;
use Modules\VehicleManagement\Interfaces\VehicleInterface;
use Modules\VehicleManagement\Service\Interface\VehicleServiceInterface;

class VehicleController extends Controller
{
    protected $vehicleService;


    public function __construct(VehicleServiceInterface $vehicleService)
    {
        $this->vehicleService = $vehicleService;
    }

    public function store(VehicleApiStoreUpdateRequest $request)
    {

        if ($this->vehicleService->findOneBy(['driver_id' => $request->driver_id])) {
            return response()->json(responseFormatter(constant: VEHICLE_DRIVER_EXISTS_403), 403);
        }

        $this->vehicleService->create(data: $request->validated());
        return response()->json(responseFormatter(VEHICLE_CREATE_200), 200);
    }
}
