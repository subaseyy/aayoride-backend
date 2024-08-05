<?php

namespace Modules\UserManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Gateways\Traits\HasUuid;
use Modules\UserManagement\Database\Factories\UserWithdrawMethodInfoFactory;

class UserWithdrawMethodInfo extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'method_name',
        'user_id',
        'withdraw_method_id',
        'method_info',
        'is_active',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'method_info' => 'json',
        'is_active'=>'boolean',
    ];


    protected static function newFactory(): UserWithdrawMethodInfoFactory
    {
        //return UserWithdrawMethodInfoFactory::new();
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function withdrawMethod(){
        return $this->belongsTo(WithdrawMethod::class);
    }
}
