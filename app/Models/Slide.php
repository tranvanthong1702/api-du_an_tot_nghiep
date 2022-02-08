<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slide extends Model
{
    use HasFactory;

    protected $table='slides';
    protected $primarykey='id';
    protected $fillable=[
        'name',
        'image',
        'cate_id'
    ];
}
