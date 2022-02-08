<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;
    protected $table='payments';
    public $fillable=[
        'order_id',
        'paymentID',
        'requestID',
        'transID',
        'amount',
        'resultCode',
        'message',
        'payType',
        'orderInfo',
        'requestType',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
