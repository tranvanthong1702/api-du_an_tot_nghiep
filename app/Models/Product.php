<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;use SoftDeletes;
    protected $table='products';
    public $fillable=[
        'cate_id',
        'name',
        'image',
        'price',
        'sale',
        'status',
        'expiration_date',
        'desc_short',
        'description'
    ];

    public function category(){
        return $this->belongsTo(Category::class,'cate_id');
     }
     public function comments()
    {
        return $this->hasMany(Comment::class,'pro_id');
    }

}
