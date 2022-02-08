<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest;


class BlogRequest extends FormRequest
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
            'title'=>'required|min:2',
            'image'=>'required',
            'content'=>'required|min:10'
        ];
        if($this->id){
        $rule['title']=[

            Rule::unique('blogs')->ignore($this->id)
        ];
       }else{
        $rule['title']=[

            Rule::unique('blogs')
        ];
       }


        return $rule;
    }
    public function messages()
    {
        return[
            'title',
            'image',
            'content',
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
