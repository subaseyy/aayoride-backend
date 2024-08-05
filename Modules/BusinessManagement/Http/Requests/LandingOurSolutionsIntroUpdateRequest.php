<?php

namespace Modules\BusinessManagement\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class LandingOurSolutionsIntroUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(OUR_SOLUTIONS_SECTION)],
            'title' => Rule::requiredIf(function () {
                return $this->input('type') === OUR_SOLUTIONS_SECTION;
            }),
            'sub_title' => Rule::requiredIf(function () {
                return $this->input('type') === OUR_SOLUTIONS_SECTION;
            }),
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return Auth::check();
    }
}
