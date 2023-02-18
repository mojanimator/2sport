<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Payment;
use App\Models\Player;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Morilog\Jalali\Jalalian;

class SettingController extends Controller
{
    protected function create(Request $request)
    {
        $request->validate([

            'name' => 'required|max:255',
            'key' => 'required|max:100|regex:/^[A-Za-z]+[A-Za-z0-9_]{1,100}$/|unique:settings,key',
            'value' => 'required|max:100',
        ], [
            'name.required' => 'نام فارسی ضروری است',
            'name.max' => 'طول نام فارسی حداکثر 255 باشد',
            'key.required' => 'شناسه ضروری است',
            'key.max' => 'حداکثر طول کلید 100 باشد',
            'key.regex' => 'کلید حداقل دو حرف و با حروف انگلیسی شروع شود و می تواند شامل عدد و _  باشد',
            'key.unique' => 'کلید تکراری است',

            'value.required' => 'مقدار ضروری است',
            'value.max' => 'حداکثر طول مقدار 100 باشد',

        ]);
        $this->authorize('editItem', [User::class, new Setting(), true]);
        Setting::create([
            'name' => $request->name,
            'key' => $request->key,
            'value' => $request->value
        ]);
        return Setting::get();
    }

    protected function edit(Request $request)
    {
        $request->validate([
            'id' => 'required|' . Rule::in(Setting::pluck('id')),
            'value' => 'required|max:100',
        ], [
            'id.required' => 'شناسه ضروری است',
            'id.in' => 'شناسه نامعتبر است',
            'value.required' => 'مقدار ضروری است',
            'value.max' => 'حداکثر طول مقدار 100 باشد',

        ]);
        $setting = Setting::where('id', $request->id)->first();
        $this->authorize('editItem', [User::class, $setting, true]);
        $setting->value = $request->value;
        $setting->save();
        return Setting::all();
    }

    protected function remove(Request $request)
    {
        $request->validate([
            'id' => 'required|' . Rule::in(Setting::pluck('id')),
        ], [
            'id.required' => 'شناسه ضروری است',
            'id.in' => 'شناسه نامعتبر است',

        ]);

        $this->authorize('editItem', [User::class, new Setting(), true]);
        Setting::where('id', $request->id)->delete();
        return Setting::all();
    }

    protected function log(Request $request)
    {
        $user = auth()->user();


        $type = $request->type ? $request->type : 'تعداد';
        $types = $request->types ? $request->types : [];
        $province = $request->province;
        $users = false;

        if (in_array($user->role, ['us', 'bl']))
            $users = [$user->id];
        if (in_array($user->role, ['ag', 'aa']))
            $users = User::where('agency_id', $user->agency_id)->pluck('id')->toArray();
        if (in_array($user->role, ['go', 'ad']))
            if (isset($request->agency))
                $users = User::where('agency_id', $request->agency)->pluck('id')->toArray();
            else
                $users = null;

        $timestamp = $request->timestamp;//y,m,d

        $from = f2e($request->dateFrom);
        $to = f2e($request->dateTo);
//        $from = '1400/10/10';
//        $to = '1401/01/03';

        $from = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $from);
        $to = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $to);

        $X_labels = [];
        $res = [];
        //shift (from and to) for month or year timestamp
        if ($timestamp == 'd' || !$timestamp) {

        } elseif ($timestamp == 'm') { //from day 1 : to day 30 or 31
            if ($c = $from->getDay() - 1 > 0)
                $from = $from->subDays($c);
            if ($c = $to->getMonthDays() - $to->getDay() > 0)
                $to = $to->addDays($c);
        } elseif ($timestamp == 'y') { //from day 1 : to day 30 or 31
            if ($c = $from->getDay() - 1 > 0)
                $from = $from->subDays($c);
            if ($c = $from->getMonth() - 1 > 0)
                $from = $from->subMonths($c);
            if ($c = $to->getMonthDays() - $to->getDay() > 0)
                $to = $to->addDays($c);
            if ($c = 12 - $to->getMonth() > 0)
                $to = $to->addMonths($c);
        }
        $tmp = $from;

        while ($tmp->lessThanOrEqualsTo($to)) {

            if ($timestamp == 'd' || !$timestamp) {
                $X_labels[] = $tmp->format('Y/m/d');
                $tmp = $tmp->addDays(1);
            } elseif ($timestamp == 'm') {
                $X_labels[] = $tmp->format('Y/m');
                $tmp = $tmp->addMonths(1);
            } elseif ($timestamp == 'y') {
                $X_labels[] = $tmp->format('Y');
                $tmp = $tmp->addYears(1);
            }
        }

        DB::statement("SET SQL_MODE=''");//this is the trick use it just before your query

        //get data with date group
        if ($type == 'تعداد')
            foreach ([
                         ['type' => \Helper::$labelsMap['players'], 'query' => Player::query(), 'col' => ['user_id', DB::raw('CONCAT(name,\' \', family) AS name'), 'id', 'province_id', 'created_at', DB::raw('"بازیکن" AS type')]],
                         ['type' => \Helper::$labelsMap['coaches'], 'query' => Coach::query(), 'col' => ['user_id', DB::raw('CONCAT(name,\' \', family) AS name'), 'id', 'province_id', 'created_at', DB::raw('"مربی" AS type')]],
                         ['type' => \Helper::$labelsMap['clubs'], 'query' => Club::query(), 'col' => ['user_id', 'name', 'id', 'province_id', 'created_at', DB::raw('"باشگاه" AS type')]],
                         ['type' => \Helper::$labelsMap['shops'], 'query' => Shop::query(), 'col' => ['user_id', 'name', 'id', 'province_id', 'created_at', DB::raw('"فروشگاه" AS type')]],
                         ['type' => \Helper::$labelsMap['products'], 'query' => Product::query(), 'col' => ['name', 'id', DB::raw('shop_id'), 'created_at', DB::raw('"محصول" AS type')]],
                         ['type' => \Helper::$labelsMap['blogs'], 'query' => Blog::query(), 'col' => ['user_id', DB::raw('title AS name'), 'id', DB::raw('category_id'), 'created_at', DB::raw('"خبر" AS type')]],
                     ] as $item) {


                if (in_array($item['type'], $types)) {
                    if ($province && $item['type'] == \Helper::$labelsMap['blogs']) continue;
                    $query = $item['query'];
                    $query = $query->select($item['col'])
                        ->where('created_at', '>=', $from->toCarbon())
                        ->where('created_at', '<=', $to->toCarbon()->addDay());
                    if ($province && $item['type'] != \Helper::$labelsMap['blogs'] && $item['type'] != \Helper::$labelsMap['products'])
                        $query = $query->where('province_id', $province);

                    if ($item['type'] != \Helper::$labelsMap['products']) {
                        if (is_array($users))
                            $query = $query->whereIntegerInRaw('user_id', $users);
                        elseif ($users === false)
                            $query = $query->where('user_id', 0);

                    } else {
                        if (is_array($users))
                            $query = $query->whereIntegerInRaw('shop_id', Shop::whereIntegerInRaw('user_id', $users)->pluck('id'));
                        elseif ($users === false)
                            $query = $query->where('id', 0);

                        if ($province)
                            if (in_array(\Helper::$labelsMap['shops'], $types)) {
                                $query = $query->whereIntegerInRaw('shop_id', $res['data'][\Helper::$labelsMap['shops']]->pluck('id'));
                            } else {
                                $shops = Shop::where('province_id', $province);
                                if (is_array($users))
                                    $shops = $shops->whereIntegerInRaw('user_id', $users);
                                elseif ($users === false)
                                    $shops = $shops->where('user_id', 0);
                                $query = $query->whereIntegerInRaw('shop_id', $shops->pluck('id'));
                            }
                    }

                    $res['data'][$item['type']] = $query->orderBy('created_at', 'DESC');


                }
            }
        elseif ($type == 'مالی')
            foreach ([
                         ['table' => 'players', 'pay_for' => 'player', 'type' => \Helper::$labelsMap['players'],],
                         ['table' => 'coaches', 'pay_for' => 'coach', 'type' => \Helper::$labelsMap['coaches'],],
                         ['table' => 'clubs', 'pay_for' => 'club', 'type' => \Helper::$labelsMap['clubs'],],
                         ['table' => 'shops', 'pay_for' => 'shop', 'type' => \Helper::$labelsMap['shops'],],
                     ] as $item) {


                if (in_array($item['type'], $types)) {

                    $query = Payment::query();
                    $query = $query->select('agency_id', 'order_id', 'province_id', 'amount', 'Shaparak_Ref_Id', 'pay_for', 'pay_for_id', 'coupon_id', 'created_at')
                        ->where('pay_for', 'like', $item['pay_for'] . '%')
                        ->whereNotNull('amount')
                        ->where('created_at', '>=', $from->toCarbon())
                        ->where('created_at', '<=', $to->toCarbon()->addDay());

                    if ($province) {
                        $query = $query->where('province_id', $province);
                    }
                    if ($request->agency) {
                        $query = $query->where('agency_id', $request->agency_id);
                    }
                    if (is_array($users))
                        $query = $query->whereIntegerInRaw('user_id', $users);
                    elseif ($users === false)
                        $query = $query->where('user_id', 0);

                    $res['data'][$item['type']] = $query->orderBy('created_at', 'DESC');


//                    if ($province) {
//                        $filter = DB::table($item['table'])->where('province_id', $province)->whereIntegerInRaw('id', $res['data'][$item['type']]->pluck('pay_for_id'))->pluck('id')->toArray();
//                        $res['data'][$item['type']] = $res['data'][$item['type']]->filter(function ($el) use ($filter) {
//                            return in_array($el->pay_for_id, $filter);
//                        });
//                    }

                }
            }
        foreach ($res['data'] as $type => $item) {


            $res['data'][$type] = $res['data'][$type]->get()->groupBy(function ($data) use ($timestamp) {
                if ($timestamp == 'm')
                    return Jalalian::fromCarbon($data->created_at)->format('Y/m');
                elseif ($timestamp == 'y')
                    return Jalalian::fromCarbon($data->created_at)->format('Y');
                else
                    return Jalalian::fromCarbon($data->created_at)->format('Y/m/d');
            });
        }
        $res['dates'] = $X_labels;
        return response($res, 200);

    }
}
