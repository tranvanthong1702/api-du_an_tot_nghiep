<?php

namespace App\Jobs;

use App\Events\ShopEvent;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class MomoPay implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $status;
    public $order_id;
    public function __construct($data)
    {
        $this->status = $data['status'];
        $this->order_id = $data['order_id'];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payment = Payment::where('order_id', $this->order_id)
            ->where('resultCode', 9000)->first();
        $amount = (int)$payment->amount;
        $partnerCode = 'MOMO9NX020211127';
        $accessKey = '5h0W4SQKf3jzZxvR';
        $secretKey = 'bvgxHxx5lfXXoZApDXqkg3gHCOyVtkiP';
        $orderId = $payment->paymentID;
        $requestId = $payment->requestID;
        $requestType = $this->status;
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

        $response = Http::post('https://test-payment.momo.vn/v2/gateway/api/confirm', $param);
        $data = $response->json();
        $payment = new Payment();
        $payment->order_id = $this->order_id;
        $payment->paymentID = $data['orderId'];
        $payment->requestID = $data['requestId'];
        $payment->amount = $data['amount'];
        $payment->transID = $data['transId'];
        $payment->resultCode = $data['resultCode'];
        $payment->message = $data['message'];
        $payment->requestType = $data['requestType'];
        $payment->save();

        $code_orders = Order::withTrashed()->find($this->order_id)->code_orders;
        if ($this->status == "capture") {
            $message = "Chuyển tiền đơn hàng $code_orders vào ví MOMO thành công";
        } elseif ($this->status == "cancel") {
            $message = "Hoàn tiền đơn hàng $code_orders thành công";
        }
        $data = [
            "message" => $message,
        ];
        $event = new ShopEvent($data);
        event($event);
    }
}
