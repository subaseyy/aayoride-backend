<?php

namespace Modules\UserManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OtpVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_or_email',
        'otp',
        'is_temp_blocked',
        'expires_at',
        'failed_attempt',
        'blocked_at',
        'created_at',
        'updated_at',
    ];

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\OtpVerificationFactory::new();
    }
}
