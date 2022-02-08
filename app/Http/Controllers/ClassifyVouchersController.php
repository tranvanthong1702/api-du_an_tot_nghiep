<?php

namespace App\Http\Controllers;

use App\Models\Classify_vouchers;
use App\Models\OrderProcess;
use Illuminate\Http\Request;

class ClassifyVouchersController extends Controller
{
    public function run()
    {
        // set phân loại voucher
        $data = [
            'Voucher giảm giá dành cho khách hàng đăng ký mới khách hàng',
            'Voucher giảm giá dành cho khách hàng đã có khách hàng',
            'Voucher miễn phí ship hàng'
        ];
        foreach ($data as $d) {
            $model = new Classify_vouchers();
            $model->name = $d;
            $model->save();
        }
        // set trạng thái đơn hàng
        $data = [
            'Chưa xử lí',
            'Đang xử lí',
            'Chờ giao',
            'Đang giao',
            'Giao thành công',
            'Giao thất bại',
        ];
        foreach ($data as $d) {
            $model = new OrderProcess();
            $model->name = $d;
            $model->save();
        }
        dd('done');
    }
}
