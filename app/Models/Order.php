<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'orders';
    public $fillable = [
        'user_id',
        'voucher_id',
        'shipper_id',
        'process_id',
        'total_price',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'customer_note',
        'transportation_costs',
        'payments',
        'paymentID',
        'shop_confirm',
        'time_shop_confirm',
        'shipper_confirm',
        'shop_note',
        'cancel_note',
    ];
    // lấy chi tiết đơn hàng
    public function order_details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    // lấy thông tin khách hàng
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // lấy thông tin vouher
    public function voucher()
    {
        return $this->belongsTo(Vouchers::class, 'voucher_id');
    }

    // lấy thông tin shipper
    public function shipper()
    {
        return $this->belongsTo(User::class, 'shipper_id');
    }

    // lấy thông tin process
    public function process()
    {
        return $this->belongsTo(OrderProcess::class, 'process_id');
    }

    //    lấy thông tin từ bảng feedback
    public function feedback()
    {
        return $this->hasOne(Feedbacks::class, 'order_id');
    }
    // lấy thông tin payment
    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id');
    }
}
