<?php

namespace Modules\UserManagement\Repositories;

use Illuminate\Database\Eloquent\Model;
use Modules\UserManagement\Entities\DriverTimeLog;
use Modules\UserManagement\Interfaces\DriverTimeLogInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class DriverTimeLogRepository implements DriverTimeLogInterface
{
    public function __construct(
        private DriverTimeLog $timeLog
    )
    {
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param bool $dynamic_page
     * @param array $except
     * @param array $attributes
     * @param array $relations
     * @return LengthAwarePaginator|array|Collection
     */
    public function get(int $limit, int $offset, bool $dynamic_page = false, array $except = [], array $attributes = [], array $relations = []): LengthAwarePaginator|array|Collection
    {
        $from = $attributes['from'] ?? null;
        $to = $attributes['to'] ?? null;
        $column = $attributes['column'] ?? null;
        $value = $attributes['value'] ?? null;
        $ExtraColumn = $attributes['column_name'] ?? null;
        $ExtraColumnValue = $attributes['column_value'] ?? null;
        $query =  $this->timeLog
            ->query()
            ->when(array_key_exists('relations', $attributes), function ($query) use($attributes){
                $query->with($attributes['relations']);
            })
            ->when($from && $to, function ($query) use($from, $to){
                $query->whereBetween('date', [$from, $to]);
            })
            ->when($column && $value, function ($query) use($value, $column){
                $query->where($column, $value);
            })
            ->when($ExtraColumn && $ExtraColumnValue, function($query) use($ExtraColumn, $ExtraColumnValue){
                $query->whereIn($ExtraColumn, $ExtraColumnValue);
            });

        if ($dynamic_page) {

            return $query->paginate(perPage: $limit,  page: $offset);
        }
        return $query->paginate($limit);
    }

    /**
     * @param string $column
     * @param string|int $value
     * @param array $attributes
     * @return Model
     */
    public function getBy(string $column, string|int $value, array $attributes = []): mixed
    {

        $ExtraColumn = $attributes['column_name'] ?? null;
        $ExtraColumnValue = $attributes['column_value'] ?? null;

        return $this->timeLog
            ->query()
            ->when(array_key_exists('relations', $attributes), function ($query) use($attributes){
                $query->with($attributes['relations']);
            })
            ->where($column, $value)
            ->when($ExtraColumn && $ExtraColumnValue, function($query) use($ExtraColumn, $ExtraColumnValue){
                $query->where($ExtraColumn, $ExtraColumnValue);
            })
            ->first();
    }

    /**
     * @param array $attributes
     * @return Model
     */
    public function updateOrCreate(array $attributes): Model
    {
        return $this->timeLog
            ->query()
            ->updateOrCreate(['driver_id' => $attributes['driver_id'], 'date' => $attributes['date']],
                [
                    'driver_id' => $attributes['driver_id'],
                    'date' => $attributes['date'],
                ]);
    }
    /**
     * @param $driver_id
     * @param $date
     * @param null $online
     * @param null $offline
     * @param bool $activeLog
     *
     */
    public function setTimeLog($driver_id , $date, $online = null, $offline = null, $accepted=null, $completed=null, $start_driving=null, $trip = null, $activeLog = false)
    {

        if ($activeLog) {
            return $this->driverActiveLog($driver_id , $date, $online , $offline , $start_driving);
        }

        return $this->driverOthersLog($driver_id,$date,$accepted,$completed,$trip);

    }

    /**
     * @param $driver_id
     * @param $date
     * @param $online
     * @param $offline
     * @return bool
     */
    private function driverActiveLog($driver_id , $date, $online = null, $offline = null, $start_driving = null): bool
    {
        $driver_time_log = $this->getBy(column:'driver_id', value:$driver_id, attributes:[
            'column_name' => 'date',
            'column_value' => $date,
        ]);
        if($driver_time_log && $driver_time_log->online && $online) {
            return true;
        }
        if($driver_time_log && ($offline || $online || $start_driving)) {
            if ($offline) {
                $driver_time_log->offline = $offline;
            }elseif($online){
                $driver_time_log->online = $online;
            }else {
                $driver_time_log->start_driving = $start_driving;
            }
            $driver_time_log->online_time = is_null($offline)?0:(strtotime($offline) - strtotime($driver_time_log->online))/60;
            $driver_time_log->save();
            return true;

        }
        else{

            $time_log = $this->time_log;
            $time_log->date = $date;
            $time_log->driver_id = $driver_id;
            $time_log->offline = $offline;
            $time_log->online = $online;
            $time_log->save();
            return true;
        }

    }

    /**
     * @param $driver_id
     * @param $date
     * @param $accepted
     * @param $completed
     * @param $trip
     * @return bool
     */
    private function driverOthersLog($driver_id , $date,$accepted, $completed, $trip)
    {
        $driver_time_log = $this->getBy(column:'driver_id', value:$driver_id, attributes:[
            'column_name' => 'date',
            'column_value' => $date,
        ]);

        $status = array('cancelled', 'failed', 'completed', 'rejected');

        if(($driver_time_log->accepted !=null) && !(in_array($trip->current_status, $status)))
        {
            if(strtotime($accepted)>strtotime($driver_time_log->completed)){

                $driver_time_log->accepted = $accepted;
                $driver_time_log->idle_time = (strtotime($accepted) - strtotime($driver_time_log->completed))/60;
                $driver_time_log->save();
                return true;

            }
        }
        else if(in_array($trip->current_status, $status) && $completed){
            $driver_time_log->completed = $completed;
            $driver_time_log->on_driving_time = (strtotime($completed) - strtotime($driver_time_log->start_driving))/60;
            $driver_time_log->save();
            return true;
        }else{
            $driver_time_log->accepted = $accepted;
            $driver_time_log->save();
            return true;
        }

        return false;
    }
}
