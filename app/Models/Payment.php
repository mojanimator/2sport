<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $table = 'payments';
    protected $fillable = [
        'id', 'user_id', 'agency_id', 'order_id', 'province_id', 'coupon_id', 'token_id', 'amount', 'card_holder', 'Shaparak_Ref_Id', 'pay_for', 'pay_for_id', 'created_at', 'updated_at'
    ];
    protected $casts = [

    ];
}