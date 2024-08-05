<?php

namespace Modules\UserManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ModuleAccess extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'role_id',
        'module_name',
        'view',
        'add',
        'update',
        'delete',
        'log',
        'export',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'view'=>'boolean',
        'add'=>'boolean',
        'update'=>'boolean',
        'delete'=>'boolean',
        'log'=>'boolean',
        'export'=>'boolean'
    ];

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\ModuleAccessFactory::new();
    }
}
