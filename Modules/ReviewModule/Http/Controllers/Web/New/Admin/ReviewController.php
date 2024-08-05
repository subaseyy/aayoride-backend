<?php

namespace Modules\ReviewModule\Http\Controllers\Web\New\Admin;

use App\Http\Controllers\BaseController;
use App\Service\BaseServiceInterface;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\ReviewModule\Service\Interface\ReviewServiceInterface;

class ReviewController extends BaseController
{
    protected $reviewService;

    public function __construct(ReviewServiceInterface $reviewService)
    {
        parent::__construct($reviewService);
        $this->reviewService = $reviewService;
    }

    public function driverReviewExport($id, $reviewed, Request $request)
    {
        $exportData = $this->reviewService->export($id, $reviewed, $request, "driver");
        return exportData($exportData, $request['file'], 'usermanagement::admin.driver.transaction.print');
    }

    public function customerReviewExport($id, $reviewed, Request $request)
    {
        $exportData = $this->reviewService->export($id, $reviewed, $request, "customer");
        return exportData($exportData, $request['file'], 'usermanagement::admin.driver.transaction.print');
    }

}
