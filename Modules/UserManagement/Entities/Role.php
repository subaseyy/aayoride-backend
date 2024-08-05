<?php

namespace Modules\UserManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AdminModule\Entities\ActivityLog;

class Role extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'name',
        'modules',
        'is_active',
        'deleted_at',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'modules' => 'array',
    ];
    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\RoleFactory::new();
    }

    public function scopeOfStatus($query, $status)
    {
        return $query->where('is_active', $status);
    }

    public function logs ()
    {
        return $this->morphMany(ActivityLog::class, 'logable');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function($item) {
            $log = new ActivityLog();
            $log->edited_by = auth()->user()->id;
            $log->after = $item;
            $item->logs()->save($log);
        });

        static::updated(function($item) {
            $log = new ActivityLog();
            $log->edited_by = auth()->user()->id;
            $log->before = $item->original;
            $log->after = $item;
            $item->logs()->save($log);
        });

        static::deleted(function($item) {
            $log = new ActivityLog();
            $log->edited_by = auth()->user()->id;
            $log->before = $item->original;
            $item->logs()->save($log);
        });

    }

}
