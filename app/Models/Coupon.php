<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'coupons';
    protected $fillable = [
        'id', 'user_id', 'code', 'discount_percent', 'limit_price', 'created_at', 'used_at', 'used_times', 'expires_at',
    ];
    protected $casts = [

//        'expires_at' => 'timestamp',
        'used_at' => 'timestamp',
    ];
}
