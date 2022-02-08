<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory;use SoftDeletes;
    protected $table='comments';
    public $fillable=[
        'pro_id',
        'user_id',
        'content',
        'vote',
        'status',
    ];

 public function user()
    {
        return $this->belongsTo(User::class);
    }

}
