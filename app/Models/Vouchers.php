<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vouchers extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'vouchers';
    public $fillable = [
        'classify_voucher_id',
        'title',
        'sale',
        'customer_type',
        'condition',
        'expiration',
        'times',
        'start_day',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'voucher_users', 'voucher_id', 'user_id',);
    }
}
