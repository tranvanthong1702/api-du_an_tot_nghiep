<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\ModelHasRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // list user
    public function index(Request $request)
    {
        $user=User::where('id','<>',1)->get();
        if($user->all()){
            $user->load('info_user','roles');
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'chưa có user trong db'
        ]);
    }
    // filter user 
    public function filterUser(Request $request)
    {
        $user = new User;
        $data=ModelHasRole::all();
        $user_role=[];
        foreach($data as $d){
            $user_role[]=$d->model_id;
        }
        if ($request->role !=null && $request->role==0) {
            $user = $user->whereNotIn('id', $user_role);
        }
        if ($request->role !=null && $request->role==1) {
            $user = $user->whereIn('id', $user_role);
        }
       if ($request->sort == 1) {
            $user = $user->orderBy('id');//cũ nhất
        }
        if ($request->sort == 2) {
            $user = $user->orderByDesc('id');//mới nhất
        }
        if ($request->sort == 3) {
            $user = $user->orderBy('user_name');//tăng dần theo anpha
        }
        if ($request->sort == 4) {
            $user = $user->orderByDesc('user_name');//giảm dần theo anpha
        }
         $user=$user->get();
        if ($user->all()) {
            $user->load('info_user');
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'không có user phù hợp trong db'
            ]);
        }
    }
    public function show($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->load(['info_user','roles']);
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'User không tồn tại'
        ]);
    }
    //xoa mem 1 user
    public function delete($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'User không tồn tại'
        ]);
    }
    //xoa vĩnh viễn 1 user
    public function forceDelete($id)
    {
        $user = User::withTrashed()->find($id);
        if ($user) {
            $user->forceDelete();
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'user không tồn tại'
        ]);
    }

    //list user đã xóa mềm
    public function trashed()
    {
        $user = User::onlyTrashed()->get();
        if ($user->all()) {
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'user không tồn tại'

        ]);
    }

    //restore 1 user
    public function backupOne($id)
    {
        $user = User::onlyTrashed()->find($id);
        if ($user) {
            $user->restore();
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'user không tồn tại'
        ]);
    }

    //restore all
    public function backupAll()
    {
        $user = User::onlyTrashed()->get();
        foreach ($user as $u) {
            $u->restore();
        }
        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    // syncRoles
    public function syncRoles(Request $request, $user_id)
    {
        // chưa làm validate cho mảng roles
        if (in_array('Admin', $request->roles) || $user_id == 1) {
            return response()->json([
                'success' => false,
                'data' => [
                    [
                        'message' => 'bạn không được cấp quyền này'
                    ]
                ],
            ]);
        }
        $user = User::find($user_id);
        $user->syncRoles($request->roles);

        return response()->json([
            'success' => true,
            'data' => [
                [
                    'message' => 'cấp quyền thành công'
                ]
            ]
        ]);
    }
    public function getAllRole()
    {
        $role=Role::where('id','<>',1)->get();
        return response()->json([
            'success' => true,
            'data' => $role
        ]);
    }
}
