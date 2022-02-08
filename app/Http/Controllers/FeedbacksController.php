<?php

namespace App\Http\Controllers;

use App\Models\Feedbacks;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeedbacksController extends Controller
{
    // list ds
    public function index()
    {
        $feedback = Feedbacks::all();
        if ($feedback->all()) {
            return response()->json([
                'success' => true,
                'data' => $feedback
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'no data'
        ]);
    }
    // filter
    public function filter(Request $request)
    {
        $feedback = new Feedbacks();
        if ($request->point) {
            $feedback = $feedback->where('point', $request->point);
        }
        if ($request->sort == 0) {
            $feedback = $feedback->orderBy('id');
        } elseif ($request->sort == 1) {
            $feedback = $feedback->orderByDesc('id');
        }
        $feedback = $feedback->get();
        if ($feedback->all()) {
            return response()->json([
                'success' => true,
                'data' => $feedback
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'no data'
        ]);
    }
    // analytics
    public function analytics($month, $year)
    {
        // thống kê theo năm
        if ($month == 0) {
            $year = $year;
            for ($i = 1; $i <= 12; $i++) {
                $feedback = DB::table('feedbacks')
                    ->select(DB::raw('FORMAT(AVG(point),1) as TB,month_format'))
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $i)
                    ->groupBy('month_format')
                    ->first();
                // cập nhật ngày và tổng feedback TB tương ứng vào mảng
                $data['Month'][] = $i;
                if ($feedback) {
                    $data['TB'][] = (float)$feedback->TB;
                } else {
                    $data['TB'][] = 0;
                }
            }
        } else {
            // thống kê theo tháng
            $year = $year;
            $month = $month;
                // lấy các feedback
                $feedback = DB::table('feedbacks')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->get();
                $point1=0; $point2=0; $point3=0; $point4=0; $point5=0;
                foreach($feedback as $f){
                    if($f->point==1){
                        $point1++;
                    }elseif($f->point==2){
                        $point2++;
                    }elseif($f->point==3){
                        $point3++;
                    }elseif($f->point==4){
                        $point4++;
                    }elseif($f->point==5){
                        $point5++;
                    }

                }
                // cập nhật tên và số lượng tổng feedback tương ứng vào mảng
                $data['pointName'] = ['1 sao','2 sao','3 sao','4 sao','5 sao'];
                $data['value']=[$point1,$point2,$point3,$point4,$point5];
        }
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    
}
