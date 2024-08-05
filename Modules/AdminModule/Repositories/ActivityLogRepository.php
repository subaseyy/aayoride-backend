<?php

namespace Modules\AdminModule\Repositories;

use Modules\AdminModule\Entities\ActivityLog;
use Modules\AdminModule\Interfaces\ActivityLogInterface;

class ActivityLogRepository implements ActivityLogInterface
{

    public function get(array $attributes= [], $export = false, array $relations = [] , array $orderBy = [])
    {
        $id = $attributes['logable_id'] ?? null;
        $search = array_key_exists('search', $attributes)? $attributes['search'] : '';
        $queryParams = ['id' => $id, 'search' => $search];

        $logs = ActivityLog::query()
            ->with('users')
            ->where('logable_type', $attributes['logable_type'])
            ->when(array_key_exists('user_type', $attributes), function ($query) use($attributes){
                $query->where('user_type', $attributes['user_type']);
            })
            ->when($search, function ($query) use($search){
                $query->whereHas('users', function ($query)use($search){
                    $query->where('email', 'like', '%'. $search. '%');
                });
            })
            ->when(array_key_exists('logable_id', $attributes), function ($query) use($attributes){
                $query->where('logable_id', $attributes['logable_id']);
            })
            ->orderBy('created_at', 'desc');
        if ($export) {

            return $logs->get();
        }

        return $logs->paginate(paginationLimit())
            ->appends($queryParams);
    }
}
