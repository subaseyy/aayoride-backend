<?php

namespace Modules\UserManagement\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RoleUser extends Pivot
{
    use HasFactory;

    protected $table = 'role_user';

    protected $fillable = [
        'role_id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\RoleUserFactory::new();
    }
}
