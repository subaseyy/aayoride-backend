<?php

namespace Modules\UserManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class WithdrawMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'method_name',
        'method_fields',
        'is_default',
        'is_active',
        'created_at',
        'updated_at',
    ];
    protected $casts = [
        'method_fields' => 'json',
        'is_default'=>'boolean',
        'is_active'=>'boolean',
    ];

    public function withdrawRequests()
    {
        return $this->hasMany(WithdrawRequest::class, 'method_id');
    }
    public function pendingWithdrawRequests()
    {
        return $this->hasMany(WithdrawRequest::class, 'method_id')->where('status',PENDING);
    }
    public function approvedWithdrawRequests()
    {
        return $this->hasMany(WithdrawRequest::class, 'method_id')->where('status',APPROVED);
    }
    public function deniedWithdrawRequests()
    {
        return $this->hasMany(WithdrawRequest::class, 'method_id')->where('status',DENIED);
    }

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\WithdrawMethodFactory::new();
    }


}
