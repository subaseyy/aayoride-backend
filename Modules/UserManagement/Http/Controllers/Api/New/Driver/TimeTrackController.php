<?php

namespace Modules\UserManagement\Http\Controllers\Api\New\Driver;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TimeTrackController extends Controller
{
    protected $customerService;

    public function __construct(CustomerServiceInterface $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * Store a newly created resource in storage.
     * @return JsonResponse
     */
    public function store(): JsonResponse
    {
        $id = auth('api')->id();
        $track = $this->track->query()
            ->with('latestLog')
            ->where(['user_id' => $id, 'date' => date('Y-m-d')])
            ->latest()
            ->first();

        if (!$track){
            $track = $this->track;
            $track->date = now();
            $track->user_id = $id;
            $track->save();

            //need to set driver to online if he is offline

            $track->logs()->create([
                'online_at' => now(),
            ]);

        }

        $previous_track = $this->track->query()
            ->with('latestLog')
            ->where(['user_id' => $id, 'date' => date('Y-m-d', strtotime('yesterday'))])
            ->latest()
            ->first();
        if ($previous_track) {
            if (!$previous_track->latestLog->offline_at) {
                $previous_track->latestLog()->update([
                    'offline_at' => now()->endOfDay()
                ]);
                $previous_track->total_online += Carbon::parse($previous_track->latestLog->online_at)->diffInMinutes(now()->endOfDay());
            }
            if ($previous_track->last_ride_started_at && !$previous_track->last_ride_completed_at) {
                $previous_track->last_ride_completed_at = now()->endOfDay();
                $previous_track->total_driving += Carbon::parse($previous_track->last_ride_started_at)->diffInMinutes(now()->endOfDay());

                if ($track->isClean('date')) {
                    $track->last_ride_started_at = now();
                    $track->save();
                }
            }
            $previous_track->save();
        }


        return response()->json(responseFormatter(constant: DEFAULT_UPDATE_200, content: $track));
    }


    public function onlineStatus(): JsonResponse
    {
        $id = auth('api')->id();
        $details = $this->details->getBy('user_id', $id);
        if ($details['availability_status'] == 'on_trip') {

            return response()->json(responseFormatter(OFFLINE_403), 403);
        }

        $track = $this->track->query()
            ->with('latestLog')
            ->where(['user_id' => $id, 'date' => date('Y-m-d')])
            ->latest()
            ->first();

        if ($details['is_online']) {
            //means he is going to be offline

            $track->latestLog()->update([
                'offline_at' => now()
            ]);
            $track->total_online += Carbon::parse($track->latestLog->online_at)->diffInMinutes(now());
            $track->save();

        }

        if (!$details['is_online']) {
            //means he is going to be online
            $track->total_offline += Carbon::parse($track->latestLog->offline_at)->diffInMinutes(now());
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
}
