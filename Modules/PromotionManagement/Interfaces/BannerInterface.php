<?php

namespace Modules\PromotionManagement\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface BannerInterface extends BaseRepositoryInterface
{

    public function trashed(array $attributes);

    public function restore(string $id);
    public function permanentDelete(string $id);
}
