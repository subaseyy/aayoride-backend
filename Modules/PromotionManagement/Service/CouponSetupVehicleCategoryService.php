<?php

namespace Modules\PromotionManagement\Service;

use App\Repository\EloquentRepositoryInterface;
use App\Service\BaseService;
use Illuminate\Database\Eloquent\Model;
use Modules\PromotionManagement\Repository\CouponSetupVehicleCategoryRepositoryInterface;
use Modules\PromotionManagement\Service\Interface\CouponSetupVehicleCategoryServiceInterface;

class CouponSetupVehicleCategoryService extends BaseService implements Interface\CouponSetupVehicleCategoryServiceInterface
{
    public function __construct(CouponSetupVehicleCategoryRepositoryInterface $baseRepository)
    {
        parent::__construct($baseRepository);
    }

    public function create(array $data): ?Model
    {
        $storeData = [
            'name'=>$data['banner_title'],
            'description'=>$data['short_desc'],
            'display_position'=>$data['display_position'] ?? null,
            'time_period'=>$data['time_period'],
            'redirect_link'=>$data['redirect_link'],
            'banner_group'=>$data['banner_group'] ?? null,
            'start_date'=>$data['start_date'] ?? null,
            'end_date'=>$data['end_date'] ?? null,
            'image'=>fileUploader('promotion/banner/', 'png', $data['banner_image']),
        ];
        return parent::create($storeData);
    }

    public function update(int|string $id, array $data = []): ?Model
    {
        $model = $this->findOne(id: $id);
        $updateData = [
            'name'=>$data['banner_title'],
            'description'=>$data['short_desc'],
            'display_position'=>$data['display_position'] ?? null,
            'time_period'=>$data['time_period'],
            'redirect_link'=>$data['redirect_link'],
            'banner_group'=>$data['banner_group'] ?? null,
            'start_date'=>$data['start_date'] ?? null,
            'end_date'=>$data['end_date'] ?? null,
        ];
        if (array_key_exists('brand_logo', $data)) {
            $updateData = array_merge($updateData,[
                'image'=>fileUploader('promotion/banner/', 'png', $data['banner_image'], $model->image),
            ]);
        }
        return parent::update($id, $updateData);
    }
}
