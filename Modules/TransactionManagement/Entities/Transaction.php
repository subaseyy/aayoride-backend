<?php

namespace Modules\TransactionManagement\Entities;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\UserManagement\Entities\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\TripManagement\Entities\TripRequest;
use Modules\UserManagement\Entities\UserAccount;

class Transaction extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'attribute_id',
        'attribute',
        'debit',
        'credit',
        'balance',
        'user_id',
        'account',
        'transaction_type',
        'trx_ref_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'debit' => 'float',
        'credit' => 'float',
        'balance' => 'float'
    ];

    protected $guarded = [];

    protected static function newFactory()
    {
        return \Modules\TransactionManagement\Database\factories\TransactionFactory::new();
    }

    public function userAccount(): BelongsTo
    {
        return $this->belongsTo(UserAccount::class, 'user_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(TripRequest::class, 'attribute_id');
    }

    public function scopeTransactionType($query, $type)
    {
        return $query->where('attribute', $type);
    }

    public function scopeAccountType($query, $type)
    {
        return $query->where('account', $type);
    }
}
