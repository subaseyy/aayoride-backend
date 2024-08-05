<?php

namespace Modules\ZoneManagement\Service;

use App\Service\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use Modules\ZoneManagement\Repository\ZoneRepositoryInterface;
use Modules\ZoneManagement\Service\Interface\ZoneServiceInterface;

class ZoneService extends BaseService implements ZoneServiceInterface
{
    protected $zoneRepository;
    public function __construct(ZoneRepositoryInterface $zoneRepository)
    {
        parent::__construct($zoneRepository);
        $this->zoneRepository = $zoneRepository;
    }

    protected function createPoint($coordinates)
    {

        foreach(explode('),(',trim($coordinates,'()')) as $index=>$single_array){
            if($index == 0)
            {
                $lastcord = explode(',',$single_array);
            }
            $coords = explode(',',$single_array);
            $polygon[] = new Point($coords[0], $coords[1]);
        }

        $polygon[] = new Point($lastcord[0], $lastcord[1]);
        return new Polygon([new LineString($polygon)]);
    }

    public function getZones(array $criteria = []): array
    {
        $data = [];
        if (array_key_exists('id', $criteria) && $criteria['id']) {
            $data['id'] =  $criteria['id'];
        }
        if (array_key_exists('status', $criteria) && $criteria['status']) {
            if ($criteria['status'] !== 'all') {
                $data['is_active'] =  $criteria['status'] == 'active' ? 1 : 0;
            }
        }
        $allZones = $this->zoneRepository->getBy(criteria: $data);
        $allZoneData = [];
        foreach ($allZones as $item) {
            $zoneCoordinate = json_decode($item->coordinates[0]->toJson(),true);
            $allZoneData[] = formatCoordinates($zoneCoordinate['coordinates']);
        }
        return $allZoneData;
    }

    public function create(array $data): ?Model
    {
        $coordinates = $this->createPoint($data['coordinates']);
        $data = [
            'name' => $data['name'],
            'coordinates' => $coordinates
        ];
        return $this->zoneRepository->create(data: $data);
    }

    public function update(string|int $id, array $data = []): ?Model
    {
        $coordinates = $this->createPoint($data['coordinates']);
        $data = [
            'name' => $data['name'],
            'coordinates' => $coordinates
        ];
        return $this->zoneRepository->update(id: $id, data: $data);
    }

    public function export(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, bool $onlyTrashed = false, bool $withTrashed = false): Collection|LengthAwarePaginator|\Illuminate\Support\Collection
    {
        $tripsCount = 0;
        $zoneData =$this->index(criteria: $criteria, relations: $relations, orderBy: $orderBy);
        foreach ($zoneData as $zone) {
            $tripsCount += count($zone['tripRequest']);
        }

        return $this->index(criteria: $criteria)->map(function ($item) use ($tripsCount) {
            $volumePercentage = ($item['tripRequest_count'] > 0) ? ($tripsCount/$item['tripRequest_count']) * 100 : 0;
            return [
                'Id' => $item['id'],
                'Name' => $item['name'],
                'Trip Request Volume' => $volumePercentage < 33.33 ? translate('low') : ($volumePercentage == 66.66 ? translate('medium') : translate('high')),
                "Active Status" => $item['is_active'] == 1 ? "Active" : "Inactive",
            ];
        });
    }


    public function getByPoints($point)
    {
        return $this->zoneRepository->getByPoints($point);
    }
}
