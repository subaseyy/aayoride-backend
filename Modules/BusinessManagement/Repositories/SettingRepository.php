<?php

namespace Modules\BusinessManagement\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Gateways\Entities\Setting;
use Modules\BusinessManagement\Interfaces\SettingInterface;

class SettingRepository implements SettingInterface
{

    public function __construct(
        private Setting $business_setting
    )
    {
    }

    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes= [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $query = $this->business_setting
            ->query()
            ->when(array_key_exists('column', $attributes) , function ($query) use($attributes){
                $query->where([$attributes['column'] => $attributes['value']]);
            })
            ->when(array_key_exists('settings_type', $attributes), function ($query) use($attributes){
                $query->settingsType($attributes['settings_type']);
            })->when(array_key_exists('select_columns', $attributes), function($query) use($attributes){
                $query->select($attributes['select_columns']);
            });

        if($dynamic_page) {
            return $query->paginate($limit, ['*'], $offset);
        }

        return $query->paginate($limit);
    }

    public function getBy(string $column, string|int $value, array $attributes = []): Model|null
    {
        return $this->business_setting
            ->query()
            ->where(['key_name' => $attributes['key_name'], 'settings_type' => $attributes['settings_type']])
            ->first();
    }


    public function store(array $attributes): Model
    {
        return $this->business_setting
            ->query()
            ->updateOrCreate(['key_name' => $attributes['key_name'], 'settings_type' => $attributes['settings_type']],
                [
                    'key_name' => $attributes['key_name'],
                    'value' => $attributes['value'],
                    'settings_type' => $attributes['settings_type'],
                ]);
    }

    public function update(array $attributes, string $id): Model
    {
        // TODO: Implement update() method.
    }

    public function destroy(string $id): Model
    {
        // TODO: Implement destroy() method.
    }

}
