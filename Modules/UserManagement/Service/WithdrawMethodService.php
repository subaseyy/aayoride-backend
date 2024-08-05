<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\UserManagement\Repository\WithdrawMethodRepositoryInterface;
use Modules\UserManagement\Service\Interface\WithdrawMethodServiceInterface;

class WithdrawMethodService extends BaseService implements WithdrawMethodServiceInterface
{
    protected $withdrawMethodRepository;

    public function __construct(WithdrawMethodRepositoryInterface $withdrawMethodRepository)
    {
        parent::__construct($withdrawMethodRepository);
        $this->withdrawMethodRepository = $withdrawMethodRepository;
    }

    public function create(array $data): ?Model
    {
        $method_fields = [];
        foreach ($data['field_name'] as $key => $field_name) {
            $method_fields[] = [
                'input_type' => $data['field_type'][$key],
                'input_name' => strtolower(str_replace(' ', "_", $data['field_name'][$key])),
                'placeholder' => $data['placeholder_text'][$key],
                'is_required' => isset($data['is_required']) && isset($data['is_required'][$key]),
            ];
        }
        $default_method = $this->withdrawMethodRepository->findOneBy(criteria: ['is_default' => true]);
        $attributes = [
            'method_name' => $data['method_name'],
            'method_fields' => $method_fields,
            'is_default' => array_key_exists('is_default',$data) && $data['is_default'] == '1' ? 1 : 0,
        ];
        if (array_key_exists('is_default',$data) && $data['is_default'] == '1' && $default_method) {
            $this->withdrawMethodRepository->update(id: $default_method->id,data: ['is_default' => 0]);
        }
       return $this->withdrawMethodRepository->create(data: $attributes);
    }

    public function update(int|string $id, array $data = []): ?Model
    {
        $method_fields = [];
        foreach ($data['field_name'] as $key => $field_name) {
            $method_fields[] = [
                'input_type' => $data['field_type'][$key],
                'input_name' => strtolower(str_replace(' ', "_", $data['field_name'][$key])),
                'placeholder' => $data['placeholder_text'][$key],
                'is_required' => isset($data['is_required']) && isset($data['is_required'][$key]) ? 1:0,
            ];
        }
        $attributes = [
            'method_name' => $data['method_name'],
            'method_fields' => $method_fields,
            'is_default' => array_key_exists('is_default',$data) && $data['is_default'] == '1' ? 1 : 0,
        ];
        $withdrawalMethod = $this->withdrawMethodRepository->update(id: $id, data: $attributes);

        if (array_key_exists('is_default',$data) && $data['is_default'] == '1') {
            $this->withdrawMethodRepository->updatedBy(criteria: [['id', '!=', $withdrawalMethod?->id]],data: ['is_default' => 0]);
        }
        return $withdrawalMethod;
    }


    public function index(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): Collection|LengthAwarePaginator
    {
        $data = [];
        if (array_key_exists('status', $criteria) && $criteria['status'] !== 'all') {
            $data['is_active'] = $criteria['status'] == 'active' ? 1 : 0;
        }
        $searchData = [];
        if (array_key_exists('search', $criteria) && $criteria['search'] != '') {
            $searchData['fields'] = ['method_fields','method_name'];
            $searchData['value'] = $criteria['search'];
        }
        $whereInCriteria = [];
        $whereBetweenCriteria = [];
        $whereHasRelations = [];
        return $this->baseRepository->getBy(criteria: $data, searchCriteria: $searchData, whereInCriteria: $whereInCriteria, whereBetweenCriteria: $whereBetweenCriteria, whereHasRelations: $whereHasRelations, relations: $relations, orderBy: $orderBy, limit: $limit, offset: $offset, withCountQuery: $withCountQuery);
    }

}
