<?php

namespace App\Http\Requests\V1\User\Order;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
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
            "address_id" => 'nullable|exists:addresses,id',
            "coupon_code" => 'nullable|exists:coupons,code',
            "payment_type" => "required|in:" . Order::PAYMENT_TYPE_CASH . "," . Order::PAYMENT_TYPE_VISA,
            "notes" => "nullable|string",

            'visitor_id' => ['nullable','exists:visitors,id',Rule::requiredIf(auth('api')->check() != true )],
            "f_name" =>  ['nullable','string',Rule::requiredIf(auth('api')->check() != true )],
            "phone" =>  ['nullable','string',Rule::requiredIf(auth('api')->check() != true )],
            "address" =>  ['nullable','string',Rule::requiredIf(auth('api')->check() != true )],
            "country_id" =>  ['nullable','exists:countries,id',Rule::requiredIf(auth('api')->check() != true )],

        ];
    }
}
