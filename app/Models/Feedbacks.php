<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedbacks extends Model
{
    use HasFactory;
    protected $table = 'feedbacks';
    // lấy thông tin order theo feedback
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
