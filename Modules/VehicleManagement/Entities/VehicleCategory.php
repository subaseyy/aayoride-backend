<?php

namespace Modules\VehicleManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AdminModule\Entities\ActivityLog;
use Modules\FareManagement\Entities\TripFare;

class VehicleCategory extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'image',
        'type',
        'is_active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'category_id');
    }

    public function tripFares()
    {
        return $this->hasMany(TripFare::class, 'vehicle_category_id');
    }

    public function logs ()
    {
        return $this->morphMany(ActivityLog::class, 'logable');
    }

    protected function scopeOfStatus($query, $status=1)
    {
        $query->where('is_active', $status);
    }

    protected static function newFactory()
    {
        return \Modules\VehicleManagement\Database\factories\VehicleCategoryFactory::new();
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function($item) {
            $array = [];
            foreach ($item->changes as $key => $change){
                $array[$key] = $item->original[$key];
            }
            if(!empty($array)) {
                $log = new ActivityLog();
                $log->edited_by = auth()->user()->id ?? 'user_update';
                $log->before = $array;
                $log->after = $item->changes;
                $item->logs()->save($log);
            }
        });

        static::deleted(function($item) {
            $log = new ActivityLog();
            $log->edited_by = auth()->user()->id;
            $log->before = $item->original;
            $item->logs()->save($log);
        });

    }

}
