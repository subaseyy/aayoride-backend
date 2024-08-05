<?php

namespace Modules\BusinessManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SmsSettingSetupStoreOrUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'gateway' => 'required|in:releans,twilio,nexmo,2factor,msg91,hubtel,paradox,signal_wire,019_sms,viatech,global_sms,akandit_sms,sms_to,alphanet_sms',
            'mode' => 'required|in:live,test',
            'status' => [
                'required_if:gateway,releans,twilio,nexmo,2factor,msg91,hubtel,paradox,signal_wire,019_sms,viatech,global_sms,akandit_sms,sms_to,alphanet_sms',
                Rule::in([1, 0])
            ],
            #releans
            'api_key' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 &&
                        ($this->input('gateway') == 'releans' || $this->input('gateway') == '2factor' || $this->input('gateway') == 'nexmo'));
                })
            ],
            'from' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 &&
                        ($this->input('gateway') == 'releans' || $this->input('gateway') == 'twilio' || $this->input('gateway') == 'nexmo'));
                })
            ],
            'otp_template' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 &&
                        ($this->input('gateway') == 'releans' || $this->input('gateway') == 'twilio' || $this->input('gateway') == 'nexmo'));
                })
            ],
            #twilio
            'sid' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'twilio');
                })
            ],
            'messaging_service_sid' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'twilio');
                })
            ],
            'token' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 &&
                        ($this->input('gateway') == 'twilio' || $this->input('gateway') == 'nexmo'));
                })
            ],
            #nexmo
            'api_secret' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'nexmo');
                })
            ],
            #msg91
            'template_id' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'msg91');
                })
            ],
            'auth_key' => [
                Rule::requiredIf(function () {
                    return ($this->input('status') == 1 && $this->input('gateway') == 'msg91');
                })
            ]
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }
}
