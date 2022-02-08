<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'user_name' => 'required|min:2|max:255',
            'email' => 'required|email',
            'password'=>'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/',
            'avatar'=> 'required',
        ];
        if($this->id){
        $rule['email']=[

            Rule::unique('users')->ignore($this->id)
        ];
       }else{
        $rule['email']=[

            Rule::unique('users')
        ];
       }
         return $rule;
    }
    public function messages()
    {
        return[
            'user_name',
            'email',
            'password',
            'avatar',
        ];
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
