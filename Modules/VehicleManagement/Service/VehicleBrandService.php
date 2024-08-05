<?php

namespace Modules\VehicleManagement\Service;

use App\Service\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\VehicleManagement\Repository\VehicleBrandRepositoryInterface;
use Modules\VehicleManagement\Service\Interface\VehicleBrandServiceInterface;

class VehicleBrandService extends BaseService implements VehicleBrandServiceInterface
{
    protected $vehicleBrandRepository;

    public function __construct(VehicleBrandRepositoryInterface $vehicleBrandRepository)
    {
        parent::__construct($vehicleBrandRepository);
        $this->vehicleBrandRepository = $vehicleBrandRepository;
    }

    public function create(array $data): ?Model
    {
        $storeData = [
            'name' => $data['brand_name'],
            'description' => $data['short_desc'],
            'image' => fileUploader('vehicle/brand/', 'png', $data['brand_logo']),
        ];
        return $this->vehicleBrandRepository->create($storeData);
    }

    public function update(int|string $id, array $data = []): ?Model
    {
        $model = $this->findOne(id: $id);
        $updateData = [
            'name' => $data['brand_name'],
            'description' => $data['short_desc'],
        ];
        if (array_key_exists('brand_logo', $data)) {
            $updateData = array_merge($updateData, [
                'image' => fileUploader('vehicle/brand/', 'png', $data['brand_logo'], $model?->image)
            ]);
        }
        return $this->vehicleBrandRepository->update($id, $updateData);
    }

    public function export(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): Collection|LengthAwarePaginator|\Illuminate\Support\Collection
    {
        return $this->index(criteria: $criteria, relations: $relations, orderBy: $orderBy)->map(function ($item) {
            return [
                'Id' => $item['id'],
                'Brand Name' => $item['name'],
                'Description' => $item['description'],
                'Total Vehicles' => $item->vehicles->count(),
                'Status' => $item['is_active'] ? 'Active' : 'Inactive',
            ];
        });
    }

}
