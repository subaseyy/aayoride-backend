<?php

namespace Modules\ChattingManagement\Http\Controllers\Api\Customer;

use App\Jobs\SendPushNotificationJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\ChattingManagement\Interfaces\ChannelConversationInterface;
use Modules\ChattingManagement\Interfaces\ChannelListInterface;
use Modules\ChattingManagement\Interfaces\ChannelUserInterface;
use Modules\ChattingManagement\Transformers\ChannelConversationResource;
use Modules\ChattingManagement\Transformers\ChannelListResource;
use Modules\TripManagement\Interfaces\TripRequestInterfaces;


class ChattingController extends Controller
{
    public function __construct(
        private ChannelListInterface $channel_list,
        private ChannelUserInterface $channel_user,
        private ChannelConversationInterface $conversation,
        private TripRequestInterfaces $trip
    )
    {
    }

    public function channelList(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric',
            'offset' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors:  errorProcessor($validator)), 403);
        }
        $chatList = $this->channel_list->get(limit: $request['limit'], offset: $request['offset'], dynamic_page: true, attributes: ['user_id' => $request->user()->id]);

        $chatList = ChannelListResource::collection($chatList);

        return response()->json(responseFormatter(DEFAULT_200, $chatList, $request['limit'], $request['offset']));
    }


    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function createChannel(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'to' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $channelIds = $this->channel_user->get(limit: 10000, offset: 1, attributes: ['user_id' => $request->user()->id]);
        $channelIds = $channelIds->pluck('channel_id')->toArray();
        $attributes = [
            'user_id' => $request->user()->id,
            'value' => $channelIds,
            'to' => $request['to'],
            'channel_users' => true
            ];

        $findChannel = $this->channel_list->getBy(column: 'id', value: '', attributes: $attributes);

        if ($findChannel) {

            return response()->json(responseFormatter(DEFAULT_200, $findChannel), 200);
        }
        $attributes = [
            'user_id' => $request->user()->id,
            'to' => $request['to']
        ];
        $channel = $this->channel_list->store($attributes);

        return response()->json(responseFormatter(DEFAULT_STORE_200, $channel), 200);

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $attributes = [
            'relations' => ['customer', 'driver'],
            'whereNotInColumn' => 'current_status',
            'whereNotInValue' => ['completed', 'rejected', 'failed', 'cancelled']
        ];

        $column = 'customer_id';
        if ($request->user()->user_type == 'driver') {
            $column = 'driver_id';
        }
        $trip = $this->trip->getBy(column: $column, value: auth('api')->id(), attributes: $attributes);
        if (!$trip) {
            return response()->json(responseFormatter(constant: TRIP_REQUEST_404), 403);
        }


        $validator = Validator::make($request->all(), [
            'channel_id' => 'required',
            'files' => 'required_without:message' ,
            'files.*' => 'max:10240|mimes:' . implode(',', array_column(FILE_TYPE, 'key')),
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $user = auth()->user();
        DB::beginTransaction();

            $this->channel_list->update(attributes: [], id: $request['channel_id']);
            $attributes = [
                'channel_id' => $request['channel_id'],
                'user_id' => $user->id,
                'is_read' => false,
            ];
            $this->channel_user->update(attributes: $attributes, id: '');

            $attributes = [
                'channel_id' => $request['channel_id'],
                'message' => $request['message'],
                'user_id' => $user->id
            ];
            if ($request->has('files')) {
                $attributes['files'] = $request->file('files');
            }
            $this->conversation->store($attributes);
        DB::commit();

        if ($user->user_type == 'driver') {
            $to_user = $trip->customer;
            $user_id = $to_user->id;
        } else {
            $to_user = $trip->driver;
            $user_id = $to_user->id;
        }

        $push = getNotification('new_message');

        sendDeviceNotification(
            fcm_token: $to_user->fcm_token,
            title: translate($push['title']),
            description: translate($push['description']). $user?->first_name,
            ride_request_id: $trip->id,
            type: $request->channel_id,
            action: 'new_message_arrived',
            user_id: $user_id
        );
        return response()->json(responseFormatter(DEFAULT_STORE_200), 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function conversation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'channel_id' => 'required|uuid',
            'limit' => 'required|numeric',
            'offset' => 'required|numeric',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(DEFAULT_400, null, null, null, errorProcessor($validator)), 403);
        }

        $attributes = [
            'channel_id' => $request['channel_id'],
            'user_id' => $request->user()->id,
            'is_read' => true,
        ];
        $this->channel_user->update(attributes: $attributes, id: '');
        $attributes = [
            'relations' => ['conversation_files'],
            'channel_id' => $request['channel_id'],
            'user_id' => $request->user()->id,
        ];
        $conversations = $this->conversation->get(
            limit: $request['limit'],
            offset: $request['offset'],
            dynamic_page: true,
            attributes: $attributes,
            relations: ['user']
        );
        $conversations = ChannelConversationResource::collection($conversations);

        return response()->json(responseFormatter(constant: DEFAULT_200, content: $conversations, limit: $request['limit'], offset: $request['offset']));

    }
}

