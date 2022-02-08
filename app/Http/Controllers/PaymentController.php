<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic\Payments;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::all();
        if ($payments->all()) {
            $payments->load('order');
            return response()->json([
                'success' => true,
                'data' => $payments
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'Chưa có hóa đơn nào trong dữ liệu'
        ]);
    }
    // chi tiết payment
    public function detailPayment($payment_id)
    {
        $payment = Payment::find($payment_id);
        if ($payment) {
            $payment->load('order');
            return response()->json([
                'success' => true,
                'data' => $payment
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'payment ko tồn tại'
        ]);
    }
    // xóa payment theo id
    public function deletePaymentId($payment_id)
    {
        $payment = Payment::find($payment_id);
        if ($payment) {
            $payment->delete();
            return response()->json([
                'success' => true,
                'data' => $payment
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'payment ko tồn tại'
        ]);
    }
    // xóa theo mảng id
    public function deletePaymentArrayId(Request $request)
    {
        if($request->payment_id){
            foreach($request->payment_id as $p){
                $payment = Payment::find($p);   
                $payment->delete();
            }
            return response()->json([
                'success' => true,
                'data' => 'xóa payment thành công'
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'mảng payment rỗng'
        ]);
    }
}
