<?php

namespace App\Jobs;

use App\Events\ShopEvent;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TestEvent implements ShouldQueue
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
        $code_orders = Order::find($this->order_id)->code_orders;
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
        echo $this->order_id;
    }
}
