<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressCustom extends Model
{
    use HasFactory;
    protected $table='address_customs';
    public $fillable=[
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'provinceID',
        'districtID',
        'customer_address',
    ];
}
