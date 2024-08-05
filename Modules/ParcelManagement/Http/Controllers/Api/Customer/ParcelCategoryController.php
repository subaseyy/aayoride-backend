<?php

namespace Modules\ParcelManagement\Http\Controllers\Api\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\FareManagement\Entities\ParcelFare;
use Modules\FareManagement\Interfaces\ParcelFareInterface;
use Modules\ParcelManagement\Entities\ParcelCategory;
use Modules\ParcelManagement\Interfaces\ParcelCategoryInterface;
use Modules\ParcelManagement\Transformers\ParcelCategoryResource;
use Modules\ParcelManagement\Transformers\ParcelFareResource;

class ParcelCategoryController extends Controller
{
    public function __construct(
        private ParcelCategoryInterface $category)
    {

    }

    public function categoryFareList(Request $request)
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

        $zoneId = $request->header('zoneId');

        $relations = ['weightFares', 'weightFares.parcelFare', 'weightFares.parcel_weight'];
        $attributes = ['column_name' => 'zone_id', 'column_value' => $zoneId, 'query' => 'is_active', 'value' => 'active', 'whereHas' => 'weightFares'];
        $list = $this->category->get(limit: $request['limit'], offset: $request['offset'], dynamic_page: true, attributes: $attributes, relations: $relations);
        $category_fare = ParcelCategoryResource::collection($list);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $category_fare, limit: $request['limit'], offset: $request['offset']));

    }


}
