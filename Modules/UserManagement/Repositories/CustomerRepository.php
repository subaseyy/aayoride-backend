<?php

namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Interfaces\CustomerInterface;
use phpseclib3\Exception\BadConfigurationException;
use function Symfony\Component\Translation\t;

class CustomerRepository implements CustomerInterface
{
    public function __construct(
        private User $customer
    )
    {
    }


    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $value = array_key_exists('value', $attributes) ? $attributes['value'] : 'all';
        $column = array_key_exists('query', $attributes) ? $attributes['query'] : '';
        $search = array_key_exists('search', $attributes) ? $attributes['search'] : '';
        $queryParams = ['value' => $value, 'search' => $search, 'column' => $column];

        $query = $this->customer
            ->query()
            ->when(array_key_exists('relations', $attributes), function ($query) use ($attributes) {
                $query->with($attributes['relations']);
            })
            ->when(!empty($relations[0]), function ($query) use ($relations) {
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
            ->when($value != 'all', function ($query) use ($column, $value) {
                $query->where($column, $value);
            })
            ->userType(CUSTOMER)
            ->latest();

        if ($dynamic_page) {
            return $query->paginate(perPage: $limit, page: $offset);
        }

        return $query->paginate($limit)
            ->appends($queryParams);

    }

    public function getBy(string $column, string|int $value, array $attributes = []): Model
    {
        return $this->customer
            ->query()
            ->when(array_key_exists('relations', $attributes), function ($query) use ($attributes) {
                $query->with($attributes['relations']);
            })
            ->where($column, $value)
            ->when(array_key_exists('withCount', $attributes), fn($query) => $query->withCount($attributes['withCount'])
            )
            ->when(array_key_exists('withAvgRelation', $attributes), fn($query) => $query->withAvg($attributes['withAvgRelation'], $attributes['withAvgColumn'])
            )
            ->userType(CUSTOMER)
            ->first();
    }

    public function store(array $attributes): Model
    {
        $identityImages = [];
        if (array_key_exists('identity_images', $attributes)) {
            foreach ($attributes['identity_images'] as $image) {
                $identityImages[] = fileUploader('customer/identity/', 'png', $image);
            }
        }

        if (array_key_exists('other_documents', $attributes)) {
            $otherDocuments = [];
            foreach ($attributes['other_documents'] as $document) {
                $otherDocuments[] = fileUploader('customer/document/', $document->getClientOriginalExtension(), $document);
            }
        }
        DB::beginTransaction();

        $customer = $this->customer;
        $customer->user_level_id = $attributes['user_level_id'] ?? null;
        $customer->first_name = $attributes['first_name'] ?? null;
        $customer->last_name = $attributes['last_name'] ?? null;
        $customer->email = $attributes['email'] ?? null;
        $customer->phone = $attributes['phone'];
        $customer->profile_image = array_key_exists('profile_image', $attributes) ? fileUploader('customer/profile/', 'png', $attributes['profile_image']) : null;
        $customer->identification_number = $attributes['identification_number'] ?? null;
        $customer->identification_type = $attributes['identification_type'] ?? null;
        $customer->other_documents = $otherDocuments ?? null;
        $customer->identification_image = $identityImages ?? null;
        $customer->password = array_key_exists('password', $attributes) ? bcrypt($attributes['password']) : null;
        $customer->fcm_token = $attributes['fcm_token'] ?? null;
        $customer->user_type = CUSTOMER;
        $customer->is_active = 1;
        $customer->save();

        $customer->levelHistory()->create([
            'user_level_id' => $attributes['user_level_id'],
            'user_type' => CUSTOMER
        ]);

        $customer->userAccount()->create();

        DB::commit();

        return $customer;
    }

    public function update(array $attributes, string $id): Model
    {
        $customer = $this->getBy(column: 'id', value: $id);


        if (!array_key_exists('status', $attributes)) {
            $identityImages = [];
            if (array_key_exists('identity_images', $attributes)) {
                foreach ($attributes['identity_images'] as $image) {
                    $identityImages[] = fileUploader('customer/identity/', 'png', $image);
                }
            }else{
                $identityImages =  $customer->identification_image;
            }
            $otherDocuments = [];
            if (array_key_exists('other_documents', $attributes)) {
                foreach ($attributes['other_documents'] as $image) {
                    $otherDocuments[] = fileUploader('customer/document/', $image->getClientOriginalExtension(), $image);
                }
            }

            if ($customer->other_documents != null && count($customer->other_documents) > 0) {
                $otherDocuments = array_merge($otherDocuments, $customer->other_documents);
            }
            array_key_exists('user_level_id', $attributes) ? $customer->user_level_id = $attributes['user_level_id'] : null;
            array_key_exists('first_name', $attributes) ? $customer->first_name = $attributes['first_name'] : null;
            array_key_exists('last_name', $attributes) ? $customer->last_name = $attributes['last_name'] : null;
            array_key_exists('email', $attributes) ? $customer->email = $attributes['email'] : null;
            array_key_exists('phone', $attributes) ? $customer->phone = $attributes['phone'] : null;
            if (array_key_exists('profile_image', $attributes)) {
                $customer->profile_image = fileUploader('customer/profile/', 'png', $attributes['profile_image'], $customer->profile_image);
            }
            array_key_exists('identification_number', $attributes) ? $customer->identification_number = $attributes['identification_number'] : null;
            array_key_exists('identification_type', $attributes) ? $customer->identification_type = $attributes['identification_type'] : null;
            $customer->other_documents = $otherDocuments;
            $customer->identification_image = $identityImages;
            if (array_key_exists('password', $attributes)) {
                $customer->password = bcrypt($attributes['password']);
            }
            $customer->is_active = 1;
            if (array_key_exists('decrease', $attributes)) {
                $customer->loyalty_points -= $attributes['decrease'];
            }
            if (array_key_exists('increase', $attributes)) {
                $customer->loyalty_points += $attributes['increase'];
            }
            $customer->save();

            // Customer Address
            if (array_key_exists('address', $attributes)) {
                $address = $customer->addresses()->where(['user_id' => $customer->id, 'address_label' => 'default'])->first();
                if (is_null($address)) {
                    $customer->addresses()->create([
                        'address' => $attributes['address'],
                        'address_label' => 'default'
                    ]);
                } else {
                    $address->address = $attributes['address'];
                    $address->save();
                }
            }
        } else {
            $customer->update(['is_active' => $attributes['status']]);
        }

        return $customer;
    }

    public function destroy(string $id): Model
    {
        $customer = $this->getBy(column: 'id', value: $id);
        $customer->delete();
        return $customer;
    }

    public function trashed(array $attributes)
    {
        $search = $attributes['search'] ?? null;
        $relations = $attributes['relations'] ?? null;
        return $this->customer->query()
            ->when($relations, function ($query) use ($relations) {
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
            ->userType(CUSTOMER)
            ->onlyTrashed()
            ->paginate(paginationLimit());

    }

    public function restore(string $id)
    {
        return $this->customer->query()->userType(CUSTOMER)->onlyTrashed()->find($id)->restore();
    }

    public function permanentDelete(string $id): Model
    {
        $model = $this->customer->query()->userType(CUSTOMER)->onlyTrashed()->find($id);
        $model->forceDelete();
        return $model;
    }

    public function overviewCount()
    {
        return [
            'registered' => $this->customer->where(['user_type' => 'customer'])->count(),
            'inactive' => $this->customer->where(['user_type' => 'customer', 'is_active' => false])->count(),
            'active' => $this->customer->where(['user_type' => 'customer', 'is_active' => true])->count(),
            'verified' => $this->customer->where(['user_type' => 'customer'])->whereNotNull(['first_name', 'last_name'])->count(),
            'new' => $this->customer->where(['user_type' => 'customer'])->whereBetween('created_at', [now()->subDays(7), now()->addDays(7)])->count()
        ];
    }


}
