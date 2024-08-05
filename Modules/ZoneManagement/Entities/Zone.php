<?php

namespace Modules\ZoneManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use MatanYadaev\EloquentSpatial\Objects\Polygon;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use Modules\AdminModule\Entities\ActivityLog;
use Modules\FareManagement\Entities\TripFare;
use Modules\FareManagement\Entities\ZoneWiseDefaultTripFare;
use Modules\TripManagement\Entities\TripRequest;
use Modules\UserManagement\Entities\UserLastLocation;

class Zone extends Model
{
    use HasFactory, SoftDeletes, HasUuid, HasSpatial;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'coordinates',
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
        'coordinates' => Polygon::class
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];


    public function tripFares()
    {
        return $this->hasMany(TripFare::class, 'zone_id');
    }

    public function defaultFare(): HasOne
    {
        return $this->hasOne(ZoneWiseDefaultTripFare::class, 'zone_id');
    }

    public function tripRequest()
    {
        return $this->hasMany(TripRequest::class, 'zone_id');
    }

    public function logs()
    {
        return $this->morphMany(ActivityLog::class, 'logable');
    }

    protected function scopeOfStatus($query, $status = 1)
    {
        $query->where('is_active', $status);
    }

    public function customers()
    {
        return $this->hasMany(UserLastLocation::class, 'zone_id')->where('type', CUSTOMER);
    }

    public function drivers()
    {
        return $this->hasMany(UserLastLocation::class, 'zone_id')->where('type', DRIVER);
    }

    protected static function newFactory()
    {
        return \Modules\ZoneManagement\Database\factories\ZoneFactory::new();
    }

    public function zoneTripFares()
    {
        return $this->hasMany(TripFare::class, 'zone_id');
    }

    public static function query(): Builder
    {
        return parent::query();
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($item) {
            $array = [];

            foreach ($item->changes as $key => $change) {
                if ($key=="coordinates"){
                    $array[$key] = json_decode($item->original[$key],true);
                }else{
                    $array[$key] = $item->original[$key];
                }
            }
            if (!empty($array)) {
                $log = new ActivityLog();
                $log->edited_by = auth()->user()->id ?? 'user_update';
                $log->before = $array;
                $log->after = $item->changes;
                $item->logs()->save($log);
            }
        });

        static::deleted(function ($item) {
            $array = [];

            foreach ($item->changes as $key => $change) {
                if ($key=="coordinates"){
                    $array[$key] = json_decode($item->original[$key],true);
                }else{
                    $array[$key] = $item->original[$key];
                }
            }
            $log = new ActivityLog();
            $log->edited_by = auth()->user()->id;
            $log->before = $array;
            $item->logs()->save($log);
        });

    }
}
