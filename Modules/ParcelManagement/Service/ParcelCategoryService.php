<?php

namespace Modules\ParcelManagement\Service;

use App\Service\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\ParcelManagement\Repository\ParcelCategoryRepositoryInterface;
use Modules\ParcelManagement\Service\Interface\ParcelCategoryServiceInterface;

class ParcelCategoryService extends BaseService implements ParcelCategoryServiceInterface
{
    protected $parcelCategoryRepository;
    public function __construct(ParcelCategoryRepositoryInterface $parcelCategoryRepository)
    {
        parent::__construct($parcelCategoryRepository);
        $this->parcelCategoryRepository = $parcelCategoryRepository;
    }
    public function create(array $data): ?Model
    {
        $storeData = [
            'name'=>$data['category_name'],
            'description'=>$data['short_desc'],
            'image'=>fileUploader('parcel/category/', 'png', $data['category_icon']),
        ];
        return $this->parcelCategoryRepository->create(data: $storeData);
    }

    public function update(string|int $id, array $data = []): ?Model
    {
        $model = $this->findOne(id: $id);
        $updateData = [
            'name'=>$data['category_name'],
            'description'=>$data['short_desc'],
        ];
        if (array_key_exists('brand_logo', $data)) {
            $updateData = array_merge($updateData,[
                'image' => fileUploader('parcel/category/', 'png', $data['category_icon'], $model?->image)
            ]);
        }
        return $this->parcelCategoryRepository->update($id, $updateData);
    }

    public function export(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false): Collection|LengthAwarePaginator|\Illuminate\Support\Collection
    {
        return $this->index(criteria: $criteria, orderBy: $orderBy)->map(function ($item) {
            return [
                'Id' => $item['id'],
                'Parcel Category Name' => $item['name'],
                'Total Delivered' => $item['parcels']->count(),
                'Status' => $item['is_active'] ? 'Active' : 'Inactive',
            ];
        });
    }

}
