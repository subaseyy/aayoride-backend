<?php

namespace Modules\UserManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WithdrawRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'method_id',
        'method_fields',
        'note',
        'driver_note',
        'approval_note',
        'denied_note',
        'rejection_cause',
        'is_approved',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'method_fields' => 'json',
        'amount' => 'double',
        'is_approved' => 'boolean',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function method(){
        return $this->belongsTo(WithdrawMethod::class, 'method_id');
    }
    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\WithdrawRequestFactory::new();
    }
}
