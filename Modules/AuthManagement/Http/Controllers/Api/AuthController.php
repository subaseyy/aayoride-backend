<?php

namespace Modules\AuthManagement\Http\Controllers\Api;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Gateways\Traits\SmsGateway;
use Modules\UserManagement\Interfaces\CustomerInterface;
use Modules\UserManagement\Interfaces\DriverLevelInterface;
use Modules\UserManagement\Interfaces\OtpVerificationInterface;
use Modules\UserManagement\Interfaces\UserInterface;
use Modules\UserManagement\Interfaces\CustomerLevelInterface;
use Modules\UserManagement\Interfaces\DriverInterface;

class AuthController extends Controller
{
    use SmsGateway;
    private array $validation_array = [
        'phone_or_email' => 'required',
        'password' => 'required',
    ];

    public function __construct(
        private OtpVerificationInterface $verification,
        private CustomerInterface        $customer,
        private DriverInterface          $driver,
        private CustomerLevelInterface   $level,
        private DriverLevelInterface     $d_level,
        private UserInterface            $user

    )
    {

    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'email|unique:users',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:17|unique:users',
            'password' => 'required|min:8',
            'profile_image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
            'identification_type' => 'in:nid,passport,driving_licence',
            'identification_number' => 'sometimes',
            'identity_images' => 'sometimes|array',
            'identity_images.*' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
            'fcm_token' => 'sometimes',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $route = str_contains($request->route()->getPrefix(), 'customer');
        if (!$route && !businessConfig('driver_self_registration')?->value) {

            return response()->json(responseFormatter(SELF_REGISTRATION_400), 403);
        }
        $first_level = $route ? $this->level->getFirstLevel() : $this->d_level->getFirstLevel();

        if (!$first_level) {

            return response()->json(responseFormatter(LEVEL_403), 403);
        }
        $request->merge(['user_level_id' => $first_level->id]);

        $user = $route ? $this->customer->store($request->all()) : $this->driver->store($request->all());
        $otp = $this->generateOtp($user);
        /**
         * phone no verification SMS_Body
         */
        $this->sendOtpToClient($user->phone, $otp);

        return response()->json(responseFormatter(REGISTRATION_200));
    }


    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), $this->validation_array);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $route = str_contains($request->route()?->getPrefix(), 'customer');
        $attributes = ['value' => $request['phone_or_email']];
        $route ? $attributes['user_type'] = 'customer' : $attributes['user_type'] = 'driver';
        $user = $this->user->getBy(attributes: $attributes);
        if (!$user) {

            return response()->json(responseFormatter(constant: AUTH_LOGIN_404), 403);
        }
        foreach($user->tokens as $token) {
            $token->revoke();
        }

        $hit_limit = businessConfig('maximum_login_hit')?->value ?? 5;
        $block_time = businessConfig('temporary_login_block_time')?->value ?? 60;
        $seconds_passed = Carbon::parse($user->blocked_at)->diffInSeconds();

        if ($user->is_temp_blocked && $seconds_passed < $block_time) {

            return response()->json( [
                'response_code' => 'too_many_attempt_405',
                'message' => translate('please_try_again_after_') . CarbonInterval::seconds($block_time - $seconds_passed)->forHumans()
            ], 403);
        }

        if ($user->is_temp_blocked) {
            $user->failed_attempt = 0;
            $user->is_temp_blocked = false;
            $user->blocked_at = null;
            $user->save();
        }

        if (Hash::check($request['password'], $user['password'])) {
            if ($user->is_active) {
                $verification = $route ?  (businessConfig('customer_verification')?->value ?? 0) : (businessConfig('driver_verification')?->value ?? 0);
                if ($verification && !$user->phone_verified_at) {
                    $otp = $this->generateOtp($user);
                    /**
                     * Phone verification SMS_Body
                     */
                    $this->sendOtpToClient($user->phone, $otp);

                    return response()->json(responseFormatter(constant: DEFAULT_SENT_OTP_200, content: [
                        'is_phone_verified' => is_null($user->phone_verified_at) ? 0 : 1,
                        'verification_url' => $route ? '/api/customer/auth/otp-login' : '/api/driver/auth/otp-login'
                    ]), 202);
                }
                $access_type = $route ? CUSTOMER_PANEL_ACCESS : DRIVER_PANEL_ACCESS;

                $user->failed_attempt = 0;
                $user->is_temp_blocked = false;
                $user->blocked_at = null;
                $user->save();
                return response()->json(responseFormatter(AUTH_LOGIN_200, $this->authenticate($user, $access_type)));
            }
            if ($user->user_type === 'driver'){
                return response()->json(responseFormatter(DEFAULT_USER_UNDER_REVIEW_DISABLED_401), 403);
            }
            return response()->json(responseFormatter(DEFAULT_USER_DISABLED_401), 403);
        }

        $user->increment('failed_attempt');
        if ($hit_limit === $user->failed_attempt) {
            $user->is_temp_blocked = true;
            $user->blocked_at = now();
        }
        $user->save();
        return response()->json(responseFormatter(AUTH_LOGIN_401), 403);
    }


    /**
     * Show the form for creating a new resource.
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        if (auth('api')->user() !== null) {
            auth('api')->user()->token()->revoke();
            auth()->user()->fcm_token = null;
            auth()->user()->save();
        }
        return response()->json(responseFormatter(AUTH_LOGOUT_200), 200);
    }


    /**
     * @throws Exception
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_or_email' => 'required'
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: AUTH_LOGIN_403, errors: errorProcessor($validator)), 403);
        }
        $user = $this->user->getBy(['value' =>  $request->phone_or_email]);
        if (!$user) {

            return response()->json(responseFormatter(constant: AUTH_LOGIN_404), 403);
        }

        $resend_after = businessConfig('otp_resend_time')?->value ?? 60;
        $data = $this->verification->getBy(column: 'phone_or_email', value: $request->phone_or_email);

        if ($data && Carbon::parse($data->updated_at)->diffInSeconds() < $resend_after) {

            return response()->json([
                'response_code' => 'too_many_attempt_405',
                'message' => translate('please_try_again_after_') . CarbonInterval::seconds($resend_after - Carbon::parse($data->updated_at)->diffInSeconds())->forHumans(),
            ], 403);
        }
        if ($data) {
            $this->verification->destroy(id: $data->id);
        }
        $otp = $this->generateOtp($user);
        /**
         * general purpose SMS_Body
         */
        $this->sendOtpToClient($user->phone, $otp);

        return response()->json(responseFormatter(DEFAULT_200));
    }

    /**
     * @param $user
     * @return int|string
     */
    private function generateOtp($user)
    {
        $otp = env('APP_MODE') == 'live' ? rand(1000, 9999) : '0000';
        $expires_at = env('APP_MODE') == 'live' ? 3 : 1000;

        $attributes = [
            'phone_or_email' => $user->phone,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes($expires_at),
        ];
        $verification = $this->verification->getBy('phone_or_email', $user->phone);
        if ($verification) {
            $verification->delete();
        }
        $this->verification->store($attributes);

        return $otp;

    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function otpVerification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_or_email' => 'required',
            'otp' => 'required|min:4|max:4'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $user_type = str_contains($request->route()->getPrefix(), 'customer') ? CUSTOMER :  DRIVER;
        $user = $this->user->getBy(attributes: ['value' => $request['phone_or_email']]);

        $otp = $this->verification->getBy(column: 'phone_or_email', value: $request['phone_or_email']);
        if (!$otp) {

            return response()->json(responseFormatter(DEFAULT_404), 403);
        }

        $block_time = businessConfig('temporary_block_time')?->value ?? 30;
        $seconds_passed = Carbon::parse($otp->blocked_at)->diffInSeconds();
        if ($otp->is_temp_blocked && $seconds_passed < $block_time) {

            return response()->json( [
                'response_code' => 'too_many_attempt_405',
                'message' => translate('please_try_again_after_') . CarbonInterval::seconds($block_time - $seconds_passed)->forHumans()
            ], 403);
        }

        if ($otp->is_temp_blocked) {
            $otp->is_temp_blocked = false;
            $otp->blocked_at = null;
            $otp->failed_attempt = 0;
            $otp->save();
        }
        if (Carbon::parse($otp->expires_at) > now() && ((int)$otp->otp) === ((int)$request['otp'])) {
            //If phone is not verified yet
            if (!$user->phone_verified_at) {
                $user->phone_verified_at = now();
                $user->save();
            }
            $this->verification->destroy(id: $otp->id);
            return response()->json(responseFormatter(AUTH_LOGIN_200, self::authenticate($user, $user_type == CUSTOMER ? CUSTOMER_PANEL_ACCESS : DRIVER_PANEL_ACCESS)));
        }

        $hit_limit = businessConfig('maximum_otp_hit')?->value ?? 5;
        $otp->increment('failed_attempt');
        if ($hit_limit == $otp->failed_attempt) {
            $otp->is_temp_blocked = true;
            $otp->blocked_at = now();
        }
        $otp->save();

        return response()->json(responseFormatter(OTP_MISMATCH_404), 403);

    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @throws GuzzleException
     */
    public function customerSocialLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'unique_id' => 'required',
            'email' => 'required',
            'medium' => 'required|in:google,facebook',
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $client = new Client();
        $token = $request['token'];
        $email = $request['email'];
        $unique_id = $request['unique_id'];

        try {
            if ($request['medium'] == 'google') {
                $res = $client->request('GET', 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token=' . $token);
            } elseif ($request['medium'] == 'facebook') {
                $res = $client->request('GET', 'https://graph.facebook.com/' . $unique_id . '?access_token=' . $token . '&&fields=name,email');
            }
            $data = json_decode($res->getBody()->getContents(), true);

        } catch (Exception $exception) {
            return response()->json(responseFormatter(DEFAULT_401), 403);
        }

        if (strcmp($email, $data['email']) === 0) {
            $user = $this->customer->getBy(column: 'email', value: $request['email']);
            if (!$user) {
                $name = explode(' ', $data['name']);
                $attributes = [
                    'first_name' => $name[0],
                    'last_name' => end($name),
                    'email' => $data['email'],
                    'profile_image' => 'def.png',
                    'password' => bcrypt(rand(1000000, 9999999))
                ];
                $user = $this->customer->store($attributes);
            }
            return response()->json(responseFormatter(AUTH_LOGIN_200, self::authenticate($user, CUSTOMER_PANEL_ACCESS)), 200);
        }

        return response()->json(responseFormatter(DEFAULT_404), 401);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function otpLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_or_email' => 'required|min:8|max:20'
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $customer = $this->user->getBy(attributes: ['value' => $request['phone_or_email'],]);

        if (!$customer) {
            //If customer not exists
            $first_level = $this->level->getFirstLevel();
            if (!$first_level) {

                return response()->json(responseFormatter(LEVEL_403), 403);
            }
            $customer = $this->customer->store(attributes: [
                'phone' => $request->phone_or_email,
                'user_level_id' => $first_level->id
            ]);
        }

        $verification = businessConfig('customer_verification', BUSINESS_INFORMATION)->value ?? 0;
        if ( ! $verification ) {

            return response()->json(responseFormatter(CUSTOMER_VERIFICATION_400), 403);
        }
        $otp = $this->generateOtp($customer);
        /**
         * otp login SMS_Body
         */
        $this->sendOtpToClient($customer->phone, $otp);

        return response()->json(responseFormatter(DEFAULT_200));

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone_or_email' => 'required|min:8|max:20',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $route = str_contains($request->route()->getPrefix(), 'customer');
        $user = $this->user->getBy(attributes: [
            'value' => $request['phone_or_email'],
            'user_type' => $route ? CUSTOMER : DRIVER,
        ]);

        if (!$user) {
            return response()->json(responseFormatter(constant: USER_404), 403);
        }
        $attributes = [
            'password' => $request['password']
        ];

        if ($user->user_type == CUSTOMER) {
            $this->customer->update(attributes: $attributes, id: $user->id);
        } else {
            $this->driver->update(attributes: $attributes, id: $user->id);
        }

        return response()->json(responseFormatter(constant: DEFAULT_PASSWORD_RESET_200, errors: errorProcessor($validator)), 200);

    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_or_email' => 'required|min:8|max:20',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $user = $this->user->getBy(attributes: ['value' => $request['phone_or_email'],]);
        if (!$user) {

            return response()->json(responseFormatter(USER_404), 403);
        }
        $otp = $this->generateOtp($user);
        /**
         * forget password SMS_Body
         */
        $this->sendOtpToClient($user->phone, $otp);



        return response()->json(responseFormatter(DEFAULT_200));
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'new_password' => 'required|min:8|different:password',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $user = $request->user('api');
        if (Hash::check($request->password, $user->password)) {

            $attributes = [
                'password' => $request['new_password']
            ];

            if ($user->user_type == CUSTOMER) {
                $this->customer->update(attributes: $attributes, id: $user->id);
            } else {
                $this->driver->update(attributes: $attributes, id: $user->id);
            }

            return response()->json(responseFormatter(constant: DEFAULT_PASSWORD_CHANGE_200));

        }

        return response()->json(responseFormatter(constant: DEFAULT_PASSWORD_MISMATCH_403), 403);

    }

    /**
     * Modify provider information
     * @param Request $request
     * @return JsonResponse
     */
    public function updateFcmToken(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required',
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $this->driver->updateFcm($request->fcm_token);

        return response()->json(responseFormatter(DEFAULT_UPDATE_200), 200);
    }

    /**
     * @param $user
     * @param $access_type
     * @return array
     */
    private function authenticate($user, $access_type)
    {
        return [
            'token' => $user->createToken($access_type)->accessToken,
            'is_active' => $user->is_active,
            'is_phone_verified' => is_null($user['phone_verified_at']) ? 0 : 1,
            'is_profile_verified' => $user->isProfileVerified(),
        ];
    }

    private function sendOtpToClient($phone, $body)
    {
        if(env('APP_MODE') != 'live') {

            return null;
        }

        SmsGateway::send($phone, $body);

    }

}

