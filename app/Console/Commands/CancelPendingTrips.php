<?php

namespace App\Console\Commands;

use App\Jobs\SendPushNotificationJob;
use Illuminate\Console\Command;
use Modules\TripManagement\Entities\TempTripNotification;
use Modules\TripManagement\Entities\TripRequest;

class CancelPendingTrips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trip-request:cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Cancel Pending Trip after certain period';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $activeMinutes = now()->subMinutes(get_cache('trip_request_active_time') ?? 10);
        $pendingTripRequests = TripRequest::whereIn('current_status', [PENDING,ACCEPTED])
            ->where('updated_at', '<', $activeMinutes)
            ->get();
        foreach ($pendingTripRequests as $pendingTripRequest) {
            $data = TempTripNotification::with('user')->where('trip_request_id', $pendingTripRequest->id)->get();
            $push = getNotification('trip_request_cancelled');
            sendDeviceNotification(fcm_token: $pendingTripRequest->customer->fcm_token,
                title: translate($push['title']),
                description: translate(textVariableDataFormat(value: $push['description'])),
                ride_request_id: $pendingTripRequest->id,
                type: $pendingTripRequest->type,
                action: 'ride_cancelled',
                user_id: $pendingTripRequest->customer->id
            );
            if (!empty($data)) {
                $notification = [
                    'title' => translate($push['title']),
                    'description' => translate($push['description']),
                    'ride_request_id' => $pendingTripRequest->id,
                    'type' => $pendingTripRequest->type,
                    'action' => 'ride_cancelled'
                ];
                dispatch(new SendPushNotificationJob($notification, $data))->onQueue('high');
                TempTripNotification::where('trip_request_id', $pendingTripRequest->id)->delete();
            }
        }
        TripRequest::whereIn('current_status', [PENDING,ACCEPTED])
            ->where('updated_at', '<', $activeMinutes)->update([
                'current_status' => 'cancelled',
            ]);
    }
}
