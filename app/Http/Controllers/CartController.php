<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request)
    {
        // chưa có validate
        $user = User::find($request->user_id);
        $list_pro = $request->list_pro;
        $data = [];
        foreach ($list_pro as $p) {
            $data[$p['id']] = ['quantity' => $p['quantity']];
        }
        $user->products()->sync($data);
        return response()->json([
            'success' => true,
            'data' => [
                [
                    'message'=>'add to cart thành công'
                ]
            ]
        ]);
       
    }

}
