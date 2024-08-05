<?php

namespace Modules\UserManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WithdrawRequestMultipleActionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'status' => Rule::in([APPROVED,DENIED,SETTLED,'reverse','invoice']),
            'ids'=>'array',
            'approval_note'=>[
                Rule::requiredIf($this->status == APPROVED && $this->type != 'type'),
                'max:2000'
            ],
            'denied_note'=>[
                Rule::requiredIf($this->status == DENIED && $this->type != 'type'),
                'max:2000'
            ],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
}
