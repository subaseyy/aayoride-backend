<?php

namespace App\WebSockets\Handler;

use App\Models\User;
use BeyondCode\LaravelWebSockets\Apps\App;
use BeyondCode\LaravelWebSockets\QueryParameters;
use BeyondCode\LaravelWebSockets\WebSockets\Exceptions\UnknownAppKey;
use Modules\TripManagement\Entities\TripRequest;
use Modules\UserManagement\Repositories\UserLastLocationRepository;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;
use Ratchet\WebSocket\MessageComponentInterface;


class UserLocationSocketHandler implements MessageComponentInterface
{
    public function __construct(private UserLastLocationRepository $location)
    {
    }

    function onMessage(ConnectionInterface $from, MessageInterface $msg)
    {
        $data = json_decode($msg->getPayload(), true);
        if (isset($data['user_id'], $data['latitude'], $data['longitude'], $data['zone_id'])) {
            $attributes = [
                'user_id' => $data['user_id'],
                'type' => $data['type'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'zone_id' => $data['zone_id']
            ];
            $this->location->updateOrCreate($attributes);

        }
        elseif (isset($data['user_id'])) {
            $user = User::query()->find($data['user_id']);
            if ($user) {
                $trip = TripRequest::query()
                    ->with(['driver.lastLocations'])
                    ->where('customer_id', $user->id)
                    ->latest()
                    ->first();

                if ($trip && $trip->driver_id && $trip->current_status != 'cancelled' && $trip->current_status != 'completed') {
                    $from->send($trip->driver->lastLocations);
                }
            }
        }

    }

    function onOpen(ConnectionInterface $conn)
    {
        $this->verifyAppKey($conn)->generateSocketId($conn);

    }

    function onClose(ConnectionInterface $conn)
    {
        // TODO: Implement onClose() method.
    }

    function onError(ConnectionInterface $conn, \Exception $e)
    {
        // TODO: Implement onError() method.
    }

    protected function verifyAppKey(ConnectionInterface $connection)
    {
        $appKey = QueryParameters::create($connection->httpRequest)->get('appKey');
        if (! $app = App::findByKey($appKey)) {
            throw new UnknownAppKey($appKey);
        }
        $connection->app = $app;

        return $this;
    }

    protected function generateSocketId(ConnectionInterface $connection)
    {
        $socketId = sprintf('%d.%d', random_int(1, 1000000000), random_int(1, 1000000000));
        $connection->socketId = $socketId;

        return $this;
    }
}

