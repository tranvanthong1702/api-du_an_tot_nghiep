<?php

namespace App\Http\Controllers;

use App\Http\Requests\infoUserRequest;
use App\Models\InfoUser;
use App\Models\User;
use Illuminate\Http\Request;

class InfoUserController extends Controller
{
    // thêm thông tin chi tiết cho user
    public function store(infoUserRequest $request)
    {
        $model = new InfoUser();
        $model->fill($request->all());
        $model->save();
        return response()->json([
            'success' => true,
            'data' => $model
        ]);
    }
    public function update(infoUserRequest $request, $user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $user->load('info_user');
            $info_user = $user->info_user;
            if ($info_user->all()) {
                $info_user = $info_user->all()[0];
                $info_user->fill($request->all());
                $info_user->save();
                return response()->json([
                    'success' => true,
                    'data' => $info_user
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'data' =>'user chưa có thông tin chi tiết'
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'data' =>'user không tồn tại'
            ]);
        }
    }
}
