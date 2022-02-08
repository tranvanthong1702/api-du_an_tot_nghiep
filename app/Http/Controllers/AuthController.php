<?php

namespace App\Http\Controllers;

use App\Mail\ForgetPassword;
use App\Mail\VerifyAccount;
use App\Models\User;
use App\Models\Vouchers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // verify email
    public function verifyEmailAccount(Request $request)
    {
        // tạo code
        $code = rand(1111, 9999);
        $data = [
            'name' => $request->name,
            'code' => $code
        ];
        Mail::to($request->email)->queue(new VerifyAccount($data));
        return response()->json([
            'success' => true,
            'data' => $code
        ]);
    }
    // register
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_name' => 'required|between:3,15',
                'email' => 'required|email|unique:users',
                'password' => [
                    'required',
                    Password::min(5)->mixedCase()->numbers(),
                    'max:12'
                ]
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors()
            ], 422);
        }
        $user = new User();
        $user->fill($request->all());
        $user->save();
        // cập nhật các voucher cho kh
        $vouchers = Vouchers::all();
        $voucher_id = [];
        foreach ($vouchers as $v) {
            $voucher_id[] = $v->id;
        }
        $user->vouchers()->sync($voucher_id);
        return response()->json([
            'success' => true,
            'data' => 'Đăng ký thành công'
        ]);
    }

    // login
    public function login(Request $request)
    {
        // validate
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => [
                    'required',
                    Password::min(5)->mixedCase()->numbers(),
                    'max:12'
                ]
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors()
            ], 422);
        }

        $email = $request->email;
        $password = $request->password;
        //  check email
        $user = User::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            $user->load(['info_user', 'roles', 'carts', 'vouchers', 'address_custom']);
            $token = $user->createToken('auth_login')->plainTextToken;
            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Đăng nhập thất bại'
        ], 201);
    }

    //  logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'data' => 'Đăng xuất thành công'
        ]);
    }
    // update account
    public function updateAccount(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'user_name' => 'max:250',
                'avatar' => 'max:250',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors()
            ], 422);
        }
        $request->user()->update(['user_name' => $request->user_name, 'avatar' => $request->avatar]);
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }
    public function changePassword(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'password_new' => [
                    'required',
                    'confirmed',
                    Password::min(5)->mixedCase()->numbers(),
                    'max:12'
                ],
                'password_new_confirmation' => [
                    'required',
                    Password::min(5)->mixedCase()->numbers(),
                    'max:12'
                ]
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'data' => $validator->errors()
            ], 422);
        }
        $email = $request->email;
        $password = $request->password;
        $password_new = $request->password_new;
        $user = $request->user();
        if ($email == $user->email && Hash::check($password, $user->password)) {
            $user->update(['password' => $password_new]);
            return response()->json([
                'success' => true,
                'data' => 'đổi mk thành công'
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'user không đúng'
        ]);
    }
    // quên mk
    public function forgotPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $number = range(0, 9);
            shuffle($number);
            $upercase = range('A', 'Z');
            shuffle($upercase);
            $lowercase = range('a', 'z');
            shuffle($lowercase);
            $password = [$number[0], $number[1], $number[2], $number[3], $upercase[0], $lowercase[0]];
            shuffle($password);
            $password = implode('', $password);
            $user->update(['password'=>$password]);
            $data = [
                'name' => $user->user_name,
                'password' => $password,
                'url' => 'http://localhost:3000/login'
            ];
            Mail::to($request->email)->send(new ForgetPassword($data));
            return response()->json([
                'success' => true,
                'data' => 'lấy mk thành công'
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'email không tồn tại trong db'
        ]);
    }
}
