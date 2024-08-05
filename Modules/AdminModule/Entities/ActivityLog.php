<?php

namespace Modules\AdminModule\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\UserManagement\Entities\User;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'logable_id',
        'logable_type',
        'edited_by',
        'before',
        'after',
        'user_type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'before' => 'array',
        'after' => 'array'
    ];


    public function logable(){
        return $this->morphTo();
    }

    public function users(){
        return $this->belongsTo(User::class, 'edited_by');
    }

    protected static function newFactory()
    {
        return \Modules\AdminModule\Database\factories\ActivityLogFactory::new();
    }
}
