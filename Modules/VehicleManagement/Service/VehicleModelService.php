<?php

namespace Modules\VehicleManagement\Service;

use App\Service\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\VehicleManagement\Repository\VehicleModelRepositoryInterface;
use Modules\VehicleManagement\Service\Interface\VehicleModelServiceInterface;

class VehicleModelService extends BaseService implements VehicleModelServiceInterface
{
    protected $vehicleModelRepository;
    public function __construct(VehicleModelRepositoryInterface $vehicleModelRepository)
    {
        parent::__construct($vehicleModelRepository);
        $this->vehicleModelRepository = $vehicleModelRepository;
    }

    public function create(array $data): ?Model
    {
        $storeData = [
            'name' => $data['name'],
            'brand_id' => $data['brand_id'],
            'seat_capacity' => $data['seat_capacity'],
            'maximum_weight' => $data['maximum_weight'],
            'hatch_bag_capacity' => $data['hatch_bag_capacity'],
            'engine' => $data['engine'],
            'description' => $data['short_desc'],
            'image' => fileUploader('vehicle/model/', 'png', $data['model_image']),
        ];
        return $this->vehicleModelRepository->create($storeData);
    }

    public function update(int|string $id, array $data = []): ?Model
    {
        $model = $this->findOne(id: $id);
        $updateData = [
            'name' => $data['name'],
            'brand_id' => $data['brand_id'],
            'seat_capacity' => $data['seat_capacity'],
            'maximum_weight' => $data['maximum_weight'],
            'hatch_bag_capacity' => $data['hatch_bag_capacity'],
            'engine' => $data['engine'],
            'description' => $data['short_desc'],
        ];

        if (array_key_exists('model_image', $data)) {
            $updateData = array_merge($updateData, [
                'image' => fileUploader('vehicle/model/', 'png', $data['model_image'], $model?->image)
            ]);
        }
        return $this->vehicleModelRepository->update($id, $updateData);
    }

    public function export(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): Collection|LengthAwarePaginator|\Illuminate\Support\Collection
    {
        return $this->index(criteria: $criteria, relations: $relations, orderBy: $orderBy)->map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['name'],
                'description' => $item['description'],
                'brand_id' => $item['brand_id'],
                'seat_capacity' => $item['seat_capacity'],
                'maximum_weight' => $item['maximum_weight'],
                "hatch_bag_capacity" => $item['hatch_bag_capacity'],
                "engine" => $item['engine'],
                "total_vehicles" => $item->vehicles->count(),
                "is_active" => $item['is_active'],
                "created_at" => $item['created_at'],
            ];
        });
    }

}
