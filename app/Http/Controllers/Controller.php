<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Setting;
use App\Models\Sport;
use App\Models\County;
use App\Models\Province;
use App\Models\Shop;
use Firebase\JWT\JWT;
use Helper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function sendError(Request $request)
    {
        $message = $request->message;
        $type = $request->type;

        if (isset($type))
            \Telegram::sendMessage(Helper::$TELEGRAM_GROUP_ID, $message);
        else
            \Telegram::logAdmins($message);

        return response()->json(['status' => 'success']);
    }


    protected function dataEdited($data, $logType, $attribute = "", $active = null)
    {
        $msg = "";
        $edited = "$attribute با موفقیت ویرایش شد";
        $queued = "و در صف بررسی قرار گرفت";
        $activated = "با موفقیت فعال شد";
        $deactivated = "با موفقیت غیرفعال شد";

        $us = auth()->user() ?: auth('api')->user();
        $allow_edit_without_check = ($us->isAdmin() || ($us->role == 'bl' && (str_contains($logType, 'event') || str_contains($logType, 'blog'))) || (($us->role == 'ag' || $us->role == 'aa') && str_contains($logType, 'agency')) || str_contains($logType, 'user'));

        if (isset($data) && isset($us)) {

            //activate/deactivate
            if ($attribute == 'شماره تماس')
                $msg = "$edited $queued";

            else
                if (isset($active)) {
                    if ($allow_edit_without_check) {
                        //admin activated a data
                        if ($active) {
                            if (isset($data->active))
                                $data->active = true;
                            if (isset($data->in_review))
                                $data->in_review = false;
                            $msg = "$activated";
                        } //admin deactivated a data
                        else {
                            if (isset($data->active))
                                $data->active = false;
                            if (isset($data->in_review))
                                $data->in_review = false;
                            $msg = "$deactivated";
                        }
                    } else {
                        //user activated a data
                        if ($active) {
                            if (isset($data->in_review))
                                if ($data->in_review == true)
                                    $msg = "در صف بررسی است و بزوی فعال خواهد شد";
                                else {
//                                    $msg = "در صف بررسی قرار گرفت و بزودی فعال خواهد شد";
                                    $msg = "با موفقیت فعال شد";
                                    $data->active = $active;
                                }
                        }  //user deactivated a data
                        else {
                            if (isset($data->active))
                                $data->active = false;
                            if (isset($data->in_review))
                                $data->in_review = false;
                            $msg = "$deactivated";
                        }
                    }

                } //edited info
                else {
                    //edited info need check
                    if (!$allow_edit_without_check) {
                        if (isset($data->active))
                            $data->active = false;
                        if (isset($data->in_review))
                            $data->in_review = true;
                        $msg = "$edited $queued";
                    } else
                        $msg = "$edited";


                }
            $data->save();
            $data->attribute = $attribute;
            \Telegram::log(\Helper::$TELEGRAM_GROUP_ID, $logType, $data);

            /*   if (!str_contains(request()->url(), 'api/'))
                   return redirect()->back()->with('success-alert', $msg);
               else*/
            return response()->json(['status' => 'success', 'msg' => $msg]);
        }
    }

    protected
    function settings(Request $request)
    {
        return response()->json([

            'days' => [0 => 'شنبه', 1 => 'یکشنبه', 2 => 'دوشنبه', 3 => 'سه شنبه', 4 => 'چهارشنبه', 5 => 'پنجشنبه', 6 => 'جمعه', 7 => 'هر روز',],
            'years' => range(1400, 1330),
            'prices' => Setting::orderBy('id', 'ASC')->get(),
            'provinces' => Province::select('id', 'name')->get(),
            'shops' => Shop::select('id', 'name')->orderByDesc('id')->get(),
            'counties' => County::select('id', 'name', 'province_id')->get(),
            'sports' => Sport::select('id', 'name')->get(),
            'categories' => Category::select('id', 'name')->get(),

            'crop_ratio' => Helper::$cropsRatio,
            'app_info' => [
                'version' => Helper::$APP_VERSION,
                'phone' => Helper::$admin_phone,
                'links' => [
                    'app' => '', 'comments' => '',
                    'aparat' => 'https://www.aparat.com/dabel.media',
                    'site' => 'https://2sport.ir',
                    'telegram' => 'https://t.me/develowper',
                    'instagram' => 'https://instagram.com/develowper',
                    'email' => 'double24.info@gmail.com']],
            'doc_types' => Helper::$docsMap,
            'limits' => ['club' => Helper::$club_image_limit, 'product' => Helper::$product_image_limit],
            'keys' => [
                'bazaar' => env('BAZAAR_RSA'),
            ],
            'chat_script' => '<!--BEGIN RAYCHAT CODE-->
  <script type="text/javascript">!function(){function t(){var t=document.createElement("script");t.type="text/javascript",t.async=!0,localStorage.getItem("rayToken")?t.src="https://app.raychat.io/scripts/js/"+o+"?rid="+localStorage.getItem("rayToken")+"&href="+window.location.href:t.src="https://app.raychat.io/scripts/js/"+o+"?href="+window.location.href;var e=document.getElementsByTagName("script")[0];e.parentNode.insertBefore(t,e)}var e=document,a=window,o="897e98b2-466c-42d9-9655-af7ef7d67840";"complete"==e.readyState?t():a.attachEvent?a.attachEvent("onload",t):a.addEventListener("load",t,!1)}();</script>
<!--END RAYCHAT CODE-->',

        ], 200);
    }

    protected
    function latest(Request $request)
    {
        $paginate = $request->paginate ?: 12;
        $page = is_numeric($request->page) ? $request->page : 1;
        $cols = 'id, province_id, county_id,created_at, ';
        $p1 = \App\Models\Player::selectRaw($cols . 'CONCAT(name,\' \', family) as name, "pl" as type')->where('active', true)->where('hidden', false);
        $p2 = \App\Models\Coach::selectRaw($cols . 'CONCAT(name,\' \', family) as name, "co" as type')->where('active', true)->where('hidden', false);
        $p3 = \App\Models\Club::selectRaw($cols . 'name, "cl" as type')->where('active', true)->where('hidden', false);
        $p4 = \App\Models\Shop::selectRaw($cols . 'name, "sh" as type')->where('active', true)->where('hidden', false);
        $p5 = \App\Models\Product::selectRaw('id,price as province_id,discount_price as county_id,created_at,name, "pr" as type')->where('active', true)->where('hidden', false);


        $res = $p5->union($p4)->union($p1)->union($p2)->union($p3)
            ->with('alldocs')
            ->orderByDesc('created_at')
//            ->inRandomOrder()
            ->paginate($paginate, ['*'], 'page', $page);

//        $res->setCollection($res->getCollection()->map(function ($row) {
//
//            $docs = $row->docs->filter(function ($item) use ($row) {
//
//                return $item->docable_type == $row->type;
//            })->values();
//
//            return [
//                'id' => $row->id,
//                'name' => $row->name,
//                'type' => $row->type,
//                'province_id' => $row->province_id,
//                'county_id' => $row->county_id,
//                'docs' => $docs
//            ];
//        }));
        return $res;
    }

    protected
    function error(Request $request)
    {

    }
}
