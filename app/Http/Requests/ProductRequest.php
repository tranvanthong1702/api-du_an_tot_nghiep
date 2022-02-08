<?php

namespace App\Http\Requests;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
        $cates=Category::all();
        $arr_cate_id=[];
        foreach($cates as $c){
            $arr_cate_id[]=$c->id;
        }
        $now=Carbon::now()->toDateString();
        $rule=[
            'cate_id' => [
                'required',
                Rule::in($arr_cate_id)
            ],//các giá trị nằm trong mảng chỉ định

            'image' => 'required|max:255',// tối đa 255 kí tự
            'price' => 'required|gt:0|lt:100000000',//0 <price <100000000
            'sale' => 'nullable|numeric|min:0|lt:100',//0 <=sale <100, đc phép null,phải là số
            'status' => [
                'required',//bắt buộc
                Rule::in([0,1])
            ],// các giá trị nằm trong mảng chỉ định
            'expiration_date'=>"nullable|date|after:$now",//được phép null-đúng định dạng date-sau ngày hiện tại
            'desc_short' => 'required',// bắt buộc
            'description' => 'required'// bắt buộc
        ];
       if($this->id){
        $rule['name']=[
            'required','max:255',
            Rule::unique('products')->ignore($this->id)
        ];
       }else{
        $rule['name']=[
            'required','max:255',//tối đa 255 ký tự
            Rule::unique('products')// không trùng tên
        ];
       }


        return $rule;
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
