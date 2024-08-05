<?php

namespace Modules\AdminModule\Http\Controllers\Web\New\Admin;

use App\Http\Controllers\BaseController;
use Modules\AdminModule\Service\Interface\ActivityLogServiceInterface;

class ActivityLogController extends BaseController
{
    protected $activityLogService;

    public function __construct(ActivityLogServiceInterface $activityLogService)
    {
        parent::__construct($activityLogService);
        $this->activityLogService = $activityLogService;
    }

    public function log($request)
    {
        return $this->activityLogService->log(data: $request->all());
    }
}
