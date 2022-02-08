<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoucherRequest;
use App\Models\Vouchers;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Classify_vouchers;

class VouchersController extends Controller
{
    // list Classify_vouchers
    public function list_Classify_vouchers()
    {
        $data = Classify_vouchers::all();
        if ($data->all()) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'no data'
        ]);
    }

    // active planning
    public function planning($id)
    {
        $voucher = Vouchers::find($id);
        if ($voucher) {
            $start_day = $voucher->start_day;
            $start_day = Carbon::create($start_day);
            $now_day = Carbon::create(Carbon::now()->toDateString());
            if ($now_day->diffInDays($start_day) > 0) {
                $voucher::where('id', $voucher->id)->update(['planning' => 1]);
                return response()->json([
                    'success' => true,
                    'data' => 'planning thành công'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'ngày bắt đầu cần lớn hơn ngày hiện tại'
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'data' => 'no data'
        ]);
    }
     // no-active planning
     public function noPlanning($id)
     {
         $voucher = Vouchers::find($id);
         if ($voucher) {
             $start_day = $voucher->start_day;
             $start_day = Carbon::create($start_day);
             $now_day = Carbon::create(Carbon::now()->toDateString());
             if ($now_day->diffInDays($start_day) > 0) {
                 $voucher::where('id', $voucher->id)->update(['planning' => 0]);
                 return response()->json([
                     'success' => true,
                     'data' => 'un planning thành công'
                 ]);
             } else {
                 return response()->json([
                     'success' => false,
                     'message' => 'ngày bắt đầu cần lớn hơn ngày hiện tại'
                 ]);
             }
         }
         return response()->json([
             'success' => false,
             'data' => 'no data'
         ]);
     }
    // list all voucher
    public function index()
    {
        $voucher= Vouchers::all();
        if($voucher->all()){
            return response()->json([
                'success' => true,
                'data' => $voucher
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'no voucher'
        ]);
    }
    // list voucher chưa active
    public function NoActive()
    {
        $voucher = Vouchers::where('active',0)->get();
        if ($voucher->all()) {
            return response()->json([
                'success' => true,
                'data' => $voucher
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'no data'
            ]);
        }
    }
     // list voucher đã active
     public function Active()
     {
         $voucher = Vouchers::where('active',1)->get();
         if ($voucher->all()) {
             return response()->json([
                 'success' => true,
                 'data' => $voucher
             ]);
         } else {
             return response()->json([
                 'success' => false,
                 'message' => 'no data'
             ]);
         }
     }
    // add voucher
    public function store(Request $request)
    {
        $model = new Vouchers();
        $model->fill($request->all());
        $start_day = Carbon::create($request->start_day);
        $end_day = $start_day->addDays($request->expiration);
        $model->end_day = $end_day;
        $model->save();
        return response()->json([
            'success' => true,
            'data' => $model
        ]);
    }
    // update voucher
    public function update(Request $request, $id)
    {
        $voucher = Vouchers::where('id',$id)->where('active',0)->first();
        if ($voucher) {
            $voucher->fill($request->all());
            $start_day = Carbon::create($request->start_day);
            $end_day = $start_day->addDays($request->expiration);
            $voucher->end_day = $end_day;
            $voucher->save();
            return response()->json([
                'success' => true,
                'data' => $voucher
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Voucher ko tồn tại hoặc đã được kích hoạt'
        ]);
    }
    // show voucher
    public function show($id)
    {
        $voucher = Vouchers::withTrashed()->find($id);
        if ($voucher) {
            return response()->json([
                'success' => true,
                'data' => $voucher
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'no data'
        ]);
    }
    // sotf delete voucher
    public function destroy($id)
    {
        $voucher = Vouchers::find($id);
        if ($voucher) {
            $voucher->delete();
            return response()->json([
                'success' => true,
                'data' => $voucher
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'no data'
        ]);
    }
    // xóa vĩnh viễn 1 voucher
    public function forceDelete($id)
    {
        $voucher = Vouchers::withTrashed()->find($id);
        if ($voucher) {
            $voucher->forceDelete();
            return response()->json([
                'success' => true,
                'data' => $voucher
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'no data'
        ]);
    }
    // xóa vĩnh viễn các voucher đã xóa mềm
    public function forceDeleteAll()
    {
        $voucher = Vouchers::onlyTrashed()->get();
        foreach ($voucher as $vc) {
            $vc->forceDelete();
        }
        return response()->json([
            'success' => true,
            'data' => $voucher
        ]);
    }
    // list voucher sotf delete
    public function trashed()
    {
        $voucher = Vouchers::onlyTrashed()->get();
        if ($voucher->all()) {
            return response()->json([
                'success' => true,
                'data' => $voucher
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'no data'
        ]);
    }
}
