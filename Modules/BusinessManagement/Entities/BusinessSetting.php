<?php

namespace Modules\BusinessManagement\Entities;

use App\Jobs\SendPushNotificationJob;
use App\Models\User;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Mail;
use Modules\UserManagement\Emails\NotifyUser;

class BusinessSetting extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key_name',
        'value',
        'settings_type',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'array',
    ];

    protected static function newFactory()
    {
        return \Modules\BusinessManagement\Database\factories\BusinessSettingFactory::new();
    }

    public function scopeSettingsType($query, $type)
    {
        $query->where('settings_type', $type);
    }

    protected static function boot()
    {
        parent::boot();

        //send notification to users
        static::updated(function($item) {
            if(in_array($item->attributes['key_name'] , ['terms_and_conditions', 'privacy_policy'])){
                $settings = NotificationSetting::query()->firstWhere('name', $item->attributes['key_name']);

                $data = User::query()
                    ->whereNotNull(['fcm_token', 'email'])
                    ->select('fcm_token', 'id AS user_id', 'email')
                    ->get()
                    ->toArray();
                if (!$settings->push) {
                    $users['user'] = $data;
                    $push = getNotification($item->attributes['key_name'].'_updated');
                    $users['title'] = translate($push['title']);
                    $users['description'] = translate($push['description']);
                    $users['ride_request_id'] = 'admin_notification';
                    $users['type'] = 'admin_notification';
                    $users['action'] = 'admin_notification';
                    dispatch(new SendPushNotificationJob($users))->onQueue('low');
                }
                if ($settings->email) {
                    $message = (new NotifyUser($item->attributes['key_name'], $item->value))
                        ->onQueue('emails');
                    foreach ($data as $datum) {
                        Mail::to($datum['email'])
                            ->queue($message);
                    }
                }
            }
        });


    }

}
