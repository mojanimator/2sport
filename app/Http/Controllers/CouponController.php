<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    protected function calculate(Request $request)
    {
        $coupon = $request->coupon;
        $type = $request->type;
        $coupon = Coupon::where('code', $coupon)->first();
        if (!$coupon)
            return response()->json(['errors' => ['coupon' => ['کد تخفیف معتبر نیست']]], 422);
        if ($coupon->user_id != null && $coupon->user_id != auth()->user()->id)
            return response()->json(['errors' => ['coupon' => ['کد تخفیف برای شما معتبر نیست']]], 422);
        if (Payment::where('coupon_id', $coupon->id)->where('user_id', auth()->user()->id)->exists())
            return response()->json(['errors' => ['coupon' => ['کد تخفیف را قبلا استفاده کرده اید']]], 422);
        if ($coupon->used_at != null)
            return response()->json(['errors' => ['coupon' => ['کد تخفیف استفاده شده است']]], 422);

        if ($coupon->expires_at != null && Carbon::now()->timestamp > $coupon->expires_at)
            return response()->json(['errors' => ['coupon' => ['کد تخفیف منقضی شده است']]], 422);


        $settings = \App\Models\Setting::where('key', 'like', $type . '%')->where('key', 'like', '%_price')->get()->all();
        $res = [];
        foreach ($settings as $setting) {
            $discount = $setting->value * $coupon->discount_percent / 100;

            $res[$setting->key] = round($setting->value - ($coupon->limit_price && $discount > $coupon->limit_price ? $coupon->limit_price : $discount));
        }
        return response()->json($res, 200);

    }
}
