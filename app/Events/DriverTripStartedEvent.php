<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Modules\TripManagement\Entities\TripRequest;

class DriverTripStartedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tripRequest;
    /**
     * Create a new event instance.
     */
    public function __construct(TripRequest $tripRequest)
    {
        $this->tripRequest = $tripRequest;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("driver-trip-started.{$this->tripRequest->id}"),
        ];
    }

    public function broadcastAs()
    {
        return "driver-trip-started.{$this->tripRequest->id}";
    }

    public function broadcastWith()
    {
        return [
            'id'=>$this->tripRequest->id,
            'type'=>$this->tripRequest->type,
            'user_id'=>$this->tripRequest->customer->id,
        ];
    }
}
