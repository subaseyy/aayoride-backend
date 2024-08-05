<?php

namespace Modules\Gateways\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Gateways\Traits\Processor;
use Modules\Gateways\Entities\Setting;

class PaymentConfigController extends Controller
{
    use Processor;

    private Setting $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function payment_config_get(): JsonResponse
    {
        $dataValues = $this->setting->whereIn('settings_type', [PAYMENT_CONFIG])->get(['key_name','is_active','additional_data']);
        return response()->json($this->responseFormatter(DEFAULT_200, $dataValues), 200);
    }
}
