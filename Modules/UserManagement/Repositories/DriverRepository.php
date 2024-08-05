<?php

namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Modules\UserManagement\Entities\User;
use Modules\UserManagement\Interfaces\DriverInterface;

class DriverRepository implements DriverInterface
{
    public function __construct(
        private User $driver
    )
    {
    }


    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $value = array_key_exists('value', $attributes) ? $attributes['value'] : 'all';
        $column = array_key_exists('query', $attributes) ? $attributes['query'] : '';
        $search = array_key_exists('search', $attributes) ? $attributes['search'] : '';
        $queryParams = ['value' => $value, 'search' => $search, 'column' => $column];

        $query = $this->driver
            ->query()
            ->when(array_key_exists('relations', $attributes), function ($query) use ($attributes) {
                $query->with($attributes['relations']);
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
            ->userType(DRIVER)
            ->latest();

        if ($dynamic_page) {
            return $query->paginate($limit, ['*'], $limit);
        }

        return $query->paginate(paginationLimit())
            ->appends($queryParams);
    }

    public function getBy(string $column, string|int $value, array $attributes = []): mixed
    {
        return $this->driver
            ->query()
            ->when(array_key_exists('relations', $attributes), function ($query) use ($attributes) {
                $query->with($attributes['relations']);
            })
            ->where($column, $value)
            ->when($attributes['withAvg'] ?? null, fn($query) => $query->withAvg($attributes['withAvg'], $attributes['avgColumn']))
            ->first();
    }

    public function store(array $attributes): Model
    {
        DB::beginTransaction();

        $driver = $this->driver;
        $identityImages = [];
        if (array_key_exists('identity_images', $attributes)) {
            foreach ($attributes['identity_images'] as $image) {
                $identityImages[] = fileUploader('driver/identity/', 'png', $image);
            }
        }
        $otherDocuments = [];
        if (array_key_exists('other_documents', $attributes)) {
            foreach ($attributes['other_documents'] as $image) {
                $otherDocuments[] = fileUploader('driver/document/', $image->getClientOriginalExtension(), $image);
            }
        }
        $driver->user_level_id = $attributes['user_level_id'] ?? null;
        $driver->first_name = $attributes['first_name'];
        $driver->last_name = $attributes['last_name'];
        $driver->email = $attributes['email'] ?? null;
        $driver->phone = $attributes['phone'];
        $driver->profile_image = array_key_exists('profile_image', $attributes) ? fileUploader('driver/profile/', 'png', $attributes['profile_image']) : null;
        $driver->identification_number = $attributes['identification_number'] ?? null;
        $driver->identification_type = $attributes['identification_type'] ?? null;
        $driver->other_documents = $otherDocuments;
        $driver->identification_image = $identityImages;
        $driver->password = bcrypt($attributes['password']);
        $driver->user_type = 'driver';
        $driver->fcm_token = $attributes['fcm_token'] ?? null;
        $verification = businessConfig('driver_verification')?->value ?? 0;
        if (!$verification) {
            $driver->is_active = true;
        }
        $driver->save();

        $driver->levelHistory()->create([
            'user_level_id' => $attributes['user_level_id'],
            'user_type' => DRIVER
        ]);

        $driver->driverDetails()->create([
            'is_online' => false,
            'availabilityStatus' => 'unavailable'
        ]);
        $driver->userAccount()->create();

        DB::commit();

        return $driver;
    }

    public function update(array $attributes, string $id): Model
    {
        $driver = $this->getBy(column: 'id', value: $id);

        if (array_key_exists('status', $attributes)) {
            $driver->is_active = $attributes['status'];
            is_null($driver->phone_verified_at && $attributes['status'] == 1) ? $driver->phone_verified_at = now() : null;
            $driver->save();

            return $driver;
        }
        $identityImages = [];
        if (array_key_exists('identity_images', $attributes)) {
            foreach ($attributes['identity_images'] as $image) {
                $identityImages[] = fileUploader('driver/identity/', 'png', $image);
            }
            if ( $driver->identification_image !=null && count($driver->identification_image)>0 && $driver->old_identification_image ==null){
                $oldIdentityImages = $driver->identification_image;
            }
        }else{
            $identityImages = $driver->identification_image;
            $oldIdentityImages = $driver->old_identification_image;
        }

        $otherDocuments = [];
        if (array_key_exists('other_documents', $attributes)) {
            foreach ($attributes['other_documents'] as $image) {
                $otherDocuments[] = fileUploader('driver/document/', $image->getClientOriginalExtension(), $image);
            }
        }
        if ( $driver->other_documents !=null  && count($driver->other_documents)>0){
            $otherDocuments = array_merge($otherDocuments, $driver->other_documents);
        }

        array_key_exists('user_level_id', $attributes) ? $driver->user_level_id = $attributes['user_level_id'] : null;
        array_key_exists('first_name', $attributes) ? $driver->first_name = $attributes['first_name'] : null;
        array_key_exists('last_name', $attributes) ? $driver->last_name = $attributes['last_name'] : null;
        array_key_exists('email', $attributes) ? $driver->email = $attributes['email'] : null;
        array_key_exists('phone', $attributes) ? $driver->phone = $attributes['phone'] : null;
        if (array_key_exists('profile_image', $attributes)) {
            $driver->profile_image = fileUploader('driver/profile/', 'png', $attributes['profile_image'], $driver->profile_image);
        }
        array_key_exists('identification_number', $attributes) ? $driver->identification_number = $attributes['identification_number'] : null;
        array_key_exists('identification_type', $attributes) ? $driver->identification_type = $attributes['identification_type'] : null;
        $driver->other_documents = $otherDocuments;
        $driver->identification_image = $identityImages?? $driver->identification_image;
        $driver->old_identification_image = $oldIdentityImages ?? null;
        if ($attributes['password'] ?? null) {
            $driver->password = bcrypt($attributes['password']);
        }
        $driver->is_active = 1;
        $driver->save();
        return $driver;
    }

    public function destroy(string $id): Model
    {
        $driver = $this->getBy(column: 'id', value: $id);
        $driver->delete();
        return $driver;
    }


    public function getCount($countColumn, $filterColumn = null, $filterValue = null, $attributes = [])
    {
        return $this->driver
            ->when($countColumn, function ($query) use ($filterColumn, $filterValue) {
                $query->where($filterColumn, $filterValue);
            })
            ->when(!empty($attributes['dates']), function ($query) use ($attributes) {
                $query->whereBetween('created_at', [$attributes['dates']['start'], $attributes['dates']['end']]);
            })
            ->count($countColumn);
    }

    public function getStatisticsData($attributes)
    {
        $total = $this->driver::query()
            ->where('user_type', 'driver')
            ->when(!empty($attributes['dates']), function ($query) use ($attributes) {
                $query->whereBetween('created_at', [$attributes['dates']['start'], $attributes['dates']['end']]);
            })
            ->count();
        $active = $this->driver::query()
            ->where(['user_type' => 'driver', 'is_active' => true])
            ->when(!empty($attributes['dates']), function ($query) use ($attributes) {
                $query->whereBetween('created_at', [$attributes['dates']['start'], $attributes['dates']['end']]);
            })->count();
        $inactive = $this->driver::query()
            ->where(['user_type' => 'driver', 'is_active' => false])
            ->when(!empty($attributes['dates']), function ($query) use ($attributes) {
                $query->whereBetween('created_at', [$attributes['dates']['start'], $attributes['dates']['end']]);
            })->count();

        $car = $this->driver::query()
            ->with(['vehicle' => function ($query) {
                $query->whereHas('category', function ($q) {
                    $q->where('type', 'car');
                });
            }])
            ->whereHas('vehicle', function ($query) {
                $query->whereHas('category', function ($q) {
                    $q->where('type', 'car');
                });
            })
            ->when(!empty($attributes['dates']), function ($query) use ($attributes) {
                $query->whereBetween('created_at', [$attributes['dates']['start'], $attributes['dates']['end']]);
            })
            ->count();

        $motorBike = $this->driver::query()
            ->with(['vehicle' => function ($query) {
                $query->whereHas('category', function ($q) {
                    $q->where('type', 'car');
                });
            }])
            ->when(!empty($attributes['dates']), function ($query) use ($attributes) {
                $query->whereBetween('created_at', [$attributes['dates']['start'], $attributes['dates']['end']]);
            })
            ->whereHas('vehicle', function ($query) {
                $query->whereHas('category', function ($q) {
                    $q->where('type', 'motor_bike');
                });
            })
            ->count();

        return [$total, $active, $inactive, $car, $motorBike];

    }

    public function getDriverWithoutVehicle(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $value = array_key_exists('value', $attributes) ? $attributes['value'] : 'all';
        $column = array_key_exists('query', $attributes) ? $attributes['query'] : '';
        $search = array_key_exists('search', $attributes) ? $attributes['search'] : '';
        $queryParams = ['value' => $value, 'search' => $search, 'column' => $column];

        $query = $this->driver
            ->query()
            ->when(array_key_exists('relations', $attributes), fn($query) => $query->with($attributes['relations']))
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
            ->with(['vehicle' => fn($query) => $query->withTrashed()])
            ->whereDoesntHave('vehicle', fn($query) => $query->withTrashed())
            ->when($value != 'all', fn($query) => $query->where($column, $value))
            ->userType(DRIVER)
            ->latest();

        if ($dynamic_page) {
            return $query->paginate( $limit)->appends($queryParams);
        }

        return $query->paginate(paginationLimit())
            ->appends($queryParams);
    }

    public function updateFcm($fcm): mixed
    {
        return auth('api')->user()->update([
            'fcm_token' => $fcm
        ]);
    }

    public function trashed(array $attributes)
    {
        $search = $attributes['search'] ?? null;
        $relations = $attributes['relations'] ?? null;
        return $this->driver->query()
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
            ->userType(DRIVER)
            ->onlyTrashed()
            ->paginate(paginationLimit());

    }

    public function restore(string $id)
    {
        return $this->driver->query()->userType(DRIVER)->onlyTrashed()->find($id)->restore();
    }

    public function permanentDelete(string $id): Model
    {
        $model = $this->driver->query()->userType(DRIVER)->onlyTrashed()->find($id);
        $model->forceDelete();
        return $model;
    }

}
