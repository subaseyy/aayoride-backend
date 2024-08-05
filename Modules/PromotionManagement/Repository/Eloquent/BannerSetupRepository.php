<?php

namespace Modules\PromotionManagement\Repository\Eloquent;

use App\Repository\Eloquent\BaseRepository;
use Modules\PromotionManagement\Entities\BannerSetup;
use Modules\PromotionManagement\Repository\BannerSetupRepositoryInterface;

class BannerSetupRepository extends BaseRepository implements BannerSetupRepositoryInterface
{
    public function __construct(BannerSetup $model)
    {
        parent::__construct($model);
    }
    public function list($data, $limit, $offset)
    {
        return $this->model->where('is_active', 1)
            ->where(function ($query) use ($data) {
                $query->where('time_period', '!=', 'period') // Exclude rows where time_period is not "period"
                    ->orWhere(function ($periodQuery) use ($data) {
                        $periodQuery->whereNull('start_date')
                            ->orWhere(function ($dateQuery) use ($data) {
                                $dateQuery->where('start_date', '<=', $data)
                                    ->where('end_date', '>=', $data);
                            });
                    });
            })
            ->paginate($limit, ['*'], 'page', $offset ?? 1);
    }
}
