<?php

namespace Modules\VehicleManagement\Service;

use App\Service\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\VehicleManagement\Repository\VehicleCategoryRepositoryInterface;
use Modules\VehicleManagement\Service\Interface\VehicleCategoryServiceInterface;

class VehicleCategoryService extends BaseService implements VehicleCategoryServiceInterface
{
    protected $vehicleCategoryRepository;
    public function __construct(VehicleCategoryRepositoryInterface $vehicleCategoryRepository)
    {
        parent::__construct($vehicleCategoryRepository);
        $this->vehicleCategoryRepository = $vehicleCategoryRepository;
    }

    public function create(array $data): ?Model
    {
        $storeData = [
            'name' => $data['category_name'],
            'description' => $data['short_desc'],
            'type' => $data['type'],
            'image' => fileUploader('vehicle/category/', 'png', $data['category_image']),
        ];
        return $this->vehicleCategoryRepository->create($storeData);
    }

    public function update(int|string $id, array $data = []): ?Model
    {
        $model = $this->findOne(id: $id);
        $updateData = [
            'name' => $data['category_name'],
            'description' => $data['short_desc'],
            'type' => $data['type'],
        ];
        if (array_key_exists('category_image', $data)) {
            $updateData = array_merge($updateData, [
                'image' => fileUploader('vehicle/category/', 'png', $data['category_image'], $model?->image)
            ]);
        }
        return $this->vehicleCategoryRepository->update($id, $updateData);
    }

    public function export(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): Collection|LengthAwarePaginator|\Illuminate\Support\Collection
    {
        return $this->index(criteria: $criteria,relations: $relations, orderBy: $orderBy)->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'description' => $item['description'],
                'type' => $item['type'],
                "total_vehicles" => $item->vehicles->count(),
                "is_active" => $item['is_active'],
                "created_at" => $item['created_at'],
            ];
        });
    }

}
