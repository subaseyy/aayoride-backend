<?php

namespace Modules\AuthManagement\Http\Controllers\Api\New;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\AuthManagement\Http\Requests\AuthApiRequest;
use Modules\AuthManagement\Http\Requests\ChangePasswordRequest;
use Modules\AuthManagement\Http\Requests\UserRegisterApiRequest;
use Modules\AuthManagement\Service\Interface\AuthServiceInterface;
use Modules\Gateways\Traits\SmsGateway;
use Modules\TripManagement\Interfaces\TripRequestInterfaces;
use Modules\UserManagement\Service\Interface\CustomerLevelServiceInterface;
use Modules\UserManagement\Service\Interface\CustomerServiceInterface;
use Modules\UserManagement\Service\Interface\DriverLevelServiceInterface;
use Modules\UserManagement\Service\Interface\DriverServiceInterface;
use Modules\UserManagement\Service\Interface\OtpVerificationServiceInterface;

class AuthController extends Controller
{
    protected $trip;
    protected $customerService;
    protected $driverService;
    protected $customerLevelService;
    protected $driverLevelService;
    protected $authService;
    protected $otpVerificationService;

    public function __construct(
        TripRequestInterfaces           $trip,
        CustomerServiceInterface        $customerService,
        DriverServiceInterface          $driverService,
        CustomerLevelServiceInterface   $customerLevelService,
        DriverLevelServiceInterface     $driverLevelService,
        AuthServiceInterface            $authService,
        OtpVerificationServiceInterface $otpVerificationService
    ) {
        $this->trip = $trip;
        $this->customerService = $customerService;
        $this->driverService = $driverService;
        $this->customerLevelService = $customerLevelService;
        $this->driverLevelService = $driverLevelService;
        $this->authService = $authService;
        $this->otpVerificationService = $otpVerificationService;
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'email|unique:users',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:17|unique:users',
            'password' => 'required|min:8',
            'profile_image' => 'image|mimes:jpeg,jpg,png,gif|max:10000',
            'identification_type' => 'in:nid,passport,driving_license',
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
        $firstLevel = $route ? $this->customerLevelService->findOneBy(['user_type' => CUSTOMER, 'sequence' => 1]) : $this->driverLevelService->findOneBy(['user_type' => DRIVER, 'sequence' => 1]);
        if (!$firstLevel) {

            return response()->json(responseFormatter(LEVEL_403), 403);
        }
        $user = $route ? $this->customerService->create($request->all()) : $this->driverService->create($request->all());
        $otp = $this->authService->generateOtp($user);
        /**
         * phone no verification SMS_Body
         */
        $this->authService->sendOtpToClient($user?->phone, $otp);

        return response()->json(responseFormatter(REGISTRATION_200));
    }


    public function login(AuthApiRequest $request): JsonResponse
    {
        $user = $this->authService->checkClientRoute($request);
        if (!$user) {
            return response()->json(responseFormatter(constant: AUTH_LOGIN_404), 403);
        }
        foreach ($user->tokens as $token) {
            $token->revoke();
        }

        $hit_limit = businessConfig('maximum_login_hit')?->value ?? 5;
        $block_time = businessConfig('temporary_login_block_time')?->value ?? 60;
        $seconds_passed = Carbon::parse($user->blocked_at)->diffInSeconds();
        if ($user->is_temp_blocked) {
            if (isset($user->blocked_at) && Carbon::parse($user->blocked_at)->DiffInSeconds() <= $block_time) {
                $time = $block_time - Carbon::parse($user->blocked_at)->DiffInSeconds();
                return response()->json([
                    "response_code" => "too_many_attempt_405",
                    "message" => translate('please_try_again_after_') . CarbonInterval::seconds($time)->cascade()->forHumans(),
                ], 403);
            }

            $user->failed_attempt = 0;
            $user->is_temp_blocked = 0;
            $user->blocked_at = null;
            $user->save();
        }
        if (!Hash::check($request['password'], $user['password'])) {
            $user->failed_attempt += 1;
            if ($user->failed_attempt >= (int)$hit_limit) {
                $user->is_temp_blocked = 1;
                $user->blocked_at = now();
            }
            $user->save();
            return response()->json(responseFormatter(AUTH_LOGIN_401), 403);
        }

        if (Hash::check($request['password'], $user['password'])) {
            if ($user->is_active) {
                $verification = $user->user_type == CUSTOMER ? (businessConfig('customer_verification')?->value ?? 0) : (businessConfig('driver_verification')?->value ?? 0);
                if ($verification && !$user->phone_verified_at) {
                    $otp = $this->authService->generateOtp($user);
                    /**
                     * Phone verification SMS_Body
                     */
                    $this->authService->sendOtpToClient($user->phone, $otp);

                    return response()->json(responseFormatter(constant: DEFAULT_SENT_OTP_200, content: [
                        'is_phone_verified' => is_null($user->phone_verified_at) ? 0 : 1,
                        'verification_url' => $user->user_type == CUSTOMER ? '/api/customer/auth/otp-login' : '/api/driver/auth/otp-login'
                    ]), 202);
                }
                $access_type = $user->user_type == CUSTOMER ? CUSTOMER_PANEL_ACCESS : DRIVER_PANEL_ACCESS;
                $userData = [
                    'failed_attempt' => 0,
                    'is_temp_blocked' => 0,
                    'blocked_at' => null,
                ];

                $user = $this->authService->update(id: $user->id, data: $userData);
                return response()->json(responseFormatter(AUTH_LOGIN_200, $this->authenticate($user, $access_type)));
            }
            if ($user->user_type === 'driver') {
                return response()->json(responseFormatter(DEFAULT_USER_UNDER_REVIEW_DISABLED_401), 403);
            }
            return response()->json(responseFormatter(DEFAULT_USER_DISABLED_401), 403);
        }

        return response()->json(responseFormatter(AUTH_LOGIN_401), 403);
    }

    public function logout(): JsonResponse
    {
        if (Auth::user() !== null) {
            Auth::user()->token()->revoke();
            Auth::user()->fcm_token = null;
            Auth::user()->save();
        }
        return response()->json(responseFormatter(AUTH_LOGOUT_200), 200);
    }

    //    public function delete(): JsonResponse
    //    {
    //        if (auth('api')->user() !== null && auth('api')->user()->user_type == DRIVER){
    //            $driver = \auth('api')->user();
    //            if (count($driver->getDriverLastTrip())!=0|| $driver?->userAccount->payable_balance>0 || $driver?->userAccount->pending_balance>0 || $driver?->userAccount->receivable_balance>0){
    //                return response()->json(responseFormatter(translate("Sorry you can't delete this account, because there are ongoing rides or payment due")), 200);
    //            }
    //        }
    //        if (auth('api')->user() !== null) {
    //            auth('api')->user()->token()->revoke();
    //            auth()->user()->fcm_token = null;
    //            auth()->user()->deleted_at = now();
    //            auth()->user()->save();
    //
    //        }
    //        return response()->json(responseFormatter(ACCOUNT_DELETED_200), 200);
    //    }

    public function delete(): JsonResponse
    {
        $user = $this->driverService->findOne(auth('api')->id());

        if ($user->user_type == DRIVER) {
            if (count($user->getDriverLastTrip()) != 0 || $user?->userAccount->payable_balance > 0 || $user?->userAccount->pending_balance > 0 || $user?->userAccount->receivable_balance > 0) {
                return response()->json(responseFormatter(
                    constant: AUTH_LOGIN_403,
                    errors: [['error_code' => 403, 'message' => translate("Sorry! you can't delete your account, because your ride is ongoing or your payment is due.")]]
                ), 403);
            }
        }
        if ($user->user_type == CUSTOMER) {
            if (count($user->getCustomerUnpaidParcelAndTrips()) > 0 || count($user->getCustomerPendingTrips()) > 0 || count($user->getCustomerAcceptedTrips()) > 0 || count($user->getCustomerOngingTrips()) > 0) {
                return response()->json(responseFormatter(
                    constant: AUTH_LOGIN_403,
                    errors: [['error_code' => '403', 'message' => translate("Sorry! you can't delete your account, because your ride is ongoing or payment due.")]]
                ), 403);
            }
        }


        if (auth('api')->user() !== null) {
            auth('api')->user()->token()->revoke();
            auth()->user()->fcm_token = null;
            auth()->user()->deleted_at = now();
            auth()->user()->save();
        }
        return response()->json(responseFormatter(ACCOUNT_DELETED_200), 200);
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_or_email' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: AUTH_LOGIN_403, errors: errorProcessor($validator)), 403);
        }
        $user = $this->authService->checkClientRoute($request);
        if (!$user) {
            return response()->json(responseFormatter(constant: AUTH_LOGIN_404), 403);
        }

        $resend_after = businessConfig('otp_resend_time')?->value ?? 60;
        $data = $this->otpVerificationService->findOneBy(criteria: ['phone_or_email' => $request->phone_or_email]);

        if ($data && Carbon::parse($data->updated_at)->diffInSeconds() < $resend_after) {

            return response()->json([
                'response_code' => 'too_many_attempt_405',
                'message' => translate('please_try_again_after_') . CarbonInterval::seconds($resend_after - Carbon::parse($data->updated_at)->diffInSeconds())->forHumans(),
            ], 403);
        }
        if ($data) {
            $this->otpVerificationService->delete(id: $data->id);
        }
        $otp = $this->authService->generateOtp($user);
        /**
         * general purpose SMS_Body
         */
        $this->authService->sendOtpToClient($user->phone, $otp);

        
        return response()->json(responseFormatter(DEFAULT_200));
    }

    public function otpVerification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_or_email' => 'required',
            'otp' => 'required|min:4|max:4'
        ]);

        if ($validator->fails()) {
            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }

        $user = $this->authService->checkClientRoute($request);

        $otp = $this->otpVerificationService->findOneBy(criteria: ['phone_or_email' => $request['phone_or_email']]);
        if (!$otp) {
            return response()->json(responseFormatter(DEFAULT_404), 403);
        }
        $block_time = businessConfig('temporary_block_time')?->value ?? 30;
        $seconds_passed = Carbon::parse($otp->blocked_at)->diffInSeconds();
        if ($otp->is_temp_blocked && $seconds_passed < $block_time) {
            return response()->json([
                'response_code' => 'too_many_attempt_405',
                'message' => translate('please_try_again_after_') . CarbonInterval::seconds($block_time - $seconds_passed)->forHumans()
            ], 403);
        }

        if ($otp->is_temp_blocked) {
            $otpData = [
                'is_temp_blocked' => false,
                'blocked_at' => null,
                'failed_attempt' => 0,
            ];
            $this->otpVerificationService->update(id: $otp->id, data: $otpData);
        }
        if (Carbon::parse($otp->expires_at) > now() && ((int)$otp->otp) === ((int)$request['otp'])) {
            //If phone is not verified yet
            if (!$user->phone_verified_at) {
                $userData = [
                    'phone_verified_at' => now()
                ];
                $this->authService->updateLoginUser(id: $user->id, data: $userData);
            }
            $this->otpVerificationService->delete(id: $otp->id);
            return response()->json(responseFormatter(AUTH_LOGIN_200, self::authenticate($user, $user->user_type == CUSTOMER ? CUSTOMER_PANEL_ACCESS : DRIVER_PANEL_ACCESS)));
        }

        $hit_limit = businessConfig('maximum_otp_hit')?->value ?? 5;
        $otp->increment('failed_attempt');
        if ($hit_limit == $otp->failed_attempt) {
            $otpData = [
                'is_temp_blocked' => true,
                'blocked_at' => now(),
                'failed_attempt' => $otp->failed_attempt + 1,
            ];
            $this->otpVerificationService->update(id: $otp->id, data: $otpData);
        }
        return response()->json(responseFormatter(OTP_MISMATCH_404), 403);
    }

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

    public function otpLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone_or_email' => 'required|min:8|max:20'
        ]);
        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $user = $this->authService->checkClientRoute($request);

        if (!$user) {
            //If customer not exists
            $firstLevel = $user->user_type == CUSTOMER ? $this->customerLevelService->findOneBy(['user_type' => CUSTOMER, 'sequence' => 1]) : $this->driverLevelService->findOneBy(['user_type' => CUSTOMER, 'sequence' => 1]);
            if (!$firstLevel) {

                return response()->json(responseFormatter(LEVEL_403), 403);
            }
            $user = $this->authService->updateLoginUser(id: $user->id, data: [
                'phone' => $request->phone_or_email,
                'user_level_id' => $firstLevel->id
            ]);
        }

        $verification = businessConfig('customer_verification', BUSINESS_INFORMATION)->value ?? 0;
        if (!$verification) {

            return response()->json(responseFormatter(CUSTOMER_VERIFICATION_400), 403);
        }
        $otp = $this->authService->generateOtp($user);
        /**
         * otp login SMS_Body
         */
        $this->authService->sendOtpToClient($user->phone, $otp);

        
        return response()->json(responseFormatter(DEFAULT_200));
    }


    public function resetPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone_or_email' => 'required|min:8|max:20',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $user = $this->authService->checkClientRoute($request);

        if (!$user) {
            return response()->json(responseFormatter(constant: USER_404), 403);
        }
        $attributes = [
            'password' => bcrypt($request['password'])
        ];

        $this->authService->updateLoginUser(id: $user->id, data: $attributes);

        return response()->json(responseFormatter(constant: DEFAULT_PASSWORD_RESET_200, errors: errorProcessor($validator)), 200);
    }


    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_or_email' => 'required|min:8|max:20',
        ]);

        if ($validator->fails()) {

            return response()->json(responseFormatter(constant: DEFAULT_400, errors: errorProcessor($validator)), 403);
        }
        $user = $this->authService->checkClientRoute($request);
        if (!$user) {
            return response()->json(responseFormatter(USER_404), 403);
        }
        $otp = $this->authService->generateOtp($user);
        /**
         * forget password SMS_Body
         */
        $this->authService->sendOtpToClient($user->phone, $otp);

        
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
                'password' => bcrypt($request['new_password'])
            ];
            $this->authService->updateLoginUser(id: $user->id, data: $attributes);
            return response()->json(responseFormatter(constant: DEFAULT_PASSWORD_CHANGE_200));
        }
        return response()->json(responseFormatter(constant: DEFAULT_PASSWORD_MISMATCH_403), 403);
    }

    private function authenticate($user, $access_type)
    {
        return [
            'token' => $user->createToken($access_type)->accessToken,
            'is_active' => $user->is_active,
            'is_phone_verified' => is_null($user['phone_verified_at']) ? 0 : 1,
            'is_profile_verified' => $user->isProfileVerified(),
        ];
    }
}
