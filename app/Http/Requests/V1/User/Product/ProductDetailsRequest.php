<?php

namespace App\Http\Requests\V1\User\Product;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductDetailsRequest extends FormRequest
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
//        request()->user_phone = request()->country_code . '' . request()->phone;
        return [
            'id' => 'required|exists:products,id,is_active,1',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        $error = implode('- ', $validator->errors()->all());
        throw new HttpResponseException(

            response()->json(msgdata(not_acceptable(), trans('lang.there_is_some_errors'), $error), not_acceptable())
        );
    }
}
