<?php

namespace Modules\UserManagement\Http\Controllers\Api\Driver;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\UserManagement\Entities\TimeTrack;
use Modules\UserManagement\Interfaces\DriverDetailsInterface;

class TimeTrackController extends Controller
{
    public function __construct(
        private TimeTrack $track,
        private DriverDetailsInterface $details,
    )
    {
    }


    /**
     * Store a newly created resource in storage.
     * @return JsonResponse
     */

public function onlineStatus(): JsonResponse
    {
        $id = auth('api')->id();
        $details = $this->details->getBy('user_id', $id);

        if ($details['availability_status'] == 'on_trip') {
            //check if the driver is really on trip
            $tripRequest = TripRequest::where(['driver_id' => $id, 'current_status' => ONGOING])->get();
            if ($tripRequest->count() > 0) {
                return response()->json(responseFormatter(OFFLINE_403), 403);
            } else {
                $details['availability_status'] = "available";
                $details->update();
            }
        }

        $track = $this->track->query()
            ->with('latestLog')
            ->where(['user_id' => $id, 'date' => date('Y-m-d')])
            ->latest()
            ->first();
            
	    if(!@$track){
            $track = new TimeTrack();
            $track->user_id = $id;
            $track->date = date('Y-m-d');
            $track->save();
        }

        if ($details['is_online']) {
            //means he is going to be offline

            $track->latestLog()->update([
                'offline_at' => now()
            ]);
            $track->total_online += Carbon::parse($track?->latestLog?->online_at)->diffInMinutes(now());
            $track->save();
        }

        if (!$details['is_online']) {
            //means he is going to be online
            $track->total_offline += Carbon::parse($track->latestLog?->offline_at)->diffInMinutes(now());
            $track->save();
            $track->latestLog()->create([
                'online_at' => now()
            ]);
        }
        $attributes = [
            'column' => 'user_id',
            'is_online' => $details['is_online'] == 1 ? 0 : 1,
            'availability_status' => $details['is_online'] == 1 ? 'unavailable' : 'available',
        ];
        $this->details->update(attributes: $attributes, id: $id);


        return response()->json(responseFormatter(DEFAULT_STATUS_UPDATE_200));
    }


// done go here
}
