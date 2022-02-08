<?php

namespace App\Http\Controllers;

use App\Events\ShipperEvent;
use App\Events\ShopEvent;
use App\Http\Requests\OrderRequest;
use App\Jobs\MomoPay;
use App\Jobs\PaymentMomoCommit;
use App\Mail\CreateAccount;
use App\Mail\NotifiOrder;
use App\Mail\OrderNew;
use App\Mail\ShopCancelOrder;
use App\Mail\VerifyOrder;
use App\Models\AddressCustom;
use App\Models\District;
use App\Models\Feedbacks;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Payment;
use App\Models\Province;
use App\Models\User;
use App\Models\Voucher_users;
use App\Models\Vouchers;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Contracts\Role;
use Spatie\Permission\Models\Role as ModelsRole;

class OrderController extends Controller
{
    ################# API order for UI #######################
    // verify email create order
    public function verifyEmail(Request $request)
    {
        // tạo code
        $code = rand(1111, 9999);
        $data = [
            'name' => $request->name,
            'code' => $code
        ];
        Mail::to($request->email)->queue(new VerifyOrder($data));
        return response()->json([
            'success' => true,
            'data' => $code
        ]);
    }
    // payment with momo
    public function paymentWithMomo(Request $request)
    {
        // validate để amount>50000
        if ($request->amount < 50000) {
            return response()->json([
                'success' => false,
                'data' => 'Giá trị thanh toán tối thiểu trên MOMO là  50000'
            ]);
        }
        $amount = (int)$request->amount;
        $partnerCode = 'MOMO9NX020211127';
        $accessKey = '5h0W4SQKf3jzZxvR';
        $secretKey = 'bvgxHxx5lfXXoZApDXqkg3gHCOyVtkiP';
        $orderInfo = 'Thanh toán đơn hàng tại MarkVeget';
        $redirectUrl = 'http://localhost:3000/result';
        $ipnUrl = 'http://localhost:8000/result';
        $orderId = rand(1111, 9999) . 'Mv' . time();
        $requestId = rand(1111, 9999) . 'Mv' . time();
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
        //    chuyển hướng đến trang thanh toán của MOMO
        return redirect($response->json()['payUrl']);
    }
    // list order chưa bị xóa mềm
    public function index(Request $request)
    {
        $orders = Order::all();
        if ($orders->all()) {
            $orders->load('order_details');
            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Chưa có đơn hàng nào '
            ]);
        }
    }

    // thêm mới order
    public function add(OrderRequest $request)
    {
        /**
         *1.	Validate dl
         *2.	Tạo tk cho kh mới nếu là kh hàng mới và chưa từng tạo tk (xét email)
         *3.	Lưu đơn hàng
         *4.	Lưu chi tiết đơn hàng
         *5.	Lưu order_id vào bảng payment nếu có thanh toán online
         *6.	Gửi mail thông báo thông tin tk  có kèm button LOGIN nếu là kh mới
         *7.	Gửi mail thông báo đặt hàng thành công có kèm button HỦY
         *8.	Cập nhật địa chỉ của khách hàng vào bảng address_custom
         *9.    Kiểm tra voucher => nếu chỉ sử dụng một lần thì cần xóa mềm khỏi bảng voucher_users => chỉ áp dụng với kh có tk
         *10.	Trả về thông tin đơn hàng


         */
        // validate để đơn hàng có số sp >0
        if (!$request->products) {
            return response()->json([
                'success' => false,
                'data' => 'Chưa có sp nào trong đơn hàng'
            ]);
        }
        # Xử lí
        # Nếu là khách hàng mới
        if ($request->user_id == null) {
            #1. tạo tk
            $user = User::where('email', $request->customer_email)->first();

            if ($user == null) {
                $user = new User();
                # tạo password
                $number = range(0, 9);
                shuffle($number);
                $upercase = range('A', 'Z');
                shuffle($upercase);
                $lowercase = range('a', 'z');
                shuffle($lowercase);
                $password = [$number[0], $number[1], $number[2], $number[3], $upercase[0], $lowercase[0]];
                shuffle($password);
                $password = implode('', $password);
                # tạo mới user
                $user->password = $password;
                $user->user_name = $request->customer_name;
                $user->email = $request->customer_email;
                $user->save();
                # thêm email vào hàng đợi
                $data = [
                    'name' => $user->user_name,
                    'password' => $password,
                    'url' => 'http://localhost:3000/login'
                ];
                Mail::to($request->customer_email)->send(new CreateAccount($data));
            }
            #2. Lưu đơn hàng
            $order = new Order();
            $order->user_id = $user->id;
            $order->code_orders = time();
            $province = Province::where('provinceID', $request->provinceID)->first()->provinceName;
            $district = District::where('districtID', $request->districtID)->first()->districtName;
            $order->customer_address = $request->customer_address . ', ' . $district . ', Tỉnh/TP ' . $province;
            $order->fill($request->except('user_id', 'customer_address'));
            $order->save();
            $order->load('order_details', 'voucher');
            #3. Lưu chi tiết đơn hàng
            $arr_pro_details = $request->products;
            foreach ($arr_pro_details as $detail) {
                $detail['order_id'] = $order->id;
                $order_detail = new OrderDetail();
                $order_detail->fill($detail);
                $order_detail->save();
            }
            #4. Cập nhật địa chỉ của khách hàng vào bảng address_custom
            AddressCustom::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'provinceID' => $request->provinceID,
                    'districtID' => $request->districtID,
                    'customer_address' => $request->customer_address,
                ]
            );
            #5. Lưu order_id vào bảng payment nếu có thanh toán online
            if ($request->paymentID) {
                Payment::updateOrCreate(
                    ['paymentID' => $request->paymentID],
                    [
                        'order_id' => $order->id,
                        'paymentID' => $request->paymentID,
                        'requestID' => $request->requestID,
                        'transID' => $request->transID,
                        'amount' => $request->amount,
                        'resultCode' => $request->resultCode,
                        'message' => $request->message,
                        'payType' => $request->payType,
                        'orderInfo' => $request->orderInfo,
                    ]
                );
            }
            #6. Gửi mail thông báo đặt hàng thành công
            Mail::to($request->customer_email)->send((new NotifiOrder($order))->afterCommit());
            Mail::to('thonvps09672@fpt.edu.vn')->send((new OrderNew($order))->afterCommit());
            #7. Trả về thông tin đơn hàng
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } else {
            # Nếu là khách hàng đã có tk
            #2. Lưu đơn hàng
            $order = new Order();
            $order->code_orders = time();
            $order->fill($request->except('customer_address'));
            $province = Province::where('provinceID', $request->provinceID)->first()->provinceName;
            $district = District::where('districtID', $request->districtID)->first()->districtName;
            $order->customer_address = $request->customer_address . ', ' . $district . ', Tỉnh/TP ' . $province;
            $order->save();
            $order->load('order_details', 'voucher');
            #3. Lưu chi tiết đơn hàng
            $arr_pro_details = $request->products;
            foreach ($arr_pro_details as $detail) {
                $detail['order_id'] = $order->id;
                $order_detail = new OrderDetail();
                $order_detail->fill($detail);
                $order_detail->save();
            }
            #4. Cập nhật địa chỉ của khách hàng vào bảng address_custom
            AddressCustom::updateOrCreate(
                ['user_id' => $request->user_id],
                [
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'provinceID' => $request->provinceID,
                    'districtID' => $request->districtID,
                    'customer_address' => $request->customer_address,
                ]
            );
            #5. Lưu order_id vào bảng payment nếu có thanh toán online
            if ($request->paymentID) {
                Payment::updateOrCreate(
                    ['paymentID' => $request->paymentID],
                    [
                        'order_id' => $order->id,
                        'paymentID' => $request->paymentID,
                        'requestID' => $request->requestID,
                        'transID' => $request->transId,
                        'amount' => $request->amount,
                        'resultCode' => $request->resultCode,
                        'message' => $request->message,
                        'payType' => $request->payType,
                        'orderInfo' => $request->orderInfo,
                    ]
                );
            }
            #6. Gửi mail thông báo đặt hàng thành công
            Mail::to($request->customer_email)->send((new NotifiOrder($order))->afterCommit());
            Mail::to('thonvps09672@fpt.edu.vn')->send((new OrderNew($order))->afterCommit());
            #7  Xóa những voucher chỉ sử dụng một lần nếu có
            if ($request->voucher_id) {
                $time = Vouchers::find($request->voucher_id)->times;
                if ($time == 1) {
                    Voucher_users::where('user_id', $request->user_id)->where('voucher_id', $request->voucher_id)->delete();
                }
            }
            #8. Trả về thông tin đơn hàng
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        }
    }
    // customer cancel order
    public function cancelOrder(Request $request, $order_id)
    {
        $order = Order::find($order_id);
        if ($order) {
            $order = Order::where('id', $order_id)->whereIn('process_id', [1, 2, 3])->whereNull('shipper_confirm')->first();
            if ($order) {
                $order->update(['process_id' => 6]);
                $order->delete();

                $code_order = $order->code_orders;
                $message = "Khách hàng đã hủy đơn hàng $code_order";
                $data = [
                    "message" => $message,
                ];
                $event = new ShopEvent($data);
                event($event);
                return response()->json([
                    'success' => true,
                    'data' => $order->code_orders
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'data' => 'Đơn hàng đang được giao, bạn không thể xóa đơn hàng này'
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'data' => 'Đơn hàng không tìm thấy'
        ]);
    }

    // chi tiết một đơn hàng
    public function detail($id)
    {
        $order = Order::withTrashed()->find($id);
        if ($order) {
            $order->load('order_details', 'customer', 'voucher', 'shipper', 'process');
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'đơn hàng không tồn tại'
        ]);
    }
    // lấy tất cả đơn hàng chưa bị xóa mềm
    public function getAllOrder()
    {
        $orders = Order::all();
        if ($orders->all()) {
            $orders->load('shipper', 'process', 'order_details');
            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'không có đơn hàng'
        ]);
    }
    // lấy tất cả đơn hàng đã bị xóa mềm
    public function getDeletedAll()
    {
        $orders = Order::onlyTrashed()->get();
        if ($orders->all()) {
            $orders->load('payments', 'shipper', 'process');
            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } else {
            return response()->json([
                'success' => false,
                'data' => 'Chưa có đơn hàng bị xóa trong dữ liệu'
            ]);
        }
    }
    // lấy tổng đơn hàng theo các trạng thái
    public function countOrderProcess()
    {
        $order = DB::table('orders')
            ->select(DB::raw('COUNT(process_id) as count, process_id'))
            ->whereNull('deleted_at')
            ->groupBy('process_id');
        $data = DB::table('order_processes')
            ->select('count', 'name', 'process_id')
            ->joinSub($order, 'op', function ($join) {
                $join->on('order_processes.id', '=', 'op.process_id');
            })->get();

        $count = Order::select(DB::raw('COUNT(*) as count'))->first();
        $count = $count->count;
        foreach ($data as $d) {
            $d->sum = $count;
        }
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    // lấy đơn hàng theo trạng thái xử lí
    public function get_order_process($process_id)
    {
        $model = Order::where('process_id', $process_id)->get();
        if ($model->all()) {
            $model->load('shipper');
            return response()->json([
                'success' => true,
                'data' => $model
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'no data'
        ]);
    }

    // update đơn hàng => chưa xử lí theo id
    public function updateNoProcessId($order_id)
    {
        // chưa validate
        $model = Order::find($order_id);
        if ($model) {
            $model->update(['process_id' => 1]);
            return response()->json([
                'success' => true,
                'data' => $model
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => 'đơn hàng không tồn tại'
        ]);
    }

    // update đơn hàng => chưa xử lí theo mảng id
    public function updateNoProcessArrayId(Request $request)
    {
        //    chưa validate
        if (is_array($request->order_id) && $request->order_id) {
            foreach ($request->order_id as $id) {
                $model = Order::find($id);
                $model->update(['process_id' => 1]);
            }
            return response()->json([
                'success' => true,
                'data' => 'update thành công'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'bạn chưa chọn đơn hàng'
        ]);
    }

    // update đơn hàng => đang xử lí theo id
    public function updateProcessingId(Request $request, $order_id)
    {
        // chưa validate
        $model = Order::find($order_id);
        if ($model) {
            $model->update(['process_id' => 2, 'shop_note' => $request->shop_note]);
            return response()->json([
                'success' => true,
                'data' => $model
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'đơn hàng không tồn tại'
        ]);
    }

    // update đơn hàng => đang xử lí theo mảng id
    public function updateProcessingArrayId(Request $request)
    {
        //    chưa validate
        if (is_array($request->order_id) && $request->order_id) {
            foreach ($request->order_id as $id) {
                $model = Order::find($id);
                $model->update(['process_id' => 2]);
            }
            return response()->json([
                'success' => true,
                'data' => 'update thành công'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'bạn chưa chọn đơn hàng'
        ]);
    }

    // update đơn hàng => chờ giao theo id
    public function updateAwaitDeliveryId(Request $request, $order_id)
    {
        // chưa validate
        $model = Order::find($order_id);
        if ($model) {
            $model->update(['process_id' => 3, 'shop_note' => $request->shop_note]);
            return response()->json([
                'success' => true,
                'data' => $model
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'đơn hàng không tồn tại'
        ]);
    }

    // update đơn hàng => chờ giao theo mảng id
    public function updateAwaitDeliveryArrayId(Request $request)
    {
        //    chưa validate
        if (is_array($request->order_id) && $request->order_id) {
            foreach ($request->order_id as $id) {
                $model = Order::find($id);
                $model->update(['process_id' => 3]);
            }
            return response()->json([
                'success' => true,
                'data' => 'update thành công'
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'bạn chưa chọn đơn hàng'
        ]);
    }
    // lấy danh sách shipper
    public function getRoleShipper()
    {
        $roleName = ModelsRole::where('name', 'shipper')->first();
        if ($roleName) {
            $users = User::role('shipper')->get();
            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'role không tồn tại trong db'
        ]);
    }
    // update đơn hàng => đang giao theo mảng id
    public function updateDeliveringArrayId(Request $request)
    {
        $total = 0;
        foreach ($request->order_id as $id) {
            $total++;
            $model = Order::find($id);
            $model->update(['shipper_id' => $request->shipper_id, 'shipper_confirm' => 0]);
        }
        $message = "Bạn có $total đơn hàng mới";
        $user_id = $request->shipper_id;
        $data = [
            "message" => $message,
            "user_id" => $user_id
        ];
        $event = new ShipperEvent($data);
        event($event);
        return response()->json([
            'success' => true,
            'data' => 'đã gửi yêu cầu xác nhận đơn hàng thành công'
        ]);
    }

    // hủy bàn giao đơn hàng
    public function cancelDeliveringArrayId(Request $request)
    {
        foreach ($request->order_id as $id) {
            $model = Order::find($id);
            $model->where('shipper_confirm', 0)->update(['shipper_id' => null, 'shipper_confirm' => null]);
        }
        return response()->json([
            'success' => true,
            'data' => 'đã hủy bàn giao đơn hàng thành công'
        ]);
    }

    // cập nhật thêm ghi chú cửa hàng vào đơn hàng
    public function updateShopNote(Request $request, $order_id)
    {
        $model = Order::find($order_id);
        if ($model) {
            $model->update(['shop_note' => $request->shop_note]);
            return response()->json([
                'success' => true,
                'data' => $model
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => 'đơn hàng không tồn tại'
        ]);
    }

    // shop cancel đơn hàng theo id
    public function shopCancelOrderId($order_id)
    {
        $model = Order::find($order_id);
        if ($model) {
            $data = [
                'name' => $model->customer_name,
                'code' => $model->code_orders
            ];
            Mail::to($model->customer_email)->send((new ShopCancelOrder($data))->afterCommit());
            if ($model->payments == 1) {
                $data = [
                    'status' => 'cancel',
                    'order_id' => $order_id
                ];
                MomoPay::dispatch($data);
                $model->update(['process_id' => 6]);
                $model->delete();
            }
            return response()->json([
                'success' => true,
                'data' => $model
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => 'đơn hàng không tồn tại'
        ]);
    }
    // shop cancel đơn hàng theo mảng id
    public function shopCancelOrderArrayId(Request $request)
    {
        if ($request->order_id) {
            foreach ($request->order_id as $o) {
                $model = Order::find($o);
                if ($model->payments == 1) {
                    $data = [
                        'status' => 'cancel',
                        'order_id' => $o
                    ];
                    MomoPay::dispatch($data);
                }
                $data = [
                    'name' => $model->customer_name,
                    'code' => $model->code_orders
                ];
                Mail::to($model->customer_email)->send((new ShopCancelOrder($data))->afterCommit());
                $model->update(['process_id' => 6]);
                $model->delete();
            }
            return response()->json([
                'success' => true,
                'data' => 'hủy thành công'
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'chưa có đơn hàng'
        ]);
    }

    // lọc đơn hàng theo trạng thái xử lí
    public function filterOrderProcess(Request $request, $process_id)
    {
        $model = new Order();
        $model = $model->where('process_id', $process_id);
        // lọc theo hình thức thanh toán
        if ($request->payments != null) {
            $model = $model->where('payments', $request->payments);
        }
        // lọc theo nhân viên
        if ($request->shipper_id != null) {
            $model = $model->where('shipper_id', $request->shipper_id);
        }
        // lọc theo trạng thái đã add nhân viên
        if ($request->shipper_confirm != 2) {
            if ($request->shipper_confirm == null) {
                $model = $model->whereNull('shipper_confirm');
            } elseif ($request->shipper_confirm == 0) {
                $model = $model->where('shipper_confirm', 0);
            }
        }
        // lọc theo số đt
        if ($request->customer_phone != null) {
            $model = $model->where('customer_phone', 'like', '%' . $request->customer_phone . '%');
        }

        // lọc theo mã đơn hàng
        if ($request->code_orders != null) {
            $model = $model->where('code_orders', 'like', $request->code_orders);
        }
        $data = $model->get();
        if ($model) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'không tìm thấy kết quả phù hợp'
        ]);
    }

    // lọc đơn hàng theo trạng thái bàn giao
    public function filterOrderShopConfirm(Request $request, $shop_confirm)
    {
        $model = new Order();
        $model = $model->where('shop_confirm', $shop_confirm);
        // lọc theo hình thức thanh toán
        if ($request->payments != null) {
            $model = $model->where('payments', $request->payments);
        }
        // lọc theo nhân viên
        if ($request->shipper_id != null) {
            $model = $model->where('shipper_id', $request->shipper_id);
        }
        // lọc theo trạng thái pass/failed
        if ($request->process_id) {
            $model = $model->where('process_id', $request->process_id);
        }
        // lọc theo trạng thái yêu cầu bàn giao của nhân viên
        if ($request->shipper_confirm != 2) {
            if ($request->shipper_confirm == null) {
                $model = $model->whereNull('shipper_confirm');
            } elseif ($request->shipper_confirm == 0) {
                $model = $model->where('shipper_confirm', 0);
            }
        }
        // lọc theo mã đơn hàng
        if ($request->code_orders != null) {
            $model = $model->where('code_orders', 'like', $request->customer_phone);
        }
        $data = $model->get();
        if ($model) {
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'không tìm thấy kết quả phù hợp'
        ]);
    }

    // tìm kiếm đơn hàng theo phone or code
    public function searchPhoneOrCode(Request $request)
    {
        $order = new Order();
        if ($request->phone) {
            $order = $order->where('customer_phone', 'like', '%' . $request->phone . '%');
        }
        if ($request->code) {
            $order = $order->where('code_orders', 'LIKE', $request->code);
        }
        $order = $order->get();
        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }
    // list đơn hàng theo trạng thái bàn giao
    public function get_order_shop_confirm($shop_confirm_id)
    {
        if ($shop_confirm_id == 1) {
            $model = Order::where('shop_confirm', $shop_confirm_id)->get();
            $model->load('shipper');
            if ($model->all()) {
                return response()->json([
                    'success' => true,
                    'data' => $model
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'data' => 'no data'
                ]);
            }
        } elseif ($shop_confirm_id == 0) {
            $model = Order::where('shipper_confirm', 1)->where(function ($query) {
                $query->whereNull('shop_confirm')->orWhere('shop_confirm', 0);
            })->get();
            if ($model->all()) {
                $model->load('shipper');
                return response()->json([
                    'success' => true,
                    'data' => $model
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'data' => 'no data'
                ]);
            }
        }
    }
    // đếm số đơn hàng theo trạng thái bàn giao
    public function countShopConfirm()
    {
        $shop_confirm = Order::select(DB::raw('COUNT(*) as count'))->where('shop_confirm', 1)->first();
        $shop_confirm = $shop_confirm->count;
        $shop_no_confirm = Order::select(DB::raw('COUNT(*) as count'))->where('shipper_confirm', 1)->where(function ($query) {
            $query->whereNull('shop_confirm')->orWhere('shop_confirm', 0);
        })->first();
        $shop_no_confirm = $shop_no_confirm->count;
        $data = [
            'shop_confirm' => $shop_confirm,
            'shop_no_confirm' => $shop_no_confirm
        ];
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
        dd($shop_confirm);
    }
    // xác nhận bàn giao từ nhân viên
    public function update_shop_confirm(Request $request)
    {
        $total = 0;
        foreach ($request->order_id as $id) {
            $total++;
            Order::find($id)->update(['shop_confirm' => 1, 'time_shop_confirm' => Carbon::now()->toDateTimeString()]);
        }
        $shipper_id = Order::find($request->order_id[0])->shipper_id;
        $message = "Xác nhận $total đơn hàng thành công";
        $user_id = $shipper_id;
        $data = [
            "message" => $message,
            "user_id" => $user_id
        ];
        $event = new ShipperEvent($data);
        event($event);
        return response()->json([
            'success' => true,
            'data' => 'xác nhận bàn giao thành công'
        ]);
    }

    // xóa mềm đơn hàng
    public function deleteOrder(Request $request)
    {
        if ($request->order_id == []) {
            return response()->json([
                'success' => false,
                'data' => 'ko có order để lưu trữ'
            ]);
        }
        foreach ($request->order_id as $id) {
            $order = Order::find($id);
            if ($order->process_id == 5) {
                if ($order->payments == 1) {
                    $data = [
                        'status' => 'capture',
                        'order_id' => $id
                    ];
                    MomoPay::dispatch($data);
                }
                $order->delete();
            } elseif ($order->process_id == 6) {
                if ($order->payments == 1) {
                    $data = [
                        'status' => 'cancel',
                        'order_id' => $id
                    ];
                    MomoPay::dispatch($data);
                }
                $data = [
                    'name' => $order->customer_name,
                    'code' => $order->code_orders
                ];
                Mail::to($order->customer_email)->send((new ShopCancelOrder($data))->afterCommit());
                $order->delete();
            } elseif ($order->process_id == 4) {
                if ($order->payments == 1) {
                    $data = [
                        'status' => 'cancel',
                        'order_id' => $id
                    ];
                    MomoPay::dispatch($data);
                }
                $data = [
                    'name' => $order->customer_name,
                    'code' => $order->code_orders
                ];
                Mail::to($order->customer_email)->send((new ShopCancelOrder($data))->afterCommit());
                $order->update(['process_id' => 6]);
                $order->delete();
            }
        }
        return response()->json([
            'success' => true,
            'data' => 'xóa thành công'
        ]);
    }
    // xóa vĩnh viễn đơn hàng đã lưu trữ theo id
    public function forceDeleteOrderId($order_id)
    {
        $order = Order::withTrashed()->find($order_id);
        if ($order) {
            $order->forceDelete();
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'đơn hàng ko tồn tại'
        ]);
    }
    // xóa vĩnh viễn đơn hàng đã lưu trữ theo mảng id
    public function forceDeleteOrderArrayId($order_id)
    {
        if ($order_id) {
            foreach ($order_id as $o) {
                $order = Order::withTrashed()->where('id', $o)->first();
                $order->forceDelete();
            }
            return response()->json([
                'success' => true,
                'data' => 'xóa thành công'
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'mảng rỗng'
        ]);
    }
    // cập nhật trạng thái cho các đơn hàng tiếp tục xử lí
    public function updateNewProcess(Request $request)
    {
        foreach ($request->order_id as $id) {
            Order::find($id)->update(['shipper_id' => null, 'shipper_confirm' => null, 'shop_confirm' => null, 'time_shop_confirm' => null, 'process_id' => $request->process_id]);
        }
        return response()->json([
            'success' => true,
            'data' => 'cập nhật trạng thái cho mới cho các đơn hàng thành công'
        ]);
    }
    // xuất hóa đơn PDF => chưa được
    public function ExportInvoice($order_id)
    {
        $order = Order::find($order_id);
        if ($order) {
            $order->load('order_details', 'voucher');
            $order = $order->toArray();
            // $pdf = PDF::loadView('PDF.order-invoice', ['order' => $order]);
            $pdf = PDF::loadView('PDF.order-invoice1', ['name' => 'thọ', 'age' => 20]);
            return $pdf->download('abc.pdf');
        }
        return response()->json([
            'success' => false,
            'data' => 'no data'
        ]);
    }
    // backup lại trạng thái đơn hàng theo order_id
    public function backupProcessOrder($order_id)
    {
        $order = Order::where('id', $order_id)->whereNull('shipper_confirm')->whereIn('process_id', [2, 3])->first();
        if ($order) {
            $order->update(['process_id' => $order->process_id - 1]);
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'no data'
        ]);
    }

    ################### API dành cho nhân viên ###############################

    // lấy đơn hàng của shipper chưa xác nhận
    public function shipperOrderNoConfirm($shipper_id)
    {
        $model = Order::where('shipper_id', $shipper_id)->where('shipper_confirm', 0)->get();
        if ($model->all()) {
            return response()->json([
                'success' => true,
                'data' => $model
            ]);
        }
        return response()->json([
            'success' => true,
            'data' => 'no data'
        ]);
    }

    // xác nhận đã nhận đơn hàng theo mảng order_id
    public function updateShipperConfirm(Request $request)
    {
        $user_name = $request->user()->user_name;
        $total = 0;
        foreach ($request->order_id as $id) {
            $total++;
            Order::find($id)->update(['shipper_confirm' => 1, 'process_id' => 4]);
        }
        $message = "Nhân viên $user_name đã nhận $total đơn hàng";
        $data = [
            "message" => $message,
        ];
        $event = new ShopEvent($data);
        event($event);
        return response()->json([
            'success' => true,
            'data' => 'xác nhận thành công'
        ]);
    }
    // list đơn hàng đang giao và chưa hoàn thành bàn giao
    public function shipperDelivering($shipper_id)
    {
        $order = Order::where('shipper_id', $shipper_id)->where('process_id', 4)->where(function ($query) {
            $query->whereNull('shop_confirm')->orWhere('shop_confirm', 0);
        })->get();
        if ($order->all()) {
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'ko có đơn hàng nào đang giao'
        ]);
    }
    // list đơn hàng của nhân viên đã nhận nhưng chưa hoàn thành việc bàn giao
    public function shipperConfirmNoShopCOnfirm($shipper_id)
    {
        $order = Order::where('shipper_id', $shipper_id)->where('shipper_confirm', 1)->where(function ($query) {
            $query->whereNull('shop_confirm')->orWhere('shop_confirm', 0);
        })->get();
        if ($order->all()) {
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'ko có đơn hàng nào đang giao'
        ]);
    }
    // list tổng đơn hàng theo trạng thái
    public function countMixProcess($shipper_id)
    {
        $order_new = Order::select(DB::raw('COUNT(*) as count'))->where('shipper_id', $shipper_id)->where('shipper_confirm', 0)->first();
        $order_new = $order_new->count;
        $order_delivering = Order::select(DB::raw('COUNT(*) as count'))
            ->where('shipper_id', $shipper_id)
            ->where('process_id', 4)
            ->where(function ($query) {
                $query->whereNull('shop_confirm')->orWhere('shop_confirm', 0);
            })->first();
        $order_delivering = $order_delivering->count;
        $order_shop_no_confirm = Order::select(DB::raw('COUNT(*) as count'))
            ->where('shipper_id', $shipper_id)
            ->where('shipper_confirm', 1)
            ->where(function ($query) {
                $query->whereNull('shop_confirm')->orWhere('shop_confirm', 0);
            })->first();
        $order_shop_no_confirm = $order_shop_no_confirm->count;
        $data = [
            'order_new' => $order_new,
            'order_delivering' => $order_delivering,
            'order_shop_no_confirm' => $order_shop_no_confirm
        ];
        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }
    // cập nhật trạng thái thành công cho đơn hàng theo id
    public function updateSuccessOrder($order_id)
    {
        $model = Order::find($order_id)->update(['process_id' => 5]);
        return response()->json([
            'success' => true,
            'data' => $model
        ]);
    }
    // cập nhật trạng thái hủy cho đơn hàng theo id
    public function updateCancelOrder(Request $request, $order_id)
    {
        $model = Order::find($order_id)->update(['process_id' => 6, 'cancel_note' => $request->cancel_note]);
        return response()->json([
            'success' => true,
            'data' => $model
        ]);
    }
    // gửi yêu cầu bàn giao đơn hàng theo mảng order_id
    public function shipperUpdateShopConfirm(Request $request)
    {
        $user_name = $request->user()->user_name;
        $total = 0;
        foreach ($request->order_id as $id) {
            $total++;
            Order::find($id)->update(['shop_confirm' => 0]);
        }
        $message = "Nhân viên $user_name gửi yêu cầu bàn giao $total đơn hàng";
        $data = [
            "message" => $message,
        ];
        $event = new ShopEvent($data);
        event($event);
        return response()->json([
            'success' => true,
            'data' => 'gửi yêu cầu xác nhận đơn hàng thành công'
        ]);
    }

    ###################### API của khách hàng ################

    // ds các đơn hàng đang xử lí
    public function orderCustomerProcessing($custom_id)
    {
        $order = Order::where('user_id', $custom_id)->whereIn('process_id', [1, 2, 3])->get();
        if ($order->all()) {
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'không có đơn hàng nào'
        ]);
    }
    // ds các đơn hàng đang giao
    public function orderCustomerDelivering($custom_id)
    {
        $order = Order::where('user_id', $custom_id)->where('process_id', 4)->get();
        if ($order->all()) {
            $shipper = User::where('id', $custom_id)->first();
            $shipperInfo = $shipper->info_user;
            if ($shipperInfo->all()) {
                foreach ($order as $o) {
                    $o->shipperInfo = $shipperInfo[0];
                }
            }
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'không có đơn hàng nào'
        ]);
    }
    // ds các đơn hàng đã giao
    public function orderCustomerSuccess($custom_id)
    {
        $order = DB::table('orders')->where('user_id', $custom_id)->where('process_id', 5)->get();
        if ($order->all()) {
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        }
        return response()->json([
            'success' => false,
            'data' => 'không có đơn hàng nào'
        ]);
    }
    // list đơn hàng hiển thị mặc định
    public function orderDefault($custom_id)
    {
        $order = Order::where('user_id', $custom_id)->where('process_id', '<', 5)->orderByDesc('process_id')->get();
        if ($order->all()) {
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        } else {
            $order = Order::withTrashed()->where('user_id', $custom_id)->where('process_id', 5)->orderByDesc('time_shop_confirm')->get();
            if ($order->all()) {
                $order->load('feedback');
                foreach ($order as $o) {
                    if (!$o->feedback) {
                        $orderNew[] = $o;
                    }
                }
                return response()->json([
                    'success' => true,
                    'data' => $orderNew
                ]);
            }
        }
    }
    // đánh giá đơn hàng
    public function postFeedback(Request $request, $order_id)
    {
        $feedback = new Feedbacks();
        $feedback->order_id = $order_id;
        $feedback->content = $request->content;
        $feedback->point = $request->point;
        $feedback->day_format = Carbon::now()->day;
        $feedback->month_format = Carbon::now()->month;
        $feedback->save();
        return response()->json([
            'success' => true,
            'data' => $feedback
        ]);
    }
    // list đơn hàng theo trạng thái đánh giá

    public function getOrderStatusFeedback($status, $custom_id)
    {
        $order = Order::withTrashed()->where('user_id', $custom_id)->where('process_id', 5)->orderByDesc('time_shop_confirm')->get();
        if ($order->all()) {
            $order->load('feedback');
            foreach ($order as $o) {
                if ($o->feedback) {
                    $feedback[] = $o;
                } else {
                    $noFeedback[] = $o;
                }
            }
            if ($status == 0) {
                return response()->json([
                    'success' => true,
                    'data' => $noFeedback
                ]);
            } elseif ($status == 1) {
                return response()->json([
                    'success' => true,
                    'data' => $feedback
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'data' => 'không có đơn hàng nào'
        ]);
    }
}
