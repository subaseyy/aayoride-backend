<?php

namespace Modules\VehicleManagement\Http\Controllers\Api\Driver;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\VehicleManagement\Interfaces\VehicleInterface;

class VehicleController extends Controller
{
    private VehicleInterface $vehicle;

    public function __construct(VehicleInterface $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
       $validator = Validator::make($request->all(), [
            'brand_id' => 'required',
            'model_id' => 'required',
            'category_id' => 'required',
            'licence_plate_number' => 'required',
            'licence_expire_date' => 'required|date',
            'vin_number' => 'required',
            'transmission' => 'required',
            'fuel_type' => 'required',
            'upload_documents' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        try {
            if ($this->vehicle->getBy(column:'driver_id', value:$request->driver_id)->exists()) {
                return response()->json(responseFormatter(constant: VEHICLE_DRIVER_EXISTS_403, errors: errorProcessor($validator)), 403);
            }
        } catch (\Exception $ex) {

            $request->merge([
                'ownership' => 'driver',

            ]);
            $this->vehicle->store(attributes: $request->all());
            return response()->json(responseFormatter(VEHICLE_CREATE_200), 200);
        }


    }

}
