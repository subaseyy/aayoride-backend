<?php

namespace Modules\UserManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAccount extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'user_id',
        'payable_balance',
        'receivable_balance',
        'received_balance',
        'pending_balance',
        'wallet_balance',
        'total_withdrawn',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'payable_balance' => 'float',
        'receivable_balance' => 'float',
        'received_balance' => 'float',
        'pending_balance' => 'float',
        'wallet_balance' => 'float',
        'total_withdrawn' => 'float',
    ];

    protected static function newFactory()
    {
        return \Modules\UserManagement\Database\factories\UserAccountFactory::new();
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }

    public function scopeAdminAccount($query)
    {
        return $query->where('user_id', User::query()->firstWhere('user_type', 'super-admin')->id);
    }
}
