<?php

namespace App\Http\Controllers;

use App\Mail\RegisterEditUserMail;
use App\Models\County;
use App\Models\Doc;
use App\Models\Player;
use App\Models\Setting;
use App\Models\Sport;
use App\Models\User;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Morilog\Jalali\Jalalian;
use SMS;

class PlayerController extends Controller
{
    protected function create(Request $request)
    {

//        $date = Carbon::now();

        $request->validate([
            'name' => 'string|min:3|max:100',
            'family' => 'string|min:3|max:100',
            'county_id' => 'required|' . Rule::in(County::pluck('id')),
            'province_id' => 'required|in:' . County::where('id', $request->county_id)->firstOrNew()->province_id,
            'sport_id' => 'required|' . Rule::in(Sport::pluck('id')),
            'sport-rule_id' => 'sometimes|' . Rule::in(DB::table('sport-rules')->pluck('id')),
            'height' => 'required|numeric|min:10|max:300',
            'weight' => 'required|numeric|min:10|max:300',
            'is_man' => 'required|boolean',
            'phone' => 'required|numeric|digits:11|regex:/^09[0-9]+$/' . '|unique:players,phone',
            'phone_verify' => [Rule::requiredIf(function () use ($request) {
                return !auth()->user() || $request->phone != auth()->user()->phone;
            }), !auth()->user() || $request->phone != auth()->user()->phone ? Rule::exists('sms_verify', 'code')->where(function ($query) use ($request) {
                return $query->where('phone', $request->phone);
            }) : '',],
            'description' => 'nullable|string|max:2048',
            'y' => 'required|numeric|min:1200|max:1500',
            'm' => 'required|numeric|min:1|max:12',
            'd' => 'required|numeric|min:1|max:31',

            'img' => 'required|base64_image'/*|base64_size:10240'*/,
            'video' => 'nullable|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:51200'
        ], [
            'name.required' => 'نام  نمی تواند خالی باشد',
            'name.string' => 'نام  نامعتبر است',
            'name.min' => 'نام  حداقل 3 حرف باشد',
            'name.max' => 'نام  حداکثر 100 حرف باشد',

            'family.required' => 'نام خانوادگی  نمی تواند خالی باشد',
            'family.string' => 'نام خانوادگی نامعتبر است',
            'family.min' => 'نام خانوادگی حداقل 3 حرف باشد',
            'family.max' => 'نام خانوادگی حداکثر 100 حرف باشد',

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

            'img.required' => 'تصویر ضروری است',
            'img.base64_image' => 'فرمت تصویر نامعتبر است',
            'img.base64_size' => 'حداکثر حجم فایل 1 مگابایت باشد',

            'video.required' => 'ویدیو ضروری است',
            'video.mimes' => 'ویدیو ارسالی با فرمت mp4 باشد',
            'video.max' => 'حجم ویدیو حداکثر 50 مگابایت باشد',

        ]);

//        DB::transaction(function () use ($request, & $user) {

        if (!auth()->user()) { //user not login or register
            $user = User::where('phone', $request->phone)->first(); //user not login
            if (!$user) { //user not exists
                $user = User::create([
                    'phone' => $request->phone,
                    'active' => true,
                    'name' => $request->name,
                    'family' => $request->family,
                    'phone_verified' => true,
                ]);
            }
        } else
            $user = auth()->user();

        $player = Player::create([
            'user_id' => $user->id,
            'province_id' => $request->province_id,
            'county_id' => $request->county_id,
            'sport_id' => $request->sport_id,
            'sport-rule_id' => $request->{'sport-rule_id'},
            'username' => $request->username,
            'name' => $request->name,
            'family' => $request->family,
            'height' => $request->height,
            'weight' => $request->weight,
            'born_at' => (new Jalalian($request->y, $request->m, $request->d))->toCarbon(),
            'is_man' => $request->is_man,
            'active' => false,
            'phone' => $request->phone,
            'description' => $request->description,
            'phone_verified' => true,
//                'expires_at' => $data['ex_date'] ? CalendarUtils::createCarbonFromFormat('Y/m/d', $data['ex_date'])->addDays(1)->timezone('Asia/Tehran') : null,
        ]);

        //make profile image
        Doc::createImage($request->img, $player->id, Helper::$typesMap['players'], Helper::$docsMap['profile']);
        Doc::createVideo($request->file('video'), $player->id, Helper::$typesMap['players'], Helper::$docsMap['video']);


        $user->setRefferal();
            if (!auth()->user())
                auth()->login($user);

        \Telegram::log(Helper::$TELEGRAM_GROUP_ID, 'player_created', $player);

        return \NextPay::makePay(new Request(['type' => 'player', 'id' => $player->id, 'month' => $request->{'renew-month'}, 'coupon' => $request->coupon,'phone'=>$player->phone]));
//            return redirect(url('panel/player'))->with('success-alert', 'با موفقیت ثبت شد! با انتخاب آن می توانید اطلاعات ثبت شده را مشاهده و ویرایش کنید');

//        });


    }

    protected function edit(Request $request)
    {

        $sports = Sport::pluck('id')->toArray();

        $request->validate([
            'name' => 'sometimes|string|min:3|max:100',
            'family' => 'sometimes|string|min:3|max:100',
            'county_id' => 'required_with:province_id|' . Rule::in(County::pluck('id')),
            'province_id' => 'required_with:county_id|in:' . County::where('id', $request->county_id)->firstOrNew()->province_id,
            'is_man' => 'sometimes|boolean',
            'y' => 'required_with:d|numeric|min:1200|max:1500',
            'm' => 'required_with:y|numeric|min:1|max:12',
            'd' => 'required_with:m|numeric|min:1|max:31',
            'height' => 'required_with:weight|numeric|min:10|max:300',
            'weight' => 'required_with:height|numeric|min:10|max:300',
            'sport_id' => 'sometimes|' . Rule::in($sports),
            'phone' => 'sometimes|numeric|digits:11|regex:/^09[0-9]+$/' . '|unique:users,phone,' . auth()->user()->id,
            'phone_verify' => ['required_with:phone', Rule::requiredIf(function () use ($request) {
                return $request->phone && ($request->phone != auth()->user()->phone);
            }), $request->phone != auth()->user()->phone ? Rule::exists('sms_verify', 'code')->where(function ($query) use ($request) {
                return $query->where('phone', $request->phone);
            }) : '',],
            'description' => 'sometimes|string|max:2048',
            'img' => 'sometimes|base64_image'/*.'|base64_size:2048'*/,
            'video' => 'sometimes|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:51200'
        ], [
            'name.required' => 'نام  نمی تواند خالی باشد',
            'name.string' => 'نام  نامعتبر است',
            'name.min' => 'نام  حداقل 3 حرف باشد',
            'name.max' => 'نام  حداکثر 100 حرف باشد',

            'family.required' => 'نام خانوادگی  نمی تواند خالی باشد',
            'family.string' => 'نام خانوادگی نامعتبر است',
            'family.min' => 'نام خانوادگی حداقل 3 حرف باشد',
            'family.max' => 'نام خانوادگی حداکثر 100 حرف باشد',

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
            'video.max' => 'حجم ویدیو حداکثر 50 مگابایت باشد',

        ]);


        if ($request->cmnd == 'delete-vid') {

            $doc = Doc::find($request->id);
            $player = Player::where('id', $doc->docable_id)->first();
            if (!$this->authorize('ownItem', [User::class, $player, false]))
                return response()->json(['errors' => ['ویدیو متعلق به شما نیست']], 422);
            if (Doc::where('type_id', $request->type)->where('docable_id', $doc->docable_id)->count() < 2)
                return response()->json(['errors' => ['حداقل یک ویدیو ضروری است']], 422);

            Doc::deleteFile($doc);

            return redirect()->back()->with('success-alert', 'ویدیو با موفقیت حذف شد');

        } elseif ($request->cmnd == 'upload-vid' && $request->video) {

            if ($request->replace) {

                $doc = Doc::find($request->id);
                $player = Player::where('id', $doc->docable_id)->first();

                if (!$this->authorize('ownItem', [User::class, $player, false]))
                    return response()->json(['errors' => ['ویدیو متعلق به شما نیست']], 422);
                Doc::createVideo($request->file('video'), $player->id, Helper::$typesMap['players'], Helper::$docsMap['video'], $doc->id);

            } elseif ($request->type == Helper::$docsMap['video']) {
                $player = Player::where('id', $request->id)->first();

                if (!$this->authorize('ownItem', [User::class, $player, false]))
                    return response()->json(['errors' => ['ویدیو متعلق به شما نیست']], 422);
                if (Doc::where('type_id', $request->type)->where('docable_id', $player->id)->count() >= 1)
                    return response()->json(['errors' => ['تعداد ویدیو بیش از حد مجاز (' . ' 1 ' . ') است']], 422);
                Doc::createVideo($request->file('video'), $player->id, Helper::$typesMap['players'], $request->type);

            }

            $this->dataEdited($player, 'player_edited', 'ویدیو با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');

        } elseif ($request->cmnd == 'delete-img') {

            $doc = Doc::find($request->id);
            $player = Player::where('id', $doc->docable_id)->first();
            if (!$this->authorize('ownItem', [User::class, $player, false]))
                return response()->json(['errors' => ['تصویر متعلق به شما نیست']], 422);
            if (Doc::where('type_id', $request->type)->where('docable_id', $doc->docable_id)->count() < 2)
                return response()->json(['errors' => ['حداقل یک تصویر ضروری است']], 422);

            Doc::deleteFile($doc);
            return redirect()->back()->with('success-alert', 'تصویر با موفقیت حذف شد');

        } elseif ($request->cmnd == 'upload-img' && $request->img) {

            if ($request->replace) {
                $doc = Doc::find($request->id);
                $player = Player::where('id', $doc->docable_id)->first();
                if (!$this->authorize('ownItem', [User::class, $player, false]))
                    return response()->json(['errors' => ['تصویر متعلق به شما نیست']], 422);
                Doc::createImage($request->img, $doc->docable_id, $doc->docable_type, $doc->type_id, $doc->id);

            }
            $this->dataEdited($player, 'player_edited', 'تصویر با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');

        }
        $player = Player::where('id', $request->id)->first();
        $user = auth()->user();
        $this->authorize('ownItem', [User::class, $player, true]);

        if (isset($request->active)) {
            if ($request->active == true && $player->active == false) { //activate
                if (Carbon::now()->timestamp > $player->expires_at) {
                    return response()->json(['errors' => ['ابتدا بازیکن را انتخاب کنید و از بالای صفحه، اشتراک آن را تمدید کنید']], 422);
                }
                if ($user->role != 'ad' && $user->role != 'go') {
                    return response()->json(['errors' => ['در صف فعالسازی است و پس از بررسی توسط ادمین فعال خواهد شد']], 422);
                }

            }
            $player->active = $request->active;
            $player->save();
        } elseif ($request->name) {
            if ($player->name == $request->name) return null;
            $player->name = $request->name;
            $this->dataEdited($player, 'player_edited', 'نام با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');
        } elseif ($request->family) {
            if ($player->family == $request->family) return null;
            $player->family = $request->family;
            $this->dataEdited($player, 'player_edited', 'نام خانوادگی با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');


        } elseif ($request->phone && $request->phone_verify) {
            $player->phone = $request->phone;
            (new SMS())->deleteActivationSMS($request->phone);
            $player->save();

        } elseif (($request->is_man !== null) && $request->d && $request->y && $request->m && $request->weight && $request->height) {
            $date = (new Jalalian($request->y, $request->m, $request->d))->toCarbon();
            if ($date->timestamp == $player->born_at && $player->weight == $request->weight && $player->height == $request->height && $player->is_man == $request->is_man) return null;
            $player->weight = $request->weight;
            $player->height = $request->height;
            $player->is_man = $request->is_man;
            $player->born_at = $date;
            $this->dataEdited($player, 'player_edited', 'قد/وزن/جنسیت/تاریخ تولد با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');


        } elseif ($request->sport_id !== null) {
            if ($player->sport_id == $request->sport_id) return null;
            $player->sport_id = $request->sport_id;
            $this->dataEdited($player, 'player_edited', 'رشته ورزشی با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');


        } elseif ($request->province_id && $request->county_id) {
            if ($player->province_id == $request->province_id && $player->county_id == $request->county_id) return null;
            $player->province_id = $request->province_id;
            $player->county_id = $request->county_id;

            $this->dataEdited($player, 'player_edited', 'استان/شهر با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');

        } elseif ($request->description) {
            if ($player->description == $request->description) return null;
            $player->description = $request->description;
            $this->dataEdited($player, 'player_edited', 'توضیحات با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');

        }
    }

    protected function remove(Request $request)
    {

        $data = Player::where('id', $request->id)->first();
        if (!$this->authorize('ownItem', [User::class, $data, false]))
            return response()->json(['errors' => ['اجازه حذف را ندارید']], 422);

        foreach ($data->docs as $doc) {
            Doc::deleteFile($doc);
        }
        $data->delete();

    }

    protected function search(Request $request)
    {

//        $query = Report::query();
//        $request->validate([
//
//            'paginate' => 'sometimes|numeric',
//            'page' => 'sometimes|numeric',
//        ], []);


        $page = $request->page;
        $paginate = $request->paginate;
        $sport_id = $request->sport;
        $province_id = $request->province;
        $county_id = $request->county;
        $name = $request->name;
        $height_l = $request->height_l;
        $height_h = $request->height_h;
        $weight_l = $request->weight_l;
        $weight_h = $request->weight_h;
        $age_l = $request->age_l;
        $age_h = $request->age_h;
        $gender = $request->gender;
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


        $query = Player::query();

        if (is_numeric($sport_id))
            $query = $query->where('sport_id', $sport_id);
        if (is_numeric($province_id))
            $query = $query->where('province_id', $province_id);
        if (is_numeric($county_id))
            $query = $query->where('county_id', $county_id);
//
//
        if ($name) {
            foreach (explode(' ', $name) as $word) {
                $query = $query->where(function ($query) use ($word) {
                    $query->orWhere('name', 'LIKE', '%' . $word . '%')
                        ->orWhere('family', 'LIKE', '%' . $word . '%');
                });
            }
        }
        if ($height_l && is_numeric($height_l) && $height_l > 0)
            $query = $query->where('height', '>=', $height_l);
        if ($height_h && is_numeric($height_h) && $height_h < 300)
            $query = $query->where('height', '<=', $height_h);

        if ($weight_l && is_numeric($weight_l) && $weight_l > 0)
            $query = $query->where('weight', '>=', $weight_l);
        if ($weight_h && is_numeric($weight_h) && $weight_h < 500)
            $query = $query->where('weight', '<=', $weight_h);

//        dd(Carbon::now()->subYears($age_l)->toDateTimeString() . "\n" . Carbon::createFromTimestamp(Player::first()->born_at));

        if ($age_l && is_numeric($age_l) && $age_l > 0)
            $query = $query->where('born_at', '<=', Carbon::now()->subYears($age_l));
        if ($age_h && is_numeric($age_h) && $age_h < 100)
            $query = $query->where('born_at', '>=', Carbon::now()->subYears($age_h));

        if ($gender == 'm' || $gender == 'w') {

            $query = $query->where('is_man', ($gender == 'm' ? true : false));
        }

        if (!$user || !$panel) {

            $query = $query->where('active', true);
        } else {

            if ($panel && $user->role == 'us')
                $query = $query->where('user_id', $user->id);
            if ($panel && is_numeric($active))
                $query = $query->where('active', (boolean)$active);
            if ($user_id && $panel && ($user->role == 'ad' || $user->role == 'go'))
                $query = $query->where('user_id', $user_id);
        }
//
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
