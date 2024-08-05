<?php

namespace Modules\UserManagement\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\TripManagement\Interfaces\TripRequestInterfaces;
use Modules\TripManagement\Repositories\TripRequestRepository;
use Modules\UserManagement\Entities\UserLastLocation;
use Modules\UserManagement\Repositories\UserLastLocationRepository;
use Modules\UserManagement\Transformers\LastLocationResource;

class UserController extends Controller
{
    public function __construct(private UserLastLocationRepository $location, private TripRequestRepository $tripRequest)
    {
    }
    public function storeLastLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'type' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'zone_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }
        if ($request->user_id){
            $attributes = [
                'user_id' => $request->user_id,
                'type' => $request->type,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'zone_id' => $request->zone_id
            ];
            $this->location->updateOrCreate($attributes);
        }
        return response()->json(responseFormatter(constant: DEFAULT_200, content: ''));
    }

    public function getLastLocation(Request $request){
        $trip = $this->tripRequest->getBy(column: 'id', value: $request['trip_request_id']);
        if (!$trip){
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404), 403);
        }
        $userLastLocation = UserLastLocation::where('user_id',$trip->user_id)->first();
        $latLocation = LastLocationResource::make($userLastLocation);
        return response()->json(responseFormatter(constant: DEFAULT_200, content:$latLocation ));
    }


}
