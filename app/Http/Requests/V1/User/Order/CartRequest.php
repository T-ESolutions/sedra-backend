<?php

namespace App\Http\Requests\V1\User\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\JWTAuth;


class CartRequest extends FormRequest
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
            'product_id' => 'required|numeric|exists:products,id',
            'qty' => 'required|numeric',
            'visitor_id' => ['nullable','exists:visitors,id',Rule::requiredIf(auth('api')->check() != true )],

        ];
    }
}
