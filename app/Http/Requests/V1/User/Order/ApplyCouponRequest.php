<?php

namespace App\Http\Requests\V1\User\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class  ApplyCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'coupon_code' => 'required|string|max:255|exists:coupons,code',
            'visitor_id' => ['nullable','exists:visitors,id',Rule::requiredIf(auth('api')->check() != true )],

        ];
    }
}
