<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected function makePay(Request $request)
    {

        return \NextPay::makePay($request);
    }

    protected function confirmPay(Request $request)
    {
        if (isset($request->np_status) && $request->np_status == 'Unsuccessful') {
            Payment::where("order_id", $request->order_id)->delete();
            return redirect()->back()->with(['error-alert' => 'پرداخت ناموفق بود']);

        }
        return \NextPay::confirmPay($request);
    }
}
