<?php

namespace App\Http\Controllers;

use App\Mail\RegisterEditUserMail;
use App\Models\Club;
use App\Models\Coach;
use App\Models\County;
use App\Models\Doc;
use App\Models\Player;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Sport;
use App\Models\User;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Morilog\Jalali\Jalalian;
use SMS;

class ShopController extends Controller
{
    protected function create(Request $request)
    {

//        $date = Carbon::now();

//        $sports = Sport::pluck('id')->toArray();

        $request->validate([
            'name' => 'string|min:3|max:100',
            'county_id' => 'required|' . Rule::in(County::pluck('id')),
            'province_id' => 'required|in:' . County::where('id', $request->county_id)->firstOrNew()->province_id,

            'phone' => 'required|numeric|digits:11|regex:/^09[0-9]+$/' . '|unique:shops,phone',
            'phone_verify' => [Rule::requiredIf(function () use ($request) {
                return !auth()->user() || $request->phone != auth()->user()->phone;
            }), !auth()->user() || $request->phone != auth()->user()->phone ? Rule::exists('sms_verify', 'code')->where(function ($query) use ($request) {
                return $query->where('phone', $request->phone);
            }) : '',],
            'description' => 'nullable|string|max:1024',
            'address' => 'required|string|max:1024',

            'license' => 'required|base64_image'/*.'|base64_size:2048'*/,
            'logo' => 'nullable|base64_image'/*.'|base64_size:2048'*/,
//            'video' => 'nullable|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:10240'

        ], [
            'name.required' => 'نام فروشگاه نمی تواند خالی باشد',
            'name.string' => 'نام فروشگاه نامعتبر است',
            'name.min' => 'نام فروشگاه حداقل 3 حرف باشد',
            'name.max' => 'نام فروشگاه حداکثر 100 حرف باشد',

            'family.required' => 'نام خانوادگی  نمی تواند خالی باشد',
            'family.string' => 'نام خانوادگی نامعتبر است',
            'family.min' => 'نام خانوادگی حداقل 3 حرف باشد',
            'family.max' => 'نام خانوادگی حداکثر 30 حرف باشد',

            'county_id.required' => 'شهر ضروری است',
            'county_id.in' => 'شهر نامعتبر است',
            'province_id.required' => 'استان ضروری است',
            'province_id.in' => 'استان نامعتبر است',
            'sport_id.required' => 'رشته ورزشی ضروری است',
            'sport_id.in' => 'رشته ورزشی نامعتبر است',
            'sport-rule_id.in' => 'پست رشته ورزشی نامعتبر است',

            'height.required' => 'قد ضروری است',
            'height.numeric' => 'قد نا معتبر است',
            'height.min' => 'قد نا معتبر است',
            'height.max' => 'قد نا معتبر است',

            'weight.required' => 'وزن ضروری است',
            'weight.numeric' => 'وزن نا معتبر است',
            'weight.min' => 'وزن نا معتبر است',
            'weight.max' => 'وزن نا معتبر است',

            'is_man.required' => 'جنسیت ضروری است',
            'is_man.boolean' => 'جنسیت نا معتبر است',

            'phone.required' => 'شماره تماس نمی تواند خالی باشد',
            'phone.numeric' => 'شماره تماس  11 رقم و با 09 شروع شود',
            'phone.digits' => 'شماره تماس  11 رقم و با 09 شروع شود',
            'phone.regex' => 'شماره تماس  11 رقم و با 09 شروع شود',
            'phone.unique' => 'شماره تماس تکراری است',
            'phone_verify.required' => 'کد تایید شماره همراه ضروری است',
            'phone_verify.exists' => 'کد تایید شماره همراه نامعتبر است',

            'address.required' => 'آدرس ضروری است',
            'address.string' => 'آدرس متنی باشد',
            'address.max' => 'حداکثر طول آدرس 1024 کلمه باشد. طول فعلی: ' . mb_strlen($request->address),

            'description.required' => 'توضیحات ضروری است',
            'description.string' => 'توضیحات متنی باشد',
            'description.max' => 'حداکثر طول توضیحات 2048 کلمه باشد. طول فعلی: ' . mb_strlen($request->description),

            'y.required' => 'سال تولد ضروری است',
            'y.numeric' => 'سال تولد بین 1300 تا 1500 است',
            'y.min' => 'سال تولد بین 1300 تا 1500 است',
            'y.max' => 'سال تولد بین 1300 تا 1500 است',

            'm.required' => 'ماه تولد ضروری است',
            'm.numeric' => 'ماه تولد بین 1 تا 12 است',
            'm.min' => 'ماه تولد بین 1 تا 12 است',
            'm.max' => 'ماه تولد بین 1 تا 12 است',

            'd.required' => 'روز تولد ضروری است',
            'd.numeric' => 'روز تولد بین 1 تا 31 است',
            'd.min' => 'روز تولد بین 1 تا 31 است',
            'd.max' => 'روز تولد بین 1 تا 31 است',

            'times.required' => 'حداقل یک ردیف برنامه کاری ضروری است',
            'times.array' => 'مقادیر برنامه کاری نمی تواند خالی باشد',
            'times.min' => 'حداقل یک ردیف برنامه کاری ضروری است',


            'license.required' => 'تصویر جواز کسب ضروری است',
            'license.base64_image' => 'فرمت تصویر جواز کسب نامعتبر است',
            'license.base64_size' => 'حداکثر حجم فایل 2 مگابایت باشد',

            'logo.base64_image' => 'فرمت تصویر جواز کسب نامعتبر است',
            'logo.base64_size' => 'حداکثر حجم فایل 2 مگابایت باشد',


            'images.required' => 'حداقل یک تصویر از باشگاه ضروری است',
            'images.array' => 'حداقل یک تصویر از باشگاه ضروری است',
            'images.min' => 'حداقل یک تصویر از باشگاه ضروری است',
            'images.max' => 'حداکثر تعداد تصاویر ' . \Helper::$club_image_limit . ' عدد است ',

            'images.*.base64_image' => 'فرمت تصویر از نوع  jpg باشد',
            'images.*.base64_size' => 'حداکثر حجم عکس 2 مگابایت باشد ',

            'video.required' => 'ویدیو ضروری است',
            'video.mimes' => 'ویدیو ارسالی با فرمت mp4 باشد',
            'video.max' => 'حجم ویدیو حداکثر 10 مگابایت باشد',

        ]);


        if (!auth()->user()) { //user not login or register
            $user = User::where('phone', $request->phone)->first(); //user not login
            if (!$user) { //user not exists
                $user = User::create([
                    'phone' => $request->phone,
                    'active' => true,
                    'name' => $request->name,
                    'family' => $request->family, 'phone_verified' => true,
                ]);
            }
        } else
            $user = auth()->user();

        $shop = Shop::create([
            'user_id' => $user->id,
            'province_id' => $request->province_id,
            'county_id' => $request->county_id,
            'name' => $request->name,
            'active' => false,
            'phone' => $request->phone,
            'description' => $request->description,
            'address' => $request->address,
            'phone_verified' => true,

//                'expires_at' => $data['ex_date'] ? CalendarUtils::createCarbonFromFormat('Y/m/d', $data['ex_date'])->addDays(1)->timezone('Asia/Tehran') : null,
        ]);

        //make   images
        Doc::createImage($request->license, $shop->id, Helper::$typesMap['shops'], Helper::$docsMap['license']);
        Doc::createImage($request->license, $shop->id, Helper::$typesMap['shops'], Helper::$docsMap['logo']);


        $user->setRefferal();
        if (!auth()->user())
            auth()->login($user);
        \Telegram::log(Helper::$TELEGRAM_GROUP_ID, 'shop_created', $shop);
        return \NextPay::makePay(new Request(['type' => 'shop', 'id' => $shop->id, 'month' => $request->{'renew-month'}, 'coupon' => $request->coupon, 'phone' => $shop->phone]));

//            return redirect(url('panel/shop'))->with('success-alert', 'با موفقیت ثبت شد! با انتخاب آن می توانید اطلاعات ثبت شده را مشاهده و ویرایش کنید');


    }

    protected function edit(Request $request)
    {


        $request->validate([
            'name' => 'sometimes|string|min:3|max:100',
            'location' => 'sometimes|string|max:100',
            'county_id' => 'required_with:province_id|' . Rule::in(County::pluck('id')),
            'province_id' => 'required_with:county_id|in:' . County::where('id', $request->county_id)->firstOrNew()->province_id,

            'phone' => 'sometimes|numeric|digits:11|regex:/^09[0-9]+$/' . '|unique:users,phone,' . auth()->user()->id,
            'phone_verify' => ['required_with:phone', Rule::requiredIf(function () use ($request) {
                return $request->phone && ($request->phone != auth()->user()->phone);
            }), $request->phone != auth()->user()->phone ? Rule::exists('sms_verify', 'code')->where(function ($query) use ($request) {
                return $query->where('phone', $request->phone);
            }) : '',],
            'description' => 'sometimes|string|max:1024',
            'address' => 'required_with:province_id|string|max:1024',
            'img' => 'sometimes|base64_image'/*.'|base64_size:2048'*/,
            'video' => 'sometimes|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:10240'
        ], [
            'name.required' => 'نام باشگاه نمی تواند خالی باشد',
            'name.string' => 'نام باشگاه نامعتبر است',
            'name.min' => 'نام باشگاه حداقل 3 حرف باشد',
            'name.max' => 'نام باشگاه حداکثر 100 حرف باشد',

            'family.required' => 'نام خانوادگی  نمی تواند خالی باشد',
            'family.string' => 'نام خانوادگی نامعتبر است',
            'family.min' => 'نام خانوادگی حداقل 3 حرف باشد',
            'family.max' => 'نام خانوادگی حداکثر 30 حرف باشد',

            'county_id.required_with' => 'شهر ضروری است',
            'county_id.in' => 'شهر نامعتبر است',
            'province_id.required_with' => 'استان ضروری است',
            'province_id.in' => 'استان نامعتبر است',
            'sport_id.required' => 'رشته ورزشی ضروری است',
            'sport_id.in' => 'رشته ورزشی نامعتبر است',
            'sport-rule_id.in' => 'پست رشته ورزشی نامعتبر است',

            'height.required' => 'قد ضروری است',
            'height.numeric' => 'قد نا معتبر است',
            'height.min' => 'قد نا معتبر است',
            'height.max' => 'قد نا معتبر است',

            'weight.required' => 'وزن ضروری است',
            'weight.numeric' => 'وزن نا معتبر است',
            'weight.min' => 'وزن نا معتبر است',
            'weight.max' => 'وزن نا معتبر است',

            'is_man.required' => 'جنسیت ضروری است',
            'is_man.boolean' => 'جنسیت نا معتبر است',

            'phone.required' => 'شماره تماس نمی تواند خالی باشد',
            'phone.numeric' => 'شماره تماس  11 رقم و با 09 شروع شود',
            'phone.digits' => 'شماره تماس  11 رقم و با 09 شروع شود',
            'phone.regex' => 'شماره تماس  11 رقم و با 09 شروع شود',
            'phone.unique' => 'شماره تماس تکراری است',
            'phone_verify.required' => 'کد تایید شماره همراه ضروری است',
            'phone_verify.required_with' => 'کد تایید شماره همراه ضروری است',
            'phone_verify.required_if' => 'کد تایید شماره همراه ضروری است',
            'phone_verify.exists' => 'کد تایید شماره همراه نامعتبر است',

            'address.required' => 'آدرس ضروری است',
            'address.required_with' => 'آدرس ضروری است',
            'address.string' => 'آدرس متنی باشد',
            'address.max' => 'حداکثر طول آدرس 1024 کلمه باشد. طول فعلی: ' . mb_strlen($request->address),

            'description.required' => 'توضیحات ضروری است',
            'description.string' => 'توضیحات متنی باشد',
            'description.max' => 'حداکثر طول توضیحات 1024 کلمه باشد. طول فعلی: ' . mb_strlen($request->description),

            'y.required' => 'سال تولد ضروری است',
            'y.numeric' => 'سال تولد بین 1300 تا 1500 است',
            'y.min' => 'سال تولد بین 1300 تا 1500 است',
            'y.max' => 'سال تولد بین 1300 تا 1500 است',

            'm.required' => 'ماه تولد ضروری است',
            'm.numeric' => 'ماه تولد بین 1 تا 12 است',
            'm.min' => 'ماه تولد بین 1 تا 12 است',
            'm.max' => 'ماه تولد بین 1 تا 12 است',

            'd.required' => 'روز تولد ضروری است',
            'd.numeric' => 'روز تولد بین 1 تا 31 است',
            'd.min' => 'روز تولد بین 1 تا 31 است',
            'd.max' => 'روز تولد بین 1 تا 31 است',

            'times.required' => 'حداقل یک ردیف برنامه کاری ضروری است',
            'times.array' => 'مقادیر برنامه کاری نمی تواند خالی باشد',
            'times.min' => 'حداقل یک ردیف برنامه کاری ضروری است',


            'img.required' => 'تصویر جواز کسب ضروری است',
            'img.base64_image' => 'فرمت تصویر جواز کسب نامعتبر است',
            'img.base64_size' => 'حداکثر حجم فایل 2 مگابایت باشد',


            'images.required' => 'حداقل یک تصویر از باشگاه ضروری است',
            'images.array' => 'حداقل یک تصویر از باشگاه ضروری است',
            'images.min' => 'حداقل یک تصویر از باشگاه ضروری است',
            'images.max' => 'حداکثر تعداد تصاویر ' . \Helper::$club_image_limit . ' عدد است ',

            'images.*.base64_image' => 'فرمت تصویر از نوع  jpg باشد',
            'images.*.base64_size' => 'حداکثر حجم عکس 2 مگابایت باشد ',

            'video.required' => 'ویدیو ضروری است',
            'video.mimes' => 'ویدیو ارسالی با فرمت mp4 باشد',
            'video.max' => 'حجم ویدیو حداکثر 10 مگابایت باشد',

        ]);


        if ($request->cmnd == 'delete-img') {

            $doc = Doc::find($request->id);
            $shop = Shop::where('id', $doc->docable_id)->first();
            if (!$this->authorize('ownItem', [User::class, $shop, false]))
                return response()->json(['errors' => ['تصویر متعلق به شما نیست']], 422);
            if ($request->replace && (Doc::where('type_id', $request->type)->where('docable_id', $doc->docable_id)->count() < 2))
                return response()->json(['errors' => ['حداقل یک تصویر ضروری است']], 422);

            Doc::deleteFile($doc);
            return redirect()->back()->with('success-alert', 'تصویر با موفقیت حذف شد');

        } elseif ($request->cmnd == 'upload-img' && $request->img) {


            if ($request->replace) {

                $doc = Doc::find($request->id);
                $shop = Shop::where('id', $doc->docable_id)->first();
                if (!$this->authorize('ownItem', [User::class, $shop, false]))
                    return response()->json(['errors' => ['تصویر متعلق به شما نیست']], 422);
                Doc::createImage($request->img, $doc->docable_id, $doc->docable_type, $doc->type_id, $doc->id);

            } else {
                $shop = Shop::where('id', $request->id)->first();
                if (!$this->authorize('ownItem', [User::class, $shop, false]))
                    return response()->json(['errors' => ['تصویر متعلق به شما نیست']], 422);

                if (Doc::where('type_id', $request->type)->where('docable_id', $shop->id)->count() >= 1)
                    return response()->json(['errors' => ['تعداد تصاویر بیش از حد مجاز (' . ' 1 ' . ') است']], 422);
                Doc::createImage($request->img, $shop->id, Helper::$typesMap['shops'], $request->type);

            }
            $this->dataEdited($shop, 'shop_edited', 'تصویر با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');


        }
        $shop = Shop::where('id', $request->id)->first();

        $this->authorize('ownItem', [User::class, $shop, true]);
        $user = auth()->user();

        if (isset($request->active)) {
            if ($request->active == true && $shop->active == false) { //activate
                if (Carbon::now()->timestamp > $shop->expires_at) {
                    return response()->json(['errors' => ['ابتدا فروشگاه را انتخاب کنید و از بالای صفحه، اشتراک آن را تمدید کنید']], 422);
                }
                if ($user->role != 'ad' && $user->role != 'go') {
                    return response()->json(['errors' => ['در صف فعالسازی است و پس از بررسی توسط ادمین فعال خواهد شد']], 422);
                }

            }
            $shop->active = $request->active;
            $shop->save();
        } elseif ($request->name) {
            if ($shop->name == $request->name) return null;
            $shop->name = $request->name;
            $this->dataEdited($shop, 'shop_edited', 'نام با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');

        } elseif ($request->phone && $request->phone_verify) {
            $shop->phone = $request->phone;
            (new SMS())->deleteActivationSMS($request->phone);
            $shop->save();

        } elseif ($request->province_id && $request->county_id && $request->address) {
            if ($shop->province_id == $request->province_id && $shop->county_id == $request->county_id && $shop->location == $request->location && $shop->address == $request->address) return null;
            $shop->province_id = $request->province_id;
            $shop->location = $request->location;
            $shop->county_id = $request->county_id;
            $shop->address = $request->address;
            $this->dataEdited($shop, 'shop_edited', 'استان/شهر/آدرس با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');

        } elseif ($request->description) {
            if ($shop->description == $request->description) return null;
            $shop->description = $request->description;
            $this->dataEdited($shop, 'shop_edited', 'توضیحات با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');

        }
    }

    protected function remove(Request $request)
    {
        $data = Shop::where('id', $request->id)->first();
        if (!$this->authorize('ownItem', [User::class, $data, false]))
            return response()->json(['errors' => ['اجازه حذف را ندارید']], 422);
        foreach (Product::where('shop_id', $data->id)->get() as $product) {
            foreach ($product->docs as $doc) {
                Doc::deleteFile($doc);
            }
            $product->delete();
        }
        foreach ($data->docs as $doc) {
            Doc::deleteFile($doc);
        }
        $data->delete();

    }

    protected function search(Request $request)
    {


        $page = $request->page;
        $paginate = $request->paginate;
        $province_id = $request->province;
        $county_id = $request->county;
        $name = $request->name;
        $pname = $request->pname;
        $sport_id = $request->sport;

        $orderBy = $request->order_by;
        $dir = $request->dir;
        $panel = $request->panel;
        $active = $request->active;
        $user_id = $request->user;

        $user = auth()->user();

        if (!$paginate) {
            $paginate = 12;
        }
        if (!$page) {
            $page = 1;
        }
        if (!$dir) {
            $dir = 'DESC';
        }
        if (!$orderBy) {
            $orderBy = 'created_at';
        }


        $query = Shop::query();

        if (is_numeric($province_id))
            $query = $query->where('province_id', $province_id);
        if (is_numeric($county_id))
            $query = $query->where('county_id', $county_id);

//
        if ($name) {
            foreach (explode(' ', $name) as $word) {
                $query = $query->where(function ($query) use ($word) {
                    $query->orWhere('name', 'LIKE', '%' . $word . '%');
                });
            }
        }
        if ($pname) {
            foreach (explode(' ', $pname) as $word) {
                $query = $query->whereIn('id', Product::where('name', 'LIKE', '%' . $word . '%')->pluck('shop_id'));
            }
        }
        if (is_numeric($sport_id)) {

            $query = $query->whereJsonContains('groups', (int)$sport_id);
        }


        if (!$user || !$panel) {

            $query = $query->where('active', true);
        } else {

            if ($panel && $user->role == 'us')
                $query = $query->where('user_id', $user->id);
            if (is_numeric($active))
                $query = $query->where('active', (boolean)$active);
            if ($user_id && $panel && ($user->role == 'ad' || $user->role == 'go'))
                $query = $query->where('user_id', $user_id);
        }
        if ($orderBy)
            $query = $query->orderBy($orderBy, $dir);


        $query = $query->with('docs');


//        $data = $query->offset($page - 1)->limit($paginate)->get();

        $data = $query->paginate($paginate, ['*'], 'page', $page);

//        foreach ($data as $idx => $item) {
//            $img = \App\Models\Image::on(env('DB_CONNECTION'))->where('type', 'p')->where('for_id', $item->id)->inRandomOrder()->first();
//            if ($img)
//                $item['img'] = $img->id . '.jpg';
//        }

        return $data;
    }
}
