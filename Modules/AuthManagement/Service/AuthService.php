<?php

namespace Modules\AuthManagement\Service;

use App\Service\BaseService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\Gateways\Traits\SmsGateway;
use Modules\UserManagement\Repository\OtpVerificationRepositoryInterface;
use Modules\UserManagement\Repository\UserRepositoryInterface;

class AuthService extends BaseService implements Interface\AuthServiceInterface
{
    use SmsGateway;
    protected $userRepository;
    protected $otpVerificationRepository;
    public function __construct(UserRepositoryInterface $userRepository, OtpVerificationRepositoryInterface $otpVerificationRepository)
    {
        parent::__construct($userRepository);
        $this->userRepository = $userRepository;
        $this->otpVerificationRepository = $otpVerificationRepository;
    }

    public function checkClientRoute($request)
    {
        $route = str_contains($request->route()?->getPrefix(), 'customer');
        if ($route) {
            $user = $this->userRepository->findOneBy(criteria: ['phone' => $request->phone_or_email, 'user_type' => CUSTOMER]);
        } else {
            $user = $this->userRepository->findOneBy(criteria: ['phone' => $request->phone_or_email, 'user_type' => DRIVER]);
        }
        return $user;
    }

    public function generateOtp($user)
    {
        $otp = env('APP_MODE') == 'live' ? $this->generateUniqueOTP() : '0000';
        $expires_at = env('APP_MODE') == 'live' ? 3 : 1000;
        $attributes = [
            'phone_or_email' => $user->phone,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes($expires_at),
        ];
        $verification = $this->otpVerificationRepository->findOneBy(['phone_or_email' => $user->phone]);
        if ($verification) {
            $verification->delete();
        }
        $this->otpVerificationRepository->create(data: $attributes);
        return $otp;
    }

    public function generateUniqueOTP($length = 4)
    {
        $first_digit = rand(1, 9);
        $remaining_digits = array_merge(range(0, 9), range(0, 9));
        shuffle($remaining_digits);
        $otp_array = array_slice($remaining_digits, 0, $length - 1);
        array_unshift($otp_array, $first_digit);
        return implode('', $otp_array);
    }

    public function updateLoginUser(string|int $id, array $data): ?Model
    {
        return $this->userRepository->update(id: $id, data: $data);
    }


    public function sendOtpToClient($phone, $body)
    {
        if (env('APP_MODE') != 'live') {

            return null;
        }
        return self::send($phone, $body);
    }

    public static function send($phone, $otp)
    {
        /**
         * Using Sparrow SMS for sending OTP
         */
        $body = "Dear User,\nYour OTP is {$otp}.It is valid for 5 minutes.\nThank you\nAayo Rides Nepal";
        $token = env('SPARROW_TOKEN');
        $sender = env('SPARROW_SENDER');
        $args = http_build_query(array(
            'token' => $token,
            'from' => $sender,
            'to' => $phone,
            'text' => $body
        ));

        $url = "http://api.sparrowsms.com/v2/sms/";

        # Make the call using API.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Response
        $response = curl_exec($ch);

        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [$status_code, json_decode($response)->response];
    }
}
