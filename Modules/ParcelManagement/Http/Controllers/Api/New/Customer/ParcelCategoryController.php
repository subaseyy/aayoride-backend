<?php

namespace Modules\ParcelManagement\Http\Controllers\Api\New\Customer;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ParcelManagement\Service\Interface\ParcelCategoryServiceInterface;
use Modules\ParcelManagement\Transformers\ParcelCategoryResource;

class ParcelCategoryController extends Controller
{
    protected $parcelCategoryService;

    public function __construct(ParcelCategoryServiceInterface $parcelCategoryService)
    {
        $this->parcelCategoryService = $parcelCategoryService;
    }

    public function categoryFareList(Request $request): JsonResponse
    {
        if (empty($request->header('zoneId'))) {

            return response()->json(responseFormatter(ZONE_404), 200);
        }
        $zoneId = $request->header('zoneId');

        $criteria = [
            'is_active' => 1,
        ];
        $whereHasRelation = [
            'weightFares' => [
                'zone_id' => $zoneId,
            ]
        ];
        $relations = ['weightFares', 'weightFares.parcelFare', 'weightFares.parcelWeight'];
        $categories = $this->parcelCategoryService->getBy(criteria: $criteria, whereHasRelations: $whereHasRelation, relations: $relations, orderBy: ['created_at' => 'desc'], limit: $request['limit'], offset: $request['offset']);
        $category_fare = ParcelCategoryResource::collection($categories);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $category_fare, limit: $request['limit'], offset: $request['offset']));
    }
}
