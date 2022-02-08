<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class VoucherRequest extends FormRequest
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
        $now=Carbon::now()->toDateString();
        return [
            'classify_voucher_id'=> [
                'required',
                Rule::in([1,2,3])
            ],
            'title'=> 'required|max:255',
            'sale'=> 'required|gt:0|max:100',
            'customer_type'=> 'required|max:255',
            'condition'=> 'required|min:0|max:100000000',
            'expiration'=> 'required|gt:0|max:100',
            'times'=> 'required|gt:0|max:100',
            'start_day'=> "required|date|after:$now"
        ];
    }
      public function messages()
    {
        return [
           'classify_voucher_id',
            'title',
            'code',
            'sale',
            'customer_type',
            'condition',
            'expiration',
            'active',
            'planning',
            'times',
            'start_day',
            'end_day',
            'condition',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = response()->json([
            'message' => 'Invalid data send',
            'details' => $errors->messages(),
        ], 422);
        throw new HttpResponseException($response);
    }
}
