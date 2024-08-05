<?php

namespace Modules\UserManagement\Service;

use App\Service\BaseService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\UserManagement\Repository\UserAddressRepositoryInterface;
use Modules\UserManagement\Repository\UserRepositoryInterface;

class EmployeeService extends BaseService implements Interface\EmployeeServiceInterface
{
    protected $userRepository;
    protected $userAddressRepository;

    public function __construct(UserRepositoryInterface $userRepository, UserAddressRepositoryInterface $userAddressRepository)
    {
        parent::__construct($userRepository);
        $this->userRepository = $userRepository;
        $this->userAddressRepository = $userAddressRepository;
    }

    public function create(array $data): ?Model
    {
        DB::beginTransaction();
        $employeeData = $data;
        $identityImages = [];
        if (array_key_exists('identity_images', $data)){
            foreach ($data['identity_images'] as $image) {
                $identityImages[] = fileUploader('employee/identity/', 'png', $image);
        }
}
        $employeeData = array_merge($employeeData, [
            'identification_image' => $identityImages,
            'is_active' => 1,
            'user_type' => 'admin-employee',
            'password' => bcrypt($data['password']),
        ]);
        if (array_key_exists('profile_image', $data)) {
            $profileImage = fileUploader('employee/profile/', 'png', $data['profile_image']);
            $employeeData = array_merge($employeeData, [
                'profile_image' => $profileImage
            ]);
        }

        $employee = $this->userRepository->create(data: $employeeData);
        $address = [
            'user_id' => $employee?->id,
            'address' => $data['address']
        ];
        $this->userAddressRepository->create(data: $address);

        $this->storeModulePermission($data['permission'], $employee, $data['role_id']);
        DB::commit();
        return $employee;
    }

    public function update(int|string $id, array $data = []): ?Model
    {
        DB::beginTransaction();
        $employee = $this->userRepository->findOneBy(criteria: ['id' => $id, 'user_type' => 'admin-employee']);
        $employeeData = $data;

        if (array_key_exists('identity_images', $data)) {
            $identityImages = [];
            foreach ($data['identity_images'] as $image) {
                $identityImages[] = fileUploader('employee/identity/', 'png', $image, $employee?->identification_image);
            }
            if (!is_null($employee?->identification_image)) {
                foreach ($employee?->identification_image as $id_image) {
                    fileRemover('employee/identity/', $id_image);
                }
            }
        } else {
            $identityImages = $employee?->identification_image;
        }


        if (array_key_exists('other_documents', $data)) {
            $otherDocuments = [];
            $extension = '';
            foreach ($data['other_documents'] as $doc) {
                $extension = $doc->getClientOriginalExtension();
                $otherDocuments[] = fileUploader('employee/document/', $extension, $doc, $employee?->other_documents);
            }
            if (!is_null($employee?->other_documents)) {
                foreach ($employee?->other_documents as $doc) {
                    fileRemover('employee/document/', $doc);
                }
            }
        } else {
            $otherDocuments = $employee?->other_documents;
        }
        if (array_key_exists('profile_image', $data)) {
            $profileImage = fileUploader('employee/profile/', 'png', $data['profile_image'], $employee?->profile_image);
            $employeeData = array_merge($employeeData, [
                'profile_image' => $profileImage
            ]);
        }
        if (array_key_exists('password', $data) && $data['password'] != null) {
            $password = bcrypt($data['password']);
            $employeeData = array_merge($employeeData, [
                'password' => $password
            ]);
        }else{
            unset($employeeData['password']);
        }
        $employeeData = array_merge($employeeData, [
            'identification_image' => $identityImages,
            'other_documents' => $otherDocuments,
            'role_id' => $data['role_id'] ?? $employee?->role_id,
            'is_active' => $employee?->is_active ?? 1,
        ]);
        if ($data['permission'] ?? null) {
            $employee?->moduleAccess()->delete();
            $this->storeModulePermission($data['permission'], $employee, $data['role_id']);
        }
        $employee = $this->userRepository->update(id: $id, data: $employeeData);
        if (array_key_exists('address', $data)) {
            $address = $this->userAddressRepository->findOneBy(criteria: ['user_id' => $id]);
            $addressData = [
                'user_id' => $id,
                'address' => $data['address']
            ];
            $this->userAddressRepository->update(id: $address?->id, data: $addressData);
        }

        DB::commit();
        return $employee;

    }

    private function storeModulePermission($permissions, $employee, $role_id)
    {
        foreach ($permissions as $key => $permission) {
            $employee->moduleAccess()->create([
                'role_id' => $role_id,
                'module_name' => $key,
                'view' => in_array('view', $permission),
                'add' => in_array('add', $permission),
                'update' => in_array('update', $permission),
                'delete' => in_array('delete', $permission),
                'log' => in_array('log', $permission),
                'export' => in_array('export', $permission) ? 1 : 0,
            ]);
        }
        return true;
    }

    public function index(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): Collection|LengthAwarePaginator
    {
        $data = [];
        $data['user_type'] = 'admin-employee';
        if (array_key_exists('status', $criteria) && $criteria['status'] !== 'all') {
            $data['is_active'] = $criteria['status'] == 'active' ? 1 : 0;
        }
        $searchData = [];
        if (array_key_exists('search', $criteria) && $criteria['search'] != '') {
            $searchData['fields'] = ['full_name', 'first_name', 'last_name', 'phone', 'email'];
            $searchData['value'] = $criteria['search'];
        }
        return $this->baseRepository->getBy(criteria: $data, searchCriteria: $searchData, relations: $relations, orderBy: $orderBy, limit: $limit, offset: $offset, withCountQuery: $withCountQuery);
    }

    public function export(array $criteria = [], array $relations = [], array $orderBy = [], int $limit = null, int $offset = null, array $withCountQuery = []): Collection|LengthAwarePaginator|\Illuminate\Support\Collection
    {
        return $this->index(criteria: $criteria, relations: $relations, orderBy: $orderBy)->map(function ($item) {
            return [
                'Name' => $item['first_name'] . ' ' . $item['last_name'],
                'Email' => $item['email'],
                'Phone' => $item['phone'],
                'Identification Number' => $item['identification_number'],
                'Identification Type' => $item['identification_type'],
                'User Type' => $item['user_type'],
                'Role' => $item?->role?->name,
                'Modules' => json_encode($item?->role?->modules),
                'Status' => $item['is_active'] ? 'Active' : 'Inactive'
            ];
        });
    }

}
