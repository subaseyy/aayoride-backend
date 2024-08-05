<?php

namespace Modules\ParcelManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AdminModule\Entities\ActivityLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParcelWeight extends Model
{
    use HasFactory, HasUuid, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'min_weight',
        'max_weight',
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
        'min_weight' => 'float:2',
        'max_weight' => 'float:2',
        'is_active' => 'boolean',
    ];


    protected static function newFactory()
    {
        return \Modules\ParcelManagement\Database\factories\ParcelWeightFactory::new();
    }

    public function logs (){
        return $this->morphMany(ActivityLog::class, 'logable');
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
            $log->user_type =auth()->user()->user_type;
            $item->logs()->save($log);
        });

    }
}
