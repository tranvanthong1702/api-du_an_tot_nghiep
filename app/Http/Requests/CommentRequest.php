<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;


class CommentRequest extends FormRequest
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
        $pros=Product::all();
        $arr_pro_id=[];
        foreach ($pros as $p) {
            $arr_pro_id[]=$p->id;
        }

        $users=User::all();
        $arr_user_id=[];
        foreach ($users as $u) {
            $arr_user_id[]=$u->id;
        }
        $rule=[
            'pro_id' => [
                'required',
                Rule::in($arr_pro_id)
            ],

            'user_id' => [
                'required',
                Rule::in($arr_user_id)
            ],

            'content' => 'required|string|min:5',
            'vote' => 'numeric|between:1,5',
            'status' => 'numeric',

        ];

        return $rule;
    }
    public function messages()
    {
        return [
            'pro_id.required'=>'Hãy nhập tên sản phẩm*.',
            'user_id.required'=>'Hãy nhập User',
            'content.required'=>'Hãy nhập Comment',
            'content.string'=>'Thông tin nhập vào phải là chuỗi',
            'content.min'=>'Nhập ít nhất 5 kí tự',
            'vote.between'=>'Đánh giá sản phẩm từ 1-5 ',
            'vote.numeric'=>'Đánh giá sản phẩm không được chứa kí tự',
            'status.numeric'=>'Trạng thái phải là số ',

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
