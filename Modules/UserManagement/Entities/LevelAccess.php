<?php

namespace Modules\UserManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LevelAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id',
        'user_type',
        'bid',
        'see_destination',
        'see_subtotal',
        'see_level',
        'create_hire_request',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'bid'=>'boolean',
        'see_destination'=>'boolean',
        'see_subtotal'=>'boolean',
        'see_level'=>'boolean',
        'create_hire_request'=>'boolean'
    ];

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\LevelAccessFactory::new();
    }
}
