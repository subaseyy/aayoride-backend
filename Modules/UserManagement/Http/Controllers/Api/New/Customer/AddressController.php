<?php

namespace Modules\UserManagement\Http\Controllers\Api\New\Customer;

use MatanYadaev\EloquentSpatial\Objects\Point;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\UserManagement\Service\Interface\UserAddressServiceInterface;
use Modules\UserManagement\Transformers\AddressResource;
use Modules\ZoneManagement\Service\Interface\ZoneServiceInterface;

class AddressController extends Controller
{
    protected $userAddressService;
    protected $zoneService;

    public function __construct(UserAddressServiceInterface $userAddressService, ZoneServiceInterface $zoneService)
    {
        $this->userAddressService = $userAddressService;
        $this->zoneService = $zoneService;
    }

    public function getAddresses(Request $request)
    {
        $user_id = auth()->user()->id;
        if (strcmp($request->user()->user_type, CUSTOMER_USER_TYPES) == 0) {
            $attributes = [
                'query' => 'user_id',
                'value' => $user_id
            ];

            $addresses = $this->address->get(limit:$request['limit'], offset:$request['offset'], dynamic_page:true, attributes:$attributes);
            $data = AddressResource::collection($addresses);

            return response()->json(responseFormatter(constant:DEFAULT_200, content:$data, limit:$request['limit'], offset:$request['offset']), 200);
        }
        return response()->json(responseFormatter(DEFAULT_403), 401);
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
            'address_label' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }

        $point = new Point($request->latitude,$request->longitude);
        $zone = $this->zone->getByPoints($point)->get(['id']);
        if($zone->count() == 0) {

            return response()->json(responseFormatter(constant:ZONE_RESOURCE_404), 403);
        }

        $user_id = auth()->user()->id;
        $request->merge([
            'user_id' => $user_id,
            'zone_id' => $zone[0]->id
        ]);
        $this->address->store(attributes:$request->all());
        return response()->json(responseFormatter(DEFAULT_STORE_200));
    }


    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return JsonResponse
     */
    public function edit($id)
    {
        $attributes = [
            'column' => 'id',
            'value' => [$id]
        ];
        $address = $this->address->getBy(column:'user_id', value:auth('api')->user()->id, attributes:$attributes);
        if (isset($address)) {
            return response()->json(responseFormatter(DEFAULT_200, $address), 200);
        }
        return response()->json(responseFormatter(DEFAULT_204), 200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'address' => 'required',
            'address_label' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 400);
        }

        $point = new Point($request->latitude,$request->longitude);
        $id = $request->id;
        $zone = $this->zone->getByPoints($point)->get(['id']);
        if($zone->count() == 0) {

            return response()->json(responseFormatter(constant:ZONE_RESOURCE_404), 403);
        }

        if(!$zone->first()) {

            return response()->json(responseFormatter(ZONE_RESOURCE_404, null, errorProcessor($validator)), 404);
        }
        $request->merge([
            'user_id' => auth('api')->id(),
            'zone_id' => $zone[0]->id
        ]);
        $this->address->update(attributes:$request->all(), id:$id);

        return response()->json(responseFormatter(constant: DEFAULT_UPDATE_200));

    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(DEFAULT_400, null, null, null, errorProcessor($validator)), 400);
        }

        $attributes = [
            'column' => 'id',
            'value' => [$request->address_id]
        ];
        $address = $this->address->getBy(column:'user_id', value:auth('api')->user()->id, attributes:$attributes);

        if (!isset($address)) {
            return response()->json(responseFormatter(DEFAULT_204), 200);
        }

        $address = $this->address->destroy(id:$request->address_id);

        return response()->json(responseFormatter(DEFAULT_DELETE_200), 200);
    }
}
