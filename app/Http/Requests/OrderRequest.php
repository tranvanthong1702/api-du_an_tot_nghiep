<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
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
            'total_price'=>'required|gt:0|lt:100000000',
            'customer_name'=>'required|max:255',
            'customer_email'=>'required|email|max:255',
            'customer_phone'=>[
                'required',
                'regex:/^0[1-9][0-9]{7,10}$/'
            ],
            'customer_address'=>'required',
            'transportation_costs'=>'required|gt:0|lt:100000000',
            'payments'=>[
                'required',
                Rule::in([0,1])
            ],
            'provinceID'=>'required|gt:0|lt:100000000',
            'districtID'=>'required|gt:0|lt:100000000',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = response()->json([
            'success' => false,
            'data' => 'Chưa có sp nào trong đơn hàng'
        ],);
        throw new HttpResponseException($response);
    }
}
