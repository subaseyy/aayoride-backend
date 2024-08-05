<?php


use Illuminate\Support\Facades\Http;
use Modules\UserManagement\Entities\AppNotification;

if (!function_exists('sendDeviceNotification')) {
    function sendDeviceNotification($fcm_token, $title, $description, $image = null, $ride_request_id = null, $type = null, $action = null, $user_id = null, $user_name = null ,array $notificationData = []): bool|string
    {
        if ($user_id) {
            $notification = new AppNotification();
            $notification->user_id = $user_id;
            $notification->ride_request_id = $ride_request_id ?? null;
            $notification->title = $title ?? 'Title Not Found';
            $notification->description = $description ?? 'Description Not Found';
            $notification->type = $type ?? null;
            $notification->action = $action ?? null;
            $notification->save();
        }
        $image = asset('storage/app/public/push-notification') . '/' . $image;
            $rewardType = $notification && array_key_exists('reward_type',$notificationData) ? $notificationData['reward_type'] : null;
            $rewardAmount = $notification && array_key_exists('reward_amount',$notificationData) ? $notificationData['reward_amount'] : 0;
            $nextLevel = $notification && array_key_exists('next_level',$notificationData) ? $notificationData['next_level'] : null;

        $postData = [
            'message' => [
                'token' => $fcm_token,
                'data' => [
                    'title' => (string)$title,
                    'body' => (string)$description,
                    "ride_request_id" => (string)$ride_request_id,
                    "type" => (string)$type,
                    "user_name" => (string)$user_name,
                    "title_loc_key" => (string)$ride_request_id,
                    "body_loc_key" => (string)$type,
                    "image" => (string)$image,
                    "action"=>(string)$action ,
                    "reward_type"=>(string)$rewardType ,
                    "reward_amount"=>(string)$rewardAmount ,
                    "next_level"=>(string)$nextLevel ,
                    "sound" => "notification.wav",
                    "android_channel_id" => "hexa-ride"
                ],
                'notification' => [
                    'title' => (string)$title,
                    'body' => (string)$description,
                ]
            ]
        ];
        return sendNotificationToHttp($postData);

//        return sendNotificationToHttp($url, $postdata, $header);
    }
}

if (!function_exists('sendTopicNotification')) {
    function sendTopicNotification($topic, $title, $description, $image = null, $ride_request_id = null, $type = null): bool|string
    {
//        $config = businessConfig('server_key');
//
//        $url = "https://fcm.googleapis.com/fcm/send";
//        $header = ["authorization: key=" . $config->value ?? null,
//            "content-type: application/json",
//        ];

        $image = asset('storage/app/public/push-notification') . '/' . $image;
//        $topic_str = "/topics/" . $topic;
//
//        $postdata = '{
//             "to":"' . $topic_str . '",
//             "notification" : {
//                    "title":"' . $title . '",
//                    "body" : "' . $description . '",
//                    "ride_request_id": "' . $ride_request_id . '",
//                    "type": "' . $type . '",
//                    "title_loc_key": "' . $ride_request_id . '",
//                    "body_loc_key": "' . $type . '",
//                    "image": "' . $image . '",
//                    "sound": "notification.wav",
//                    "android_channel_id": "hexa-ride"
//                },
//                "data": {
//                    "title":"' . $title . '",
//                    "body" : "' . $description . '",
//                    "ride_request_id": "' . $ride_request_id . '",
//                    "type": "' . $type . '",
//                    "title_loc_key": "' . $ride_request_id . '",
//                    "body_loc_key": "' . $type . '",
//                    "image": "' . $image . '",
//                    "sound": "notification.wav",
//                    "android_channel_id": "hexa-ride"
//                },
//                "priority":"high"
//              }';

//        return sendNotificationToHttp($postdata);


        $postData = [
            'message' => [
                'topic' => $topic,
                'data' => [
                    'title' => (string)$title,
                    'body' => (string)$description,
                    "ride_request_id" => (string)$ride_request_id,
                    "type" => (string)$type,
                    "title_loc_key" => (string)$ride_request_id,
                    "body_loc_key" => (string)$type,
                    "image" => (string)$image,
                    "sound" => "notification.wav",
                    "android_channel_id" => "hexa-ride"
                ],
                'notification' => [
                    'title' => (string)$title,
                    'body' => (string)$description,
                ]
            ]
        ];
        return sendNotificationToHttp($postData);
    }
}

//function sendPushNotificationToTopic(array|object $data, string $topic = 'sixvalley'): bool|string
//{
//
//    $postData = [
//        'message' => [
//            'topic' => $topic,
//            'data' => [
//                'title' => (string)$data['title'],
//                'body' => (string)$data['description'],
//                'image' => $data['image'],
//                'order_id' => (string)$data['order_id'] ?? '',
//                'type' => (string)$data['type'],
//                'is_read' => '0'
//            ],
//            'notification' => [
//                'title' => (string)$data['title'],
//                'body' => (string)$data['description'],
//            ]
//        ]
//    ];
//    return $this->sendNotificationToHttp($postData);
//}

/**
 * @param string $url
 * @param string $postdata
 * @param array $header
 * @return bool|string
 */
function sendCurlRequest(string $url, string $postdata, array $header): string|bool
{
    $ch = curl_init();
    $timeout = 120;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    // Get URL content
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

function sendNotificationToHttp(array|null $data): bool|string|null
{
    $key = json_decode(businessConfig('server_key')->value);
    $url = 'https://fcm.googleapis.com/v1/projects/' . $key->project_id . '/messages:send';
    $headers = [
        'Authorization' => 'Bearer ' . getAccessToken($key),
        'Content-Type' => 'application/json',
    ];
    try {
        return Http::withHeaders($headers)->post($url, $data);
    } catch (\Exception $exception) {
        return false;
    }
}

function getAccessToken($key): string
{
    $jwtToken = [
        'iss' => $key->client_email,
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud' => 'https://oauth2.googleapis.com/token',
        'exp' => time() + 3600,
        'iat' => time(),
    ];
    $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
    $jwtPayload = base64_encode(json_encode($jwtToken));
    $unsignedJwt = $jwtHeader . '.' . $jwtPayload;
    openssl_sign($unsignedJwt, $signature, $key->private_key, OPENSSL_ALGO_SHA256);
    $jwt = $unsignedJwt . '.' . base64_encode($signature);

    $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
        'assertion' => $jwt,
    ]);
    return $response->json('access_token');
}
