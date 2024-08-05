<?php

namespace Modules\PromotionManagement\Http\Controllers\Api\New\Customer;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\PromotionManagement\Service\Interface\BannerSetupServiceInterface;
use Modules\PromotionManagement\Transformers\BannerResource;

class BannerSetupController extends Controller
{
    protected $bannerService;
    public function __construct(BannerSetupServiceInterface $bannerService)
    {
        $this->bannerService = $bannerService;
    }
    public function list(Request $request):JsonResponse
    {

        $today = Carbon::today();
        $banner = $this->bannerService->list($today,limit:$request['limit'],offset:$request['offset']);
        $data = BannerResource::collection($banner);
        return response()->json(responseFormatter(DEFAULT_200, $data,$request['limit'], $request['offset']));

    }

     public function RedirectionCount(Request $request)
     {
         $banner = $this->bannerService->findOne($request->banner_id);

         if(!is_null($banner)){
             $banner->total_redirection = $banner->total_redirection + 1;
             $banner->save();
             return response()->json(responseFormatter(DEFAULT_STORE_200, $banner->total_redirection));
         }

         return response()->json(responseFormatter(DEFAULT_404));
     }
}
