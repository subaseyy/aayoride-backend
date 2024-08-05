<?php

namespace Modules\VehicleManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AdminModule\Entities\ActivityLog;
use Modules\FareManagement\Entities\TripFare;
use Modules\UserManagement\Entities\User;

class Vehicle extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = [
        'ref_id',
        'brand_id',
        'model_id',
        'category_id',
        'licence_plate_number',
        'licence_expire_date',
        'vin_number',
        'transmission',
        'fuel_type',
        'ownership',
        'driver_id',
        'documents',
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
        'licence_expire_date' => 'date',
        'documents' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function newFactory()
    {
        return \Modules\VehicleManagement\Database\factories\VehicleFactory::new();
    }

    protected function scopeOfStatus($query, $status=1)
    {
        $query->where('is_active', $status);
    }


    public function brand(): BelongsTo
    {
        return $this->belongsTo(VehicleBrand::class, 'brand_id');
    }

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'model_id');
    }

    public function category()
    {
        return $this->belongsTo(VehicleCategory::class, 'category_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function tripFares()
    {
        return $this->hasMany(TripFare::class, 'vehicle_category_id');
    }

    public function logs ()
    {
        return $this->morphMany(ActivityLog::class, 'logable');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item){
            $item->ref_id = $item->count() + 100000;
        });

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
