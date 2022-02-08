<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClassifyVouchersController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FeedbacksController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\SlideController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\VouchersController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// API setup data default

// set data cho bảng classify_voucher
Route::get('setup_value_default', [ClassifyVouchersController::class, 'run']);
Route::get('setup_transport', [TransportController::class, 'resetTransport']);
// setup role và permission mặc định
Route::get('setup_role_permission', [PermissionController::class, 'run']);

// các API của admin
Route::middleware(['auth:sanctum', 'role:Admin|manager order|manager content|shipper'])->prefix('admin')->group(function () {
    Route::get('dashboad/analyticts',[AnalyticsController::class,'analytictDashboad']);
    Route::middleware(['role:Admin|manager content'])->group(function () {
        Route::prefix('product')->group(function () {
            // danh sách tất cả các sp chưa bị xóa mềm
            Route::get('', [ProductController::class, 'index']);
            // filter
            Route::get('filter/admin', [ProductController::class, 'filterAdmin']);
            // thêm mới 1 sp
            Route::post('store', [ProductController::class, 'store']);
            // cập nhật 1 sp
            Route::put('update/{id}', [ProductController::class, 'update']);
            // xóa mềm 1 sp
            Route::delete('delete/{id}', [ProductController::class, 'delete']);
            // danh sách tất cả các sp đã bị xóa mềm
            Route::get('trashed', [ProductController::class, 'trashed']);
            // backup 1 sp đã bị xóa mềm
            Route::options('backup-one/{id}', [ProductController::class, 'backupOne']);
            // backup tất cả các sp đã bị xóa mềm
            Route::options('backup-all', [ProductController::class, 'backupAll']);
            // chi tiết 1 sp
            Route::get('detail/{id}', [ProductController::class, 'show']);
            //list comment thuộc sp
            Route::get('comment/{pro_id}', [ProductController::class, 'list_comments']);
            Route::middleware(['permission:delete product'])->group(function () {
                // xóa vĩnh viễn 1 sp
                Route::delete('force-delete/{id}', [ProductController::class, 'forceDelete']);
                // xóa vĩnh viễn tất cả các sp đã bị xóa mềm
                Route::options('force-delete/all', [ProductController::class, 'forceDeleteAll']);
            });
        });
        Route::prefix('category')->group(function () {
            // lấy danh sách dm
            Route::get('', [CategoryController::class, 'index']);
            // thêm mới dm
            Route::post('store', [CategoryController::class, 'store']);
            // cập nhật dm
            Route::put('update/{id}', [CategoryController::class, 'update']);
            // xóa mềm 1 dm
            Route::delete('delete/{id}', [CategoryController::class, 'delete']);
            // danh sách tất cả các dm đã bị xóa mềm
            Route::get('trashed', [CategoryController::class, 'trashed']);
            // backup 1 dm đã bị xóa mềm
            Route::options('backup-one/{id}', [CategoryController::class, 'backupOne']);
            // backup tất cả các dm đã bị xóa mềm
            Route::options('backup-all', [CategoryController::class, 'backupAll']);
            // chi tiết 1 dm: ok
            Route::get('detail/{id}', [CategoryController::class, 'show']);
            // list các sp trong dm
            Route::get('product/{cate_id}', [CategoryController::class, 'list_pro']);
            Route::middleware(['permission:delete category'])->group(function () {
                // xóa vĩnh viễn
                Route::delete('force-delete/{id}', [CategoryController::class, 'forceDelete']);
                // xóa vĩnh viễn tất cả các dm đã bị xóa mềm
                Route::options('force-delete/all', [CategoryController::class, 'forceDeleteAll']);
            });
        });

        Route::prefix('slide')->group(function () {
            Route::get('', [SlideController::class, 'index']);
            Route::post('store', [SlideController::class, 'store']);
            Route::put('update/{id}', [SlideController::class, 'update']);
            Route::get('detail/{id}', [SlideController::class, 'show']);
            Route::middleware(['permission:delete slide'])->group(function () {
                Route::delete('delete/{id}', [SlideController::class, 'destroy']);
            });
        });

        Route::prefix('blog')->group(function () {
            Route::get('', [BlogController::class, 'index']);
            Route::post('store', [BlogController::class, 'store']);
            Route::put('update/{id}', [BlogController::class, 'update']);
            //xoa men
            Route::delete('delete/{id}', [BlogController::class, 'destroy']);
            //list da bi xoa mem
            Route::get('trashed', [BlogController::class, 'trashed']);
            //restor 1
            Route::options('backup-one/{id}', [BlogController::class, 'backupOne']);
            //restor all
            Route::options('backup-all', [BlogController::class, 'backupAll']);
            Route::get('detail/{id}', [BlogController::class, 'show']);
            Route::middleware(['permission:delete blog'])->group(function () {
                //xoa vv 1
                Route::delete('force-delete/{id}', [BlogController::class, 'forceDelete']);
                //xoa vv all
                Route::options('force-delete/all', [BlogController::class, 'forceDeleteAll']);
            });
        });
        Route::prefix('comment')->group(function () {
            Route::get('', [CommentController::class, 'index']);

            Route::post('store', [CommentController::class, 'store']);

            Route::put('update/{id}', [CommentController::class, 'update']);
            //xoa men
            Route::delete('delete/{id}', [CommentController::class, 'delete']);

            //list da bi xoa mem
            Route::get('trashed', [CommentController::class, 'trashed']);
            //restor 1
            Route::options('backup-one/{id}', [CommentController::class, 'backupOne']);
            //restor all
            Route::options('backup-all', [CommentController::class, 'backupAll']);

            Route::get('detail/{id}', [CommentController::class, 'show']);
            Route::middleware(['permission:delete comment'])->group(function () {
                //xoa vv 1
                Route::delete('force-delete/{id}', [CommentController::class, 'forceDelete']);
                //xoa vv all
                Route::options('force-delete/all', [CommentController::class, 'forceDeleteAll']);
            });
        });
    });
    Route::middleware(['role:Admin|manager order'])->group(function () {
        Route::prefix('order')->group(function () {
            // lấy tất cả đơn hàng chưa bị xóa mềm
            Route::get('process/9', [OrderController::class, 'getAllOrder']);
            // lấy tất cả đơn hàng đã bị xóa mềm
            Route::get('deleted/all', [OrderController::class, 'getDeletedAll']);
            // lấy tổng đơn hàng theo trạng thái
            Route::get('count-process', [OrderController::class, 'countOrderProcess']);
            // list đơn hàng theo trạng thái
            Route::get('process/{process_id}', [OrderController::class, 'get_order_process']);
            // lọc đơn hàng theo trạng thái xử lí trong tab hiện tại
            Route::get('filter/process/{process_id}', [OrderController::class, 'filterOrderProcess']);
            // lọc đơn hàng theo trạng thái bàn giao trong tab hiện tại
            Route::get('filter/shop-confirm/{shop_confirm}', [OrderController::class, 'filterOrderShopConfirm']);
            // tìm kiếm đơn hàng theo sđt hoặc mã đh (tất cả đơn hàng chưa được lưu trữ)
            Route::get('search/phone/code', [OrderController::class, 'searchPhoneOrCode']);
            // update đơn hàng => chưa xử lí theo id
            Route::put('update/no_process/id/{order_id}', [OrderController::class, 'updateNoProcessId']);
            // update đơn hàng => chưa xử lí theo mảng id
            Route::put('update/no_process/array_id', [OrderController::class, 'updateNoProcessArrayId']);
            // update đơn hàng => đang xử lí theo id
            Route::put('update/processing/id/{order_id}', [OrderController::class, 'updateProcessingId']);
            // update đơn hàng => đang xử lí theo mảng id
            Route::put('update/processing/array_id', [OrderController::class, 'updateProcessingArrayId']);
            // update đơn hàng => chờ giao theo id
            Route::put('update/await-delivery/id/{order_id}', [OrderController::class, 'updateAwaitDeliveryId']);
            // update đơn hàng => chờ giao theo mảng id
            Route::put('update/await-delivery/array_id', [OrderController::class, 'updateAwaitDeliveryArrayId']);
            //  update đơn hàng => đang giao theo mảng id
            Route::put('update/delivering/array_id', [OrderController::class, 'updateDeliveringArrayId']);
            // lấy danh sách shipper
            Route::get('role/shipper', [OrderController::class, 'getRoleShipper']);
            // hủy bàn giao đơn hàng theo mảng id
            Route::put('update/cancel-delivering/array_id', [OrderController::class, 'cancelDeliveringArrayId']);
            // cập nhật ghi chú của cửa hàng cho đơn hàng
            Route::put('update/shop-note/{order_id}', [OrderController::class, 'updateShopNote']);
            // shop hủy đơn hàng theo id
            Route::delete('delete/shop-cancel/id/{order_id}', [OrderController::class, 'shopCancelOrderId']);
            // shop hủy đơn hàng theo mảng id
            Route::delete('delete/shop-cancel/array_id', [OrderController::class, 'shopCancelOrderArrayId']);
            // list đơn hàng theo trạng thái bàn giao
            Route::get('shop_confirm/{shop_confirm_id}', [OrderController::class, 'get_order_shop_confirm']);
            // lấy tổng đơn hàng theo trạng thái bàn giao
            Route::get(('count-shop-confirm'), [OrderController::class, 'countShopConfirm']);
            // xác nhận bàn giao từ nhân viên theo mảng order_id
            Route::put('update/shop_confirm', [OrderController::class, 'update_shop_confirm']);
            // xóa mềm các đơn hàng theo mảng order_id 
            Route::delete('delete/array_id', [OrderController::class, 'deleteOrder']);
            // xóa vĩnh viễn một đơn hàng theo id
            Route::delete('force-delete/id/{order_id}', [OrderController::class, 'forceDeleteOrderId']);
            // xóa vĩnh viễn một đơn hàng theo mảng id
            Route::delete('force-delete/array_id', [OrderController::class, 'forceDeleteOrderArrayId']);
            // cập nhật trạng thái cho những đơn hàng tiếp tục xử lí
            Route::put('update/new-process/array_id', [OrderController::class, 'updateNewProcess']);
            // xuất hóa đơn PDF
            Route::get('export/invoice/{order_id}', [OrderController::class, 'ExportInvoice']);
            // back lại trạng thái theo order_id
            Route::put('backup/process/{order_id}', [OrderController::class, 'backupProcessOrder']);



            // list order chưa bị xóa mềm
            Route::get('all', [OrderController::class, 'index']);
            // chi tiết một đơn hàng
            Route::get('{id}', [OrderController::class, 'detail']);
        });

        Route::prefix('payment')->group(function () {
            Route::get('all', [PaymentController::class, 'index']);
            Route::get('detail/{payment_id}', [PaymentController::class, 'detailPayment']);
            // xóa theo id
            Route::delete('delete/id/{payment_id}', [PaymentController::class, 'deletePaymentId']);
            // xóa theo mảng id
            Route::delete('delete/array_id/', [PaymentController::class, 'deletePaymentArrayId']);
        });
    });
    Route::middleware(['role:Admin|shipper'])->group(function () {
        Route::prefix('order')->group(function () {
            ############### API dành cho nhân viên shiiper #################
            // 1. list đơn hàng của nhân viên theo trạng thái chưa xác nhận
            Route::get('shipper/no-confirm/{shipper_id}', [OrderController::class, 'shipperOrderNoConfirm']);
            // xác nhận đã nhận đơn hàng theo mảng order_id
            Route::put('update/shipper_confirm/array_id', [OrderController::class, 'updateShipperConfirm']);
            // 2. list đơn hàng nhân viên có trạng thái đang giao và chưa hoàn thành bàn giao
            Route::get('shipper/delivering/no-shop-confirm/{shipper_id}', [OrderController::class, 'shipperDelivering']);
            // 3. list đơn hàng của nhân viên đã nhận nhưng chưa hoàn thành việc bàn giao
            Route::get('shipper/shipper-confirm/no-shop-confirm/{shipper_id}', [OrderController::class, 'shipperConfirmNoShopCOnfirm']);
            // tổng đơn hàng theo các trạng thái của shipper
            Route::get('count-mix-process/{shipper_id}', [OrderController::class, 'countMixProcess']);
            // cập nhật trạng thái hoàn thành cho đơn hàng theo id
            Route::put('update/success-order/{order_id}', [OrderController::class, 'updateSuccessOrder']);
            // cập nhật trạng thái hủy cho đơn hàng theo id
            Route::put('update/cancel-order/{order_id}', [OrderController::class, 'updateCancelOrder']);
            // gửi yêu cầu bàn giao đơn hàng theo mảng order_id
            Route::put('shipper-update/shop_confirm', [OrderController::class, 'shipperUpdateShopConfirm']);
            // chi tiết đơn hàng
            Route::get('{id}', [OrderController::class, 'detail']);
        });
    });
    Route::middleware(['role:Admin'])->group(function () {
        //bảng voucher:
        Route::prefix('voucher')->group(function () {
            // lấy list classify_vouchers
            Route::get('classify_vouchers/all', [VouchersController::class, 'list_Classify_vouchers']);
            // active planning
            Route::post('planning/{id}', [VouchersController::class, 'planning']);
            // unactive planning
            Route::post('no-planning/{id}', [VouchersController::class, 'noPlanning']);
            //danh sach voucher chưa active
            Route::get('no-active', [VouchersController::class, 'NoActive']);
            //danh sach voucher đã active
            Route::get('active', [VouchersController::class, 'Active']);
            //them moi voucher
            Route::post('store', [VouchersController::class, 'store']);
            //sua  voucher
            Route::put('update/{id}', [VouchersController::class, 'update']);
            //xoa mem
            Route::delete('delete/{id}', [VouchersController::class, 'destroy']);
            //list voucher đã lưu trữ
            Route::get('trashed', [VouchersController::class, 'trashed']);
            // xóa vĩnh viễn
            Route::delete('force-delete/{id}', [VouchersController::class, 'forceDelete']);
            //xoa vv all
            Route::options('force-delete/all', [VouchersController::class, 'forceDeleteAll']);
            // chi tiết 1 voucher
            Route::get('detail/{id}', [VouchersController::class, 'show']);
        });

        Route::prefix('user')->group(function () {
            // list user chưa bị xóa mềm
            Route::get('', [UserController::class, 'index']);
            // filter user
            Route::get('filter', [UserController::class, 'filterUser']);
            //xoa mềm 1 user
            Route::delete('delete/{id}', [UserController::class, 'delete']);
            //list user đã xóa mềm
            Route::get('trashed/all', [UserController::class, 'trashed']);
            // get user theo id
            Route::get('detail/{id}', [UserController::class, 'show']);
            //restor 1 user
            Route::options('backup-one/{id}', [UserController::class, 'backupOne']);
            //restor all user đã xóa mềm
            Route::options('backup-all', [UserController::class, 'backupAll']);
            // đồng bộ hóa role cho user
            Route::post('syncRoles/{user_id}', [UserController::class, 'syncRoles']);
            // list role
            Route::get('role/all', [UserController::class, 'getAllRole']);
            Route::middleware(['permission:delete user'])->group(function () {
                //xoa vĩnh viễn 1 user
                Route::delete('force-delete/{id}', [UserController::class, 'forceDelete']);
            });
        });
        Route::prefix('info-user')->group(function () {
            // thêm mới info-user
            Route::post('store', [InfoUserController::class, 'store']);
            // update một info_user
            Route::put('update/{user_id}', [InfoUserController::class, 'update']);
        });

        // API thống kê
        Route::prefix('analytics')->group(function () {
            // thống kê doanh thu theo tháng - năm
            Route::get('order/revenue/{month}/{year}', [AnalyticsController::class, 'revenue']);
            // thống kê so sánh số đơn hàng tạo mới và số đơn hàng hoàn thành theo thời gian
            Route::get('order/compare/create/success/{month}/{year}', [AnalyticsController::class, 'compareCreateSuccess']);
            // thống kê số lượng sản phẩm đã bán
            Route::get('quantity/product', [AnalyticsController::class, 'quantityProduct']);
        });
        // API export data
        Route::prefix('export')->group(function () {
            Route::get('order/revenue/{month}/{year}', [AnalyticsController::class, 'ExportOrderRevenue']);
            Route::get('order/compare/create/success/{month}/{year}', [AnalyticsController::class, 'ExportCompareCreateSuccess']);
            Route::get('quantity/product', [AnalyticsController::class, 'ExportQuantityProduct']);
        });
        Route::prefix('feedback')->group(function () {
            // list ds feedback
            Route::get('', [FeedbacksController::class, 'index']);
            // lọc và sắp xếp; sort=0 => cũ nhất; sort=1 => mới nhất
            Route::get('filter', [FeedbacksController::class, 'filter']);
            // thống kê feedback
            Route::get('analytics/{month}/{year}', [FeedbacksController::class, 'analytics']);
        });
        Route::prefix('transport')->group(function () {
            // lấy danh sách các tỉnh
            Route::get('provinces', [TransportController::class, 'getProvince']);
            // reset lại thông tin giá cước
            Route::get('reset', [TransportController::class, 'resetTransport']);
            // cập nhật thông tin giá cước
            Route::put('update', [TransportController::class, 'updateTransport']);
            // lấy thông tin config_ghn
            Route::get('edit', [TransportController::class, 'editTransport']);
        });
    });
});

// các API của UI User
Route::prefix('product')->group(function () {
    // danh sách tất cả các sp chưa bị xóa mềm
    Route::get('', [ProductController::class, 'index']);
    // chi tiết 1 sp
    Route::get('detail/{id}', [ProductController::class, 'show']);
    // filter
    Route::get('filter/user', [ProductController::class, 'filterUser']);
});

Route::prefix('category')->group(function () {
    // lấy danh sách dm
    Route::get('', [CategoryController::class, 'index']);
    // list các sp trong dm
    Route::get('product/{cate_id}', [CategoryController::class, 'list_pro']);
});

Route::prefix('slide')->group(function () {
    Route::get('', [SlideController::class, 'index']);
    Route::get('detail/{id}', [SlideController::class, 'show']);
});

Route::prefix('blog')->group(function () {
    Route::get('', [BlogController::class, 'index']);
    Route::get('detail/{id}', [BlogController::class, 'show']);
});


// check auth
Route::prefix('auth')->group(function () {
    Route::get('verify/email', [AuthController::class, 'verifyEmailAccount']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('update-account', [AuthController::class, 'updateAccount'])->middleware('auth:sanctum');
    Route::post('change-password', [AuthController::class, 'changePassword'])->middleware('auth:sanctum');
    Route::get('forgot-password', [AuthController::class, 'forgotPassword']);
});


Route::prefix('comment')->group(function () {
    Route::get('', [CommentController::class, 'index']);
    Route::post('store', [CommentController::class, 'store']);
    Route::get('comment/{pro_id}', [ProductController::class, 'list_comments']);
});

Route::prefix('order')->group(function () {
    // verify email create order
    Route::get('verify/email', [OrderController::class, 'verifyEmail']);
    // payment with MOMO
    Route::get('payment/momo', [OrderController::class, 'paymentWithMomo']);
    // add order
    Route::post('add', [OrderController::class, 'add']);
    // cancel order
    Route::put('cancel/{order_id}', [OrderController::class, 'cancelOrder']);
    // list order chưa bị xóa mềm
    Route::get('all', [OrderController::class, 'index']);
    // list 1 order theo user_id

    // chi tiết một đơn hàng
    Route::get('detail/{id}', [OrderController::class, 'detail']);
});

Route::prefix('cart')->group(function () {
    // add cart
    Route::post('add-cart', [CartController::class, 'add']);
});
Route::prefix('voucher')->group(function () {
    //danh sach voucher
    Route::get('', [VouchersController::class, 'index']);
});
Route::prefix('transport')->group(function () {
    // lấy giá cước giao hàng cho UI
    Route::get('price/{total}', [TransportController::class, 'getPriceTransport']);
    // lấy tên tỉnh và các quận trong tỉnh cho UI
    Route::get('province/district', [TransportController::class, 'getProvinceDisstrict']);
});

############ API order dành cho khách hàng ################
Route::prefix('customer')->group(function () {
    // ds các đơn hàng đang xử lí
    Route::get('processing/{custom_id}', [OrderController::class, 'orderCustomerProcessing']);
    // ds các đơn hàng đang giao
    Route::get('delivering/{custom_id}', [OrderController::class, 'orderCustomerDelivering']);
    // ds các đơn hàng đã giao
    Route::get('success/{custom_id}', [OrderController::class, 'orderCustomerSuccess']);
    // list danh sách đơn hàng hiển thị mặc định
    Route::get('order/default/{custom_id}', [OrderController::class, 'orderDefault']);
    // đánh giá đơn hàng
    Route::post('feedback/add/{order_id}', [OrderController::class, 'postFeedback']);
    // list đơn hàng đã theo trạng thái đánh giá => chưa đánh giá -> 0; đã đánh giá -> 1
    Route::get('order/feedback/{status}/{custom_id}', [OrderController::class, 'getOrderStatusFeedback']);
});
