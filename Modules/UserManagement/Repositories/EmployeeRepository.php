<?php

namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use Modules\UserManagement\Interfaces\EmployeeInterface;
use Modules\UserManagement\Interfaces\EmployeeRoleInterface;

class EmployeeRepository implements EmployeeInterface
{
    public function __construct(
        private User                  $employee,
    )
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes= [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $value = $attributes['value'] ?? 'all';
        $column = $attributes['query'] ?? '';
        $search = $attributes['search'] ?? '';
        $queryParams = ['value' => $value, 'search' => $search, 'column' => $column];

        $query = $this->employee
            ->query()
            ->when($value != 'all', function ($query) use ($column, $value) {
                $query->where($column, $value);
            })
            ->when(!empty($relations), function ($query) use ($relations) {
                $query->with($relations);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $keys = explode(' ', $search);
                    foreach ($keys as $key) {
                        $query->where('first_name', 'like', '%' . $key . '%')
                            ->orWhere('last_name', 'like', '%' . $key . '%')
                            ->orWhere('phone', 'like', '%' . $key . '%')
                            ->orWhere('email', 'like', '%' . $key . '%');
                    }
                });
            })
            ->userType('admin-employee')
            ->latest();

        if ($dynamic_page) {
            return $query->paginate($limit, ['*'], $offset);
        }

        return $query->paginate(paginationLimit())
            ->appends($queryParams);

    }

    public function getBy(string $column, string|int $value, array $attributes = []): mixed
    {
        return $this->employee
            ->query()
            ->when(($attributes['relations'] ?? null), fn($query) => $query->with($attributes['relations']))
            ->where($column, $value)
            ->first();
    }

    public function store(array $attributes): Model
    {
        $identityImages = [];
        foreach ($attributes['identity_images'] as $image) {
            $identityImages[] = fileUploader('employee/identity/', 'png', $image);
        }

        $employee = $this->employee;
        $employee->first_name = $attributes['first_name'];
        $employee->last_name = $attributes['last_name'];
        $employee->email = $attributes['email'];
        $employee->phone = $attributes['phone'];
        $employee->profile_image = fileUploader('employee/profile/', 'png', $attributes['profile_image']);
        $employee->identification_number = $attributes['identification_number'];
        $employee->identification_type = $attributes['identification_type'];
        $employee->identification_image = $identityImages;
        $employee->password = bcrypt($attributes['password']);
        $employee->user_type = 'admin-employee';
        $employee->is_active = 1;
        $employee->role_id = $attributes['role_id'];
        $employee->save();

        $this->storeModulePermission($attributes['permission'], $employee, $attributes['role_id']);

        return $employee;
    }

    public function update(array $attributes, string $id): Model
    {
        $employee = $this->getBy(column: 'id', value: $id);
        if (!is_null($attributes['status']?? null)) {

            $employee->is_active = $attributes['status'];
            $employee->save();

            return $employee;
        }
        if (array_key_exists('identity_images', $attributes)) {
            $identityImages = [];
            foreach ($attributes['identity_images'] as $image) {
                $identityImages[] = fileUploader('employee/identity/', 'png', $image, $employee->identity_images);
            }
            if (!is_null($employee->identity_images)) {
                foreach ($employee->identity_images as $id_image) {
                    fileRemover('employee/identity/', $id_image);
                }
            }
        } else {
            $identityImages = $employee->identification_image;
        }

        if (array_key_exists('other_documents', $attributes)) {
            $otherDocuments = [];
            $extension = '';
            foreach ($attributes['other_documents'] as $doc) {
                $extension = $doc->getClientOriginalExtension();
                $otherDocuments[] = fileUploader('employee/document/', $extension, $doc, $employee->other_documents);
            }
            if (!is_null($employee->other_documents)) {
                foreach ($employee->other_documents as $doc) {
                    fileRemover('employee/document/', $doc);
                }
            }
        } else {
            $otherDocuments = $employee->other_documents;
        }

        $employee->first_name = $attributes['first_name'];
        $employee->last_name = $attributes['last_name'];
        $employee->email = $attributes['email'];
        $employee->phone = $attributes['phone'];
        if (array_key_exists('profile_image', $attributes)) {
            $employee->profile_image = fileUploader('employee/profile/', 'png', $attributes['profile_image'], $employee->profile_image);
        }
        if (array_key_exists('identification_number', $attributes)) {
            $employee->identification_number = $attributes['identification_number'];
            $employee->identification_type = $attributes['identification_type'];
        }
        $employee->other_documents = $otherDocuments;
        $employee->identification_image = $identityImages;
        $employee->role_id = $attributes['role_id'] ?? $employee->role_id;
        if (!is_null($attributes['password'])) {
            $employee->password = bcrypt($attributes['password']);
        }
        $employee->is_active = 1;
        if ($attributes['permission'] ?? null) {
            $employee->moduleAccess()->delete();
            $this->storeModulePermission($attributes['permission'], $employee, $attributes['role_id']);
        }

        $employee->save();

        return $employee;
    }

    public function destroy(string $id): Model
    {
        $employee = $this->getBy(column: 'id', value: $id);
        $employee->delete();
        return $employee;
    }
    public function trashed(array $attributes)
    {
        $search = $attributes['search'] ?? null;
        $relations = $attributes['relations'] ?? null;
        return $this->employee->query()
        ->when($relations, function ($query) use ($relations){
            $query->with($relations);
        })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $keys = explode(' ', $search);
                    foreach ($keys as $key) {
                        $query->where('first_name', 'like', '%'. $key. '%')
                            ->orWhere('last_name', 'like', '%'. $key. '%')
                            ->orWhere('phone', 'like', '%'. $key. '%')
                            ->orWhere('email', 'like', '%'. $key. '%');
                    }
                });
            })
            ->userType('admin-employee')
            ->onlyTrashed()
            ->paginate(paginationLimit());

    }

    public function restore(string $id)
    {
        return $this->employee->query()->userType('admin-employee')->onlyTrashed()->find($id)->restore();
    }

    public function permanentDelete(string $id): Model
    {
        $model = $this->employee->query()->userType('admin-employee')->onlyTrashed()->find($id);
        $model->forceDelete();
        return $model;
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

}
