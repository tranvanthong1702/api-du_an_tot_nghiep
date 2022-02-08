<?php

namespace App\Http\Controllers;

use App\Exports\AnalyticRevenueExport;
use App\Exports\AnalytictQuantityProduct;
use App\Exports\CompareCreateSuccessExport;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AnalyticsController extends Controller
{
    //  thống kê dashboad
    public function analytictDashboad()
    {
       $user=User::count();
       $cate=Category::count();
       $pro=Product::count();
       $orderNew=Order::where('process_id',1)->count();
       $data=[
        'user'=>$user,
        'cate'=>$cate,
        'pro'=>$pro,
        'orderNew'=>$orderNew
       ];
       return response()->json([
        'success' => true,
        'data' => $data
    ]);
    }
    // thống kê doanh thu theo năm - tháng
    public function revenue($month, $year)
    {
        // thống kê theo năm
        if ($month == 0) {
            $year = $year;
            for ($i = 1; $i <= 12; $i++) {
                $orders = Order::withTrashed()->select('time_shop_confirm', 'total_price')->whereYear('time_shop_confirm', $year)->whereMonth('time_shop_confirm', $i)->where('process_id', 5)->get();
                $total = 0;
                foreach ($orders as $value) {
                    $total += $value->total_price;
                }
                // $data[$i] = $total;
                $data['time'][] = $i;
                $data['value'][] = $total;
            }
        } else {
            // thống kê theo tháng
            $year = $year;
            $month = $month;
            // tạo đối tượng thời gian do người dùng gửi lên
            $time = Carbon::create($year, $month);
            // lấy số ngày của tháng trong thời gian người dùng gửi lên
            $count = $time->lastOfMonth()->day;
            // lấy các đơn hàng ứng với các ngày trong tháng
            for ($i = 1; $i <= $count; $i++) {
                // tạo đối tượng thời gian ứng với từng ngày trong tháng 
                $time = Carbon::create($year, $month, $i);
                // lấy các đơn hàng 
                $orders = Order::withTrashed()->select('time_shop_confirm', 'total_price')->whereDate('time_shop_confirm', $time)->where('process_id', 5)->get();
                // tính tổng giá trị đơn hàng
                $total = 0;
                foreach ($orders as $value) {
                    $total += $value->total_price;
                }
                // cập nhật ngày và tổng giá trị đơn hàng tương ứng vào mảng
                $data['time'][] = $i;
                $data['value'][] = $total;
            }
        }
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    // thống kê so sánh tổng số đơn hàng tạo mới và số đơn hàng hoàn thành
    public function compareCreateSuccess($month, $year)
    {
        // thống kê theo năm
        if ($month == 0) {
            $year = $year;
            for ($i = 1; $i <= 12; $i++) {
                // lấy tổng số đơn hàng tạo mới
                $create = DB::table('orders')->select(DB::raw('COUNT(*) as total'))->whereYear('created_at', $year)->whereMonth('created_at', $i)->first();
                // lấy tổng số đơn hàng hoàn thành đã bàn giao
                $success = DB::table('orders')->select(DB::raw('COUNT(*) as total'))->whereYear('time_shop_confirm', $year)->whereMonth('time_shop_confirm', $i)->where('process_id', 5)->first();
                // cập nhật vào mảng data
                $dl = [
                    'date' => $i,
                    'create' => $create->total,
                    'success' => $success->total
                ];
                $data[] = $dl;
            }
        } else {
            // thống kê theo tháng
            $year = $year;
            $month = $month; // nếu tháng <10 phải thêm số 0 phía trước
            // tạo đối tượng thời gian do người dùng gửi lên
            $time = Carbon::create($year, $month);
            // lấy số ngày của tháng trong thời gian người dùng gửi lên
            $count = $time->lastOfMonth()->day;
            for ($i = 1; $i <= $count; $i++) {
                // tạo đối tượng thời gian ứng với từng ngày trong tháng 
                $time = Carbon::create($year, $month, $i);
                // lấy tổng các đơn hàng được tạo mới
                $create = DB::table('orders')->select(DB::raw('COUNT(*) as total'))->whereDate('created_at', $time)->first();
                // lấy tổng số đơn hàng hoàn thành đã bàn giao
                $success = DB::table('orders')->select(DB::raw('COUNT(*) as total'))->whereDate('time_shop_confirm', $time)->where('process_id', 5)->first();
                // cập nhật vào mảng data
                $dl = [
                    'date' => $i,
                    'create' => $create->total,
                    'success' => $success->total
                ];
                $data[] = $dl;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // thống kê số lượng sp đã bán
    public function quantityProduct(Request $request)
    {
        $day = $request->day;
        $month = $request->month;
        $year = $request->year;
        $time = Carbon::create($day)->toDateString();
        if ($day) {
            $model = DB::table('order_details')
                ->join('orders', function ($join) use ($time) {
                    $join->on('orders.id', '=', 'order_details.order_id')
                        ->whereDate('orders.time_shop_confirm', $time)
                        ->where('process_id', 5);
                })
                ->select(DB::raw('SUM(quantity) as sum, product_id,standard_name'))
                ->groupBy('product_id', 'standard_name')
                ->get();
            if ($model->all()) {
                foreach ($model as $m) {
                    $data['name'][] = $m->standard_name;
                    $data['value'][] = $m->sum;
                }
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);

                //  foreach ($model as $m) {
                //     $dl = [
                //         'name' => $m->standard_name,
                //         'value' => $m->sum
                //     ];
                //     $data[] = $dl;
                // }
                // return Excel::download(new AnalyticRevenueExport($data), 'thong-ke-doanh-thu.xlsx');

            }
        } elseif ($month && $year) {
            $model = DB::table('order_details')
                ->join('orders', function ($join) use ($month, $year) {
                    $join->on('orders.id', '=', 'order_details.order_id')
                        ->whereYear('time_shop_confirm', $year)
                        ->whereMonth('time_shop_confirm', $month)
                        ->where('process_id', 5);
                })
                ->select(DB::raw('SUM(quantity) as sum, product_id,standard_name'))
                ->groupBy('product_id', 'standard_name')
                ->get();
            if ($model->all()) {
                foreach ($model as $m) {
                    $data['name'][] = $m->standard_name;
                    $data['value'][] = $m->sum;
                }
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }
        } elseif ($year) {
            $model = DB::table('order_details')
                ->join('orders', function ($join) use ($year) {
                    $join->on('orders.id', '=', 'order_details.order_id')
                        ->whereYear('time_shop_confirm', $year)
                        ->where('process_id', 5);
                })
                ->select(DB::raw('SUM(quantity) as sum, product_id,standard_name'))
                ->groupBy('product_id', 'standard_name')
                ->get();
            if ($model->all()) {
                foreach ($model as $m) {
                    $data['name'][] = $m->standard_name;
                    $data['value'][] = $m->sum;
                }
                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'data' => 'no data'
        ]);
    }
    // API export số lượng sản phẩm đã bán
    public function ExportQuantityProduct(Request $request)
    {
        $day = $request->day;
        $month = $request->month;
        $year = $request->year;
        $time = Carbon::create($day)->toDateString();
        if ($day) {
            $model = DB::table('order_details')
                ->join('orders', function ($join) use ($time) {
                    $join->on('orders.id', '=', 'order_details.order_id')
                        ->whereDate('orders.time_shop_confirm', $time)
                        ->where('process_id', 5);
                })
                ->select(DB::raw('SUM(quantity) as sum, product_id,standard_name'))
                ->groupBy('product_id', 'standard_name')
                ->get();
            if ($model->all()) {
                foreach ($model as $m) {
                    $dl = [
                        'name' => $m->standard_name,
                        'value' => $m->sum
                    ];
                    $data[] = $dl;
                }
                return Excel::download(new AnalytictQuantityProduct($data), "thong-ke-so-luong-$day.xlsx");
            } else {
                return response()->json([
                    'success' => false,
                    'data' => 'no data'
                ]);
            }
        } elseif ($month && $year) {
            $model = DB::table('order_details')
                ->join('orders', function ($join) use ($month, $year) {
                    $join->on('orders.id', '=', 'order_details.order_id')
                        ->whereYear('time_shop_confirm', $year)
                        ->whereMonth('time_shop_confirm', $month)
                        ->where('process_id', 5);
                })
                ->select(DB::raw('SUM(quantity) as sum, product_id,standard_name'))
                ->groupBy('product_id', 'standard_name')
                ->get();
            if ($model->all()) {
                foreach ($model as $m) {
                    $dl = [
                        'name' => $m->standard_name,
                        'value' => $m->sum
                    ];
                    $data[] = $dl;
                }
                return Excel::download(new AnalytictQuantityProduct($data), "thong-ke-so-luong-$month-$year.xlsx");
            } else {
                return response()->json([
                    'success' => false,
                    'data' => 'no data'
                ]);
            }
        } elseif ($year) {
            $model = DB::table('order_details')
                ->join('orders', function ($join) use ($year) {
                    $join->on('orders.id', '=', 'order_details.order_id')
                        ->whereYear('time_shop_confirm', $year)
                        ->where('process_id', 5);
                })
                ->select(DB::raw('SUM(quantity) as sum, product_id,standard_name'))
                ->groupBy('product_id', 'standard_name')
                ->get();
            if ($model->all()) {
                foreach ($model as $m) {
                    $dl = [
                        'name' => $m->standard_name,
                        'value' => $m->sum
                    ];
                    $data[] = $dl;
                }
                return Excel::download(new AnalytictQuantityProduct($data), "thong-ke-so-luong-$year.xlsx");
            } else {
                return response()->json([
                    'success' => false,
                    'data' => 'no data'
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'data' => 'dl không hợp lệ'
        ]);
    }

    // API export doanh thu
    public function ExportOrderRevenue($month, $year)
    {
        // thống kê theo năm
        if ($month == 0) {
            $year = $year;
            for ($i = 1; $i <= 12; $i++) {
                $orders = Order::withTrashed()->select('time_shop_confirm', 'total_price')->whereYear('time_shop_confirm', $year)->whereMonth('time_shop_confirm', $i)->where('process_id', 5)->get();
                $total = 0;
                foreach ($orders as $value) {
                    $total += $value->total_price;
                }
                $time = Carbon::create($year, $i)->format('m-Y');
                $dl = [
                    'time' => $time,
                    'value' => $total
                ];
                $data[] = $dl;
            }
            return Excel::download(new AnalyticRevenueExport($data), "doanh-thu-năm-$year.xlsx");
        } else {
            // thống kê theo tháng
            $year = $year;
            $month = $month;
            // tạo đối tượng thời gian do người dùng gửi lên
            $time = Carbon::create($year, $month);
            // lấy số ngày của tháng trong thời gian người dùng gửi lên
            $count = $time->lastOfMonth()->day;
            // lấy các đơn hàng ứng với các ngày trong tháng
            for ($i = 1; $i <= $count; $i++) {
                // tạo đối tượng thời gian ứng với từng ngày trong tháng 
                $time = Carbon::create($year, $month, $i);
                // lấy các đơn hàng 
                $orders = Order::withTrashed()->select('time_shop_confirm', 'total_price')->whereDate('time_shop_confirm', $time)->where('process_id', 5)->get();
                // tính tổng giá trị đơn hàng
                $total = 0;
                foreach ($orders as $value) {
                    $total += $value->total_price;
                }
                // cập nhật ngày và tổng giá trị đơn hàng tương ứng vào mảng
                $dl = [
                    'time' => $time,
                    'value' => $total
                ];
                $data[] = $dl;
            }
        }
        return Excel::download(new AnalyticRevenueExport($data), "doanh-thu-$month-$year.xlsx");
    }
    // EXPORT số lượng đơn hàng tạo mới và hoàn thành
    public function ExportCompareCreateSuccess($month, $year)
    {
        // thống kê theo năm
        if ($month == 0) {
            $year = $year;
            for ($i = 1; $i <= 12; $i++) {
                // lấy tổng số đơn hàng tạo mới
                $create = DB::table('orders')->select(DB::raw('COUNT(*) as total'))->whereYear('created_at', $year)->whereMonth('created_at', $i)->first();
                // lấy tổng số đơn hàng hoàn thành đã bàn giao
                $success = DB::table('orders')->select(DB::raw('COUNT(*) as total'))->whereYear('time_shop_confirm', $year)->whereMonth('time_shop_confirm', $i)->where('process_id', 5)->first();
                // tạo thời gian trả về
                $time = Carbon::create($year, $i)->format('m-Y');
                // cập nhật vào mảng data
                $dl = [
                    'date' => $time,
                    'create' => $create->total,
                    'success' => $success->total
                ];
                $data[] = $dl;
            }
        } else {
            // thống kê theo tháng
            $year = $year;
            $month = $month; // nếu tháng <10 phải thêm số 0 phía trước
            // tạo đối tượng thời gian do người dùng gửi lên
            $time = Carbon::create($year, $month);
            // lấy số ngày của tháng trong thời gian người dùng gửi lên
            $count = $time->lastOfMonth()->day;
            for ($i = 1; $i <= $count; $i++) {
                // tạo đối tượng thời gian ứng với từng ngày trong tháng 
                $time = Carbon::create($year, $month, $i);
                // lấy tổng các đơn hàng được tạo mới
                $create = DB::table('orders')->select(DB::raw('COUNT(*) as total'))->whereDate('created_at', $time)->first();
                // lấy tổng số đơn hàng hoàn thành đã bàn giao
                $success = DB::table('orders')->select(DB::raw('COUNT(*) as total'))->whereDate('time_shop_confirm', $time)->where('process_id', 5)->first();
                // tạo thời gian trả về
                $time = Carbon::create($year, $month, $i)->format('d-m-Y');
                // cập nhật vào mảng data
                $dl = [
                    'date' => $time,
                    'create' => $create->total,
                    'success' => $success->total
                ];
                $data[] = $dl;
            }
        }
        return Excel::download(new CompareCreateSuccessExport($data), 'so-sanh-don-hang-tao-moi-hoan-thanh.xlsx');
    }
}
