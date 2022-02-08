<?php

namespace App\Http\Requests;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class infoUserRequest extends FormRequest
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
        $now = Carbon::now()->toDateString();
        $rule = [
            'user_id' => [
                'required',
                'exists:users,id'
            ],
            'address' => 'required',
            'birthday' => "required|date_format:Y-m-d|before:$now",
            'gender' => [
                'required',
                Rule::in([0, 1, 2])
            ],
            'image' => 'required|max:255',
        ];
        if ($this->user_id) {
            $rule['phone'] = [
                'required',
                'regex:/^0[1-9][0-9]{7,10}$/',
                Rule::unique('info_users')->ignore($this->user_id)
            ];
        } else {
            $rule['phone'] = [
                'required',
                'regex:/^0[1-9][0-9]{7,10}$/',
                Rule::unique('info_users') // không trùng phone
            ];
        }
        return $rule;
    }
    public  function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $response = response()->json([
            'message' => 'Invalid data send',
            'details' => $errors->messages(),
        ], 422);
        throw new HttpResponseException($response);
    }
}
