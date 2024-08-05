<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        protected $notification,
        protected $notify = null)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->notify) {
            foreach ($this->notify as $user) {
                if ($user->user?->is_active) {
                    sendDeviceNotification(
                        fcm_token: $user->user->fcm_token,
                        title: $this->notification['title'],
                        description: $this->notification['description'],
                        image: $this->notification['image']?? null,
                        ride_request_id: $this->notification['ride_request_id'] ?? null,
                        type: $this->notification['type'] ?? null,
                        action: $this->notification['action']?? null,
                        user_id: $user->user->id ?? null,
                    );
                }
            }
        }
         else {
             foreach ($this->notification['user'] as $user) {
                 sendDeviceNotification(
                     fcm_token: $user['fcm_token'],
                     title: $this->notification['title'],
                     description: $this->notification['description'],
                     image: $this->notification['image']?? null,
                     ride_request_id: $this->notification['ride_request_id'] ?? null,
                     type: $this->notification['type'] ?? null,
                     action: $this->notification['action']?? null,
                     user_id: $user['user_id']?? null,
                 );
             }
         }

    }
}
