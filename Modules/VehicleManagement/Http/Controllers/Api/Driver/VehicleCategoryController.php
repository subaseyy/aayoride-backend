<?php

namespace Modules\VehicleManagement\Http\Controllers\Api\Driver;

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
     * @return JsonResponse
     */
    public function categoryList() :JsonResponse
    {
        $attributes = ['query' => 'is_active', 'value' => 'active'];
        $list = $this->category->get(
            limit: 999999,
            offset: 1,
            attributes: $attributes
        );
        $data = VehicleCategoryResource::collection($list);

        return response()->json(responseFormatter(constant: DEFAULT_200,content:$data, limit:99999, offset: 1), 200);
    }

}
