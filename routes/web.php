<?php

use App\Events\ShopEvent;
use App\Jobs\Demo;
use App\Jobs\MomoPay;
use App\Jobs\PaymentMomoCommit;
use App\Jobs\Test;
use App\Jobs\TestEvent;
use App\Jobs\TestRespose;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('result', function (Request $request) {
    dd($request->all());
});
Route::get('Payment', function () {
    $amount = 50000;
    $partnerCode = 'MOMO9NX020211127';
    $accessKey = '5h0W4SQKf3jzZxvR';
    $secretKey = 'bvgxHxx5lfXXoZApDXqkg3gHCOyVtkiP';
    $orderInfo = 'Thanh toán đơn hàng tại MarkVeget';
    $redirectUrl = 'http://localhost:8000/result';
    $ipnUrl = 'http://localhost:8000/result';
    $orderId = rand(1111,9999).'Mv'. time();
    $requestId = rand(1111,9999).'Mv'. time();
    $requestType = "captureWallet";
    $extraData = "";
    $rawSignature = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
    $signature = hash_hmac("sha256", $rawSignature, $secretKey);

    $param = [
        'partnerCode' => $partnerCode,
        'requestId' => $requestId,
        'amount' => $amount,
        'orderId' => $orderId,
        'orderInfo' => $orderInfo,
        'redirectUrl' => $redirectUrl,
        'ipnUrl' => $ipnUrl,
        'requestType' => $requestType,
        'extraData' => $extraData,
        'lang' =>  'vi',
        'autoCapture' => false, // không tự động chuyển tiền vào ví đối tác ngay
        'signature' => $signature
    ];

    $response = Http::post('https://test-payment.momo.vn/v2/gateway/api/create', $param);
    // dd($response->json());
    //    chuyển hướng đến trang thanh toán của MOMO
    return redirect($response->json()['payUrl']);
});
Route::get('commit', function () {
    $payment = Payment::where('order_id', 25)
        ->where('resultCode', 9000)->first();
    dd($payment);
    $amount = 50000;
    $partnerCode = 'MOMO9NX020211127';
    $accessKey = '5h0W4SQKf3jzZxvR';
    $secretKey = 'bvgxHxx5lfXXoZApDXqkg3gHCOyVtkiP';
    // $orderInfo = 'test payment with momo';
    // $redirectUrl = 'http://localhost:8000/result';
    // $ipnUrl = 'http://localhost:8000/result';
    $orderId = '1639828103';
    $requestId = '1639828103';
    $requestType = "cancel";
    $extraData = "";
    $description = "";


    $rawSignature = "accessKey=" . $accessKey . "&amount=" . $amount . '&description=' . $description .  "&orderId=" . $orderId . "&partnerCode=" . $partnerCode . "&requestId=" . $requestId . "&requestType=" . $requestType;
    // accessKey=$accessKey&amount=$amount&description=$description&orderId=$orderId&partnerCode=$partnerCode&requestId=$requestId&requestType=$requestType;
    $signature = HASH_HMAC("sha256", $rawSignature, $secretKey);
    $param = [
        'partnerCode' => $partnerCode,
        'requestId' => $requestId,
        'orderId' => $orderId,
        'requestType' => $requestType,
        'lang' =>  'vi',
        'amount' => $amount,
        'description' => $description,
        'signature' => $signature
    ];

    $response = http::post('https://test-payment.momo.vn/v2/gateway/api/confirm', $param);
    dd($response->json());
});
Route::get('test-commit', function () {
    $data = [
        'status' => 'capture',
        'order_id' => 46
    ];
    TestEvent::dispatch($data);
    // MomoPay::dispatch($data);
    // TestRespose::dispatch($data);
    // echo ('done');
    // $payment = Payment::where('order_id', 45)
    //     ->where('resultCode', 9000)->first();
    // $amount = (int)$payment->amount;
    // $partnerCode = 'MOMO9NX020211127';
    // $accessKey = '5h0W4SQKf3jzZxvR';
    // $secretKey = 'bvgxHxx5lfXXoZApDXqkg3gHCOyVtkiP';
    // $orderId = $payment->paymentID;
    // $requestId = $payment->requestID;
    // $requestType = "capture";
    // $description = "";
    // $rawSignature = "accessKey=" . $accessKey . "&amount=" . $amount . '&description=' . $description .  "&orderId=" . $orderId . "&partnerCode=" . $partnerCode . "&requestId=" . $requestId . "&requestType=" . $requestType;
    // // accessKey=$accessKey&amount=$amount&description=$description&orderId=$orderId&partnerCode=$partnerCode&requestId=$requestId&requestType=$requestType;
    // $signature = HASH_HMAC("sha256", $rawSignature, $secretKey);
    // $param = [
    //     'partnerCode' => $partnerCode,
    //     'requestId' => $requestId,
    //     'orderId' => $orderId,
    //     'requestType' => $requestType,
    //     'lang' =>  'vi',
    //     'amount' => $amount,
    //     'description' => $description,
    //     'signature' => $signature
    // ];

    // $response = Http::post('https://test-payment.momo.vn/v2/gateway/api/confirm', $param);
    // $data = $response->json();
    // // /dd($data);
    // dd($data['orderId'],$data['requestId'],$data['amount'],$data['transId'],$data['resultCode'],$data['message'],$data['requestType']);

    // // $payment = new Payment();
    // // $payment->order_id = 37;
    // // $payment->paymentID = "1639873415";
    // // $payment->requestID = "1639873415";
    // // $payment->amount = 50000;
    // // $payment->transID = 2627326031;
    // // $payment->resultCode = 0;
    // // $payment->message = "Giao dịch thành công.";
    // // $payment->requestType = "capture";
    // // $payment->save();
    // echo ('done');
    // // // $code_orders = Order::find(34)->code_orders;
    // // // if ($this->status == "capture") {
    // // //     $message = "Chuyển tiền đơn hàng $code_orders vào ví MOMO thành công";
    // // // } elseif ($this->status == "cancel") {
    // // //     $message = "Hoàn tiền đơn hàng $code_orders thành công";
    // // // }
    // // $data = [
    // //     "message" => '$message',
    // // ];
    // // $event = new ShopEvent($data);
    // // event($event);
    // // dd('done');
});
