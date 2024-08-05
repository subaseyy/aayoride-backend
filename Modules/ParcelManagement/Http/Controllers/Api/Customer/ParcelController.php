<?php

namespace Modules\ParcelManagement\Http\Controllers\Api\Customer;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ParcelManagement\Interfaces\ParcelWeightInterface;
use Modules\TripManagement\Entities\TripRequest;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Entities\UserLastLocation;
use Modules\VehicleManagement\Entities\Vehicle;
use Modules\VehicleManagement\Entities\VehicleCategory;
use Modules\VehicleManagement\Entities\VehicleModel;
use Modules\VehicleManagement\Interfaces\VehicleCategoryInterface;
use Modules\VehicleManagement\Interfaces\VehicleModelInterface;
use Modules\VehicleManagement\Transformers\VehicleCategoryResource;
use Illuminate\Support\Facades\Validator;
use Modules\TripManagement\Interfaces\TripRequestInterfaces;
use Modules\TripManagement\Transformers\TripRequestResource;

class ParcelController extends Controller
{
    public function __construct(
        private ParcelWeightInterface $parcelWeight,
        private VehicleModelInterface $vehicleModel,
        private VehicleCategoryInterface $vehicleCategory,
        private TripRequestInterfaces $trip,

        )
    {

    }
    public function vehicleList(Request $request)
    {
        $parcelWeight = $this->parcelWeight->getBy(column:'id', value:$request['weight_id'])->max_weight;
        $vehicleModels = $this->vehicleModel->getByComparison(attributes:[
            'weight' => $parcelWeight
        ]);
        $list_of_vehicle = [];

        $zone_id = $request->header('zoneId');
        $user = auth()->user();

        $relations = ['vehicles.model', 'vehicles.driver'];
        $attributes = ['column_name' => 'maximum_weight', 'column_value' => 1, 'whereHas' => 'vehicles.model', 'operator' => '>'];
        $list = $this->vehicleCategory->get(limit: 5, offset: 1,dynamic_page: true, attributes:$attributes, relations:$relations);


        return $list_of_vehicle;

    }

    public function orderDetails($ride_request_id)
    {
        $data = $this->trip->getBy( 'id', $ride_request_id, ['relations' => ['driver', 'parcel']]);
        $resource = TripRequestResource::make($data);

        return response()->json(responseFormatter(DEFAULT_200, $resource));
    }

    public function orderList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to' => 'required_with:from|date',
            'from' => 'required_with:to|date',
            'limit' => 'required|numeric',
            'offset' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }

        $attributes = [
            'type' => 'parcel',
            'column' => 'customer_id',
            'value' => $request->user()->id,
        ];

        if ($request['to']) {
            $attributes['from'] = Carbon::parse($request['from'])->startOfDay();
            $attributes['to'] = Carbon::parse($request['to'])->endOfDay();
        }

        $data = $this->trip->get(limit: $request['limit'], offset: $request['offset'], dynamic_page: true, attributes: $attributes);
        $resource = TripRequestResource::collection($data);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $resource, limit: $request['limit'], offset: $request['offset']));
    }

    public function statusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:completed,cancelled',
            'trip_request_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }
        $attributes = [
            'column' => 'id',
            'value' => $request->trip_request_id,
            'trip_status' => $request['status']
        ];
        $data = $this->trip->updateRelationalTable($attributes);

        return response()->json(responseFormatter(DEFAULT_UPDATE_200, $data));
    }

    public function trackDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_request_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }

        $trip = $this->trip->getBy(column: 'id', value: $request->trip_request_id, attributes: [
            'relations' => ['driver.lastLocations']
        ]);

        if (!$trip) {
            return response()->json(responseFormatter(TRIP_REQUEST_404), 403);
        }
        $trip = TripRequestResource::make($trip);

        return response()->json(responseFormatter(constant: DEFAULT_404, content: $trip), 403);
    }

    public function suggestedVehicleCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parcel_weight' => 'required' ,

        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }

        $attributes = ['column_name' => 'maximum_weight', 'column_value' => $request->parcel_weight, 'whereHas' => 'vehicles.model', 'operator' => '>='];
        $vehicleCategory = $this->vehicleCategory->get(limit: 9999, offset: 1, attributes: $attributes, relations: ['vehicles.model']);


        return response()->json(responseFormatter(constant: DEFAULT_200, content: $vehicleCategory));

    }

    private function nearestDriver($latitude, $longitude, $radius = 400)
    {
        /*
         * replace 6371000 with 6371 for kilometer and 3956 for miles
         */
        return UserLastLocation::selectRaw("* ,
                         ( 6371 * acos( cos( radians(?) ) *
                           cos( radians( latitude ) )
                           * cos( radians( longitude ) - radians(?)
                           ) + sin( radians(?) ) *
                           sin( radians( latitude ) ) )
                         ) AS distance", [$latitude, $longitude, $latitude])
            ->where('type', '=', 'driver')
            ->having("distance", "<", $radius)
            ->orderBy("distance",'asc')
            ->offset(0)
            ->limit(20)
            ->get();
    }
}
