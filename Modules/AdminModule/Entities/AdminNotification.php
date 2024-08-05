<?php

namespace Modules\AdminModule\Entities;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\TransactionManagement\Entities\Transaction;
use Modules\TripManagement\Entities\TripRequest;
use Modules\UserManagement\Entities\User;

class AdminNotification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model',
        'model_id',
        'message',
        'is_seen',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_seen' => 'boolean',
    ];

    protected static function newFactory()
    {
        return \Modules\AdminModule\Database\factories\AdminNotificationFactory::new();
    }

    public function trip(){
        $this->belongsTo(TripRequest::class, 'type_id');
    }

    public function user() {
        $this->belongsTo(User::class,  'type_id');
    }

    public function trasacion(){
        $this->belongsTo(Transaction::class, 'type_id');
    }

    public function scopeOfNotSeen(Builder $query, $value = false): Builder
    {
        return $query->where('is_seen', $value);
    }
}
