<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSinglePushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notification;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (is_string($this->notification->fcm_token)) {
            sendDeviceNotification(
                fcm_token: $this->notification->fcm_token,
                title: $this->notification->title,
                description: $this->notification->description,
                image: $this->notification->image ?? null,
                ride_request_id: $this->notification->ride_request_id ?? null,
                type: $this->notification->type ?? null,
                action: $this->notification->action ?? null,
                user_id: $this->notification->user_id ?? null,
            );
        } else {
            foreach ($this->notification->fcm_token as $token) {
                sendDeviceNotification(
                    fcm_token: $token,
                    title: $this->notification->title,
                    description: $this->notification->description,
                    image: $this->notification->image ?? null,
                    ride_request_id: $this->notification->ride_request_id ?? null,
                    type: $this->notification->type ?? null,
                    action: $this->notification->action ?? null,
                    user_id: $this->notification->user_id ?? null,
                );
            }

        }

    }
}
