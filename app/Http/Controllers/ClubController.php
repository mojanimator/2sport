<?php

namespace App\Http\Controllers;

use App\Mail\RegisterEditUserMail;
use App\Models\Club;
use App\Models\Coach;
use App\Models\County;
use App\Models\Doc;
use App\Models\Player;
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
use PHPUnit\TextUI\Help;
use SMS;

class ClubController extends Controller
{
    protected function create(Request $request)
    {

//        $date = Carbon::now();

        $sports = Sport::pluck('id')->toArray();

        $request->validate([
            'name' => 'string|min:3|max:100',
            'county_id' => 'required|' . Rule::in(County::pluck('id')),
            'province_id' => 'required|in:' . County::where('id', $request->county_id)->firstOrNew()->province_id,

            'phone' => 'required|numeric|digits:11|regex:/^09[0-9]+$/' . '|unique:clubs,phone',
            'phone_verify' => [Rule::requiredIf(function () use ($request) {
                return !auth()->user() || $request->phone != auth()->user()->phone;
            }), !auth()->user() || $request->phone != auth()->user()->phone ? Rule::exists('sms_verify', 'code')->where(function ($query) use ($request) {
                return $query->where('phone', $request->phone);
            }) : '',],
            'description' => 'nullable|string|max:1024',
            'address' => 'required|string|max:1024',
            'times' => 'required|json',
            'license' => 'required|base64_image'/*.'|base64_size:2048'*/,
//            'video' => 'nullable|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:10240'
            'images' => 'required|array|min:1|max:' . \Helper::$club_image_limit,
            'images.*' => 'required|base64_image'/*.'|base64_size:2048'*/,
        ], [
            'name.required' => 'نام باشگاه نمی تواند خالی باشد',
            'name.string' => 'نام باشگاه نامعتبر است',
            'name.min' => 'نام باشگاه حداقل 3 حرف باشد',
            'name.max' => 'نام باشگاه حداکثر 100 حرف باشد',

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

            'phone_verify.required' => 'کد تایید شماره همراه ضروری است',
            'phone_verify.exists' => 'کد تایید شماره همراه نامعتبر است',
            'phone.unique' => 'شماره تماس تکراری است',
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
            'times.json' => 'مقادیر برنامه کاری نامعتبر است',
            'times.min' => 'حداقل یک ردیف برنامه کاری ضروری است',


            'license.required' => 'تصویر جواز کسب ضروری است',
            'license.base64_image' => 'فرمت تصویر جواز کسب نامعتبر است',
            'license.base64_size' => 'حداکثر حجم فایل 2 مگابایت باشد',


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

        //validating times json
        $msg = null;
        foreach (json_decode($request->times) as $time) {

            if (!in_array($time->id, $sports)) {
                $msg = "مقادیر نوع ورزش کاری خالی و یا نامعتبر هستند";
                break;
            }
            if (!in_array($time->g, [0, 1])) {
                $msg = "مقادیر جنسیت خالی و یا نامعتبر هستند";
                break;
            }
            if (!in_array($time->fm, [0, 30])) {
                $msg = "مقادیر دقیقه شروع خالی و یا نامعتبر هستند";
                break;
            }
            if (!in_array($time->tm, [0, 30])) {
                $msg = "مقادیر دقیقه پایان خالی و یا نامعتبر هستند";
                break;
            }
            if (!($time->d >= 0 && $time->d < 8)) {
                $msg = "مقادیر روز هفته خالی و یا نامعتبر هستند";
                break;
            }
            if (!($time->fh > 0 && $time->fh < 25)) {
                $msg = "مقادیر ساعت شروع خالی و یا نامعتبر هستند";
                break;
            }
            if (!($time->th > 0 && $time->th < 25)) {
                $msg = "مقادیر ساعت پایان خالی و یا نامعتبر هستند";
                break;
            }


        }
        if ($msg)
            throw ValidationException::withMessages(['times' => "$msg"]);

        DB::transaction(function () use ($request, & $user) {
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
            }

            $club = Club::create([
                'user_id' => $user->id,
                'province_id' => $request->province_id,
                'county_id' => $request->county_id,
                'name' => $request->name,
                'active' => false,
                'phone' => $request->phone,
                'description' => $request->description,
                'address' => $request->address,
                'times' => $request->times,
                'phone_verified' => true,
//                'expires_at' => $data['ex_date'] ? CalendarUtils::createCarbonFromFormat('Y/m/d', $data['ex_date'])->addDays(1)->timezone('Asia/Tehran') : null,
            ]);

            //make profile image
            Doc::createImage($request->license, $club->id, Helper::$typesMap['clubs'], Helper::$docsMap['license']);
            foreach ($request->images as $image) {

                Doc::createImage($image, $club->id, Helper::$typesMap['clubs'], Helper::$docsMap['club']);
            }

            (new SMS())->deleteActivationSMS($request->phone);
            $user->setRefferal();
            if (!auth()->user())
                auth()->login($user);
            return redirect(url('panel/club'))->with('success-alert', 'با موفقیت ثبت شد! با انتخاب آن می توانید اطلاعات ثبت شده را مشاهده و ویرایش کنید');

        });


    }

    protected function edit(Request $request)
    {

        $sports = Sport::pluck('id')->toArray();
        $user = auth()->user();
        $request->validate([
            'name' => 'sometimes|string|min:3|max:100',
            'county_id' => 'required_with:province_id|' . Rule::in(County::pluck('id')),
            'province_id' => 'required_with:county_id|in:' . County::where('id', $request->county_id)->firstOrNew()->province_id,
            'location' => 'sometimes|string|max:100',
            'phone' => 'sometimes|numeric|digits:11|regex:/^09[0-9]+$/' . '|unique:users,phone,' . auth()->user()->id,
            'phone_verify' => ['required_with:phone', Rule::requiredIf(function () use ($request) {
                return $request->phone && ($request->phone != auth()->user()->phone);
            }), $request->phone != auth()->user()->phone ? Rule::exists('sms_verify', 'code')->where(function ($query) use ($request) {
                return $query->where('phone', $request->phone);
            }) : '',],
            'description' => 'sometimes|string|max:1024',
            'address' => 'required_with:province_id|string|max:1024',
            'times' => 'sometimes|json',
            'img' => 'sometimes|base64_image'/*.'|base64_size:2048'*/,
//            'video' => 'nullable|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:10240'
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
            'times.json' => 'مقادیر برنامه کاری نامعتبر است',
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
            $club = Club::where('id', $doc->docable_id)->first();
            if (!$this->authorize('ownItem', [User::class, $club, false]))
                return response()->json(['errors' => ['تصویر متعلق به شما نیست']], 422);
            if (Doc::where('type_id', $request->type)->where('docable_id', $doc->docable_id)->count() < 2)
                return response()->json(['errors' => ['حداقل یک تصویر ضروری است']], 422);

            Doc::deleteFile($doc);
            return redirect()->back()->with('success-alert', 'تصویر با موفقیت حذف شد');

        } elseif ($request->cmnd == 'upload-img' && $request->img) {


            if ($request->replace) {

                $doc = Doc::find($request->id);
                $club = Club::where('id', $doc->docable_id)->first();
                if (!$this->authorize('ownItem', [User::class, $club, false]))
                    return response()->json(['errors' => ['تصویر متعلق به شما نیست']], 422);
                Doc::createImage($request->img, $doc->docable_id, $doc->docable_type, $doc->type_id, $doc->id);

            } elseif ($request->type == Helper::$docsMap['club']) {
                $club = Club::where('id', $request->id)->first();
                if (!$this->authorize('ownItem', [User::class, $club, false]))
                    return response()->json(['errors' => ['باشگاه متعلق به شما نیست']], 422);
                if (Doc::where('type_id', $request->type)->where('docable_id', $club->id)->count() >= Helper::$club_image_limit)
                    return response()->json(['errors' => ['تعداد تصاویر بیش از حد مجاز (' . Helper::$club_image_limit . ') است']], 422);
                Doc::createImage($request->img, $club->id, Helper::$typesMap['clubs'], $request->type);

            }

            return redirect()->back()->with('success-alert', 'تصویر با موفقیت اضافه شد!');

        }
        $club = Club::where('id', $request->id)->first();

        $this->authorize('ownItem', [User::class, $club, true]);

        if (isset($request->active) && ($user->role == 'ad' || $user->role == 'go')) {
            $club->active = $request->active;
            $club->save();
        } elseif (isset($request->active) && $user->role == 'us') {
            if ($request->active == true && $club->active == false) {

                Helper::makePay('club',$club);
            }

        } elseif ($request->name) {
            $club->name = $request->name;
            $club->save();

        } elseif ($request->phone && $request->phone_verify) {
            $club->phone = $request->phone;
            (new SMS())->deleteActivationSMS($request->phone);
            $club->save();

        } elseif ($request->times) {
            $msg = null;
            foreach (json_decode($request->times) as $time) {

                if (!in_array($time->id, $sports)) {
                    $msg = "مقادیر نوع ورزش کاری خالی و یا نامعتبر هستند";
                    break;
                }
                if (!in_array($time->g, [0, 1])) {
                    $msg = "مقادیر جنسیت خالی و یا نامعتبر هستند";
                    break;
                }
                if (!in_array($time->fm, [0, 30])) {
                    $msg = "مقادیر دقیقه شروع خالی و یا نامعتبر هستند";
                    break;
                }
                if (!in_array($time->tm, [0, 30])) {
                    $msg = "مقادیر دقیقه پایان خالی و یا نامعتبر هستند";
                    break;
                }
                if (!($time->d >= 0 && $time->d < 8)) {
                    $msg = "مقادیر روز هفته خالی و یا نامعتبر هستند";
                    break;
                }
                if (!($time->fh > 0 && $time->fh < 25)) {
                    $msg = "مقادیر ساعت شروع خالی و یا نامعتبر هستند";
                    break;
                }
                if (!($time->th > 0 && $time->th < 25)) {
                    $msg = "مقادیر ساعت پایان خالی و یا نامعتبر هستند";
                    break;
                }


            }
            if ($msg)
                throw ValidationException::withMessages(['times' => "$msg"]);

            $club->times = $request->times;
            $club->save();

        } elseif ($request->province_id && $request->county_id && $request->address) {

            $club->province_id = $request->province_id;
            $club->location = $request->location;
            $club->county_id = $request->county_id;
            $club->address = $request->address;
            $club->save();
        } elseif ($request->description) {

            $club->description = $request->description;
            $club->save();
        }
    }

    protected function remove(Request $request)
    {
        $data = Club::where('id', $request->id)->first();
        if (!$this->authorize('ownItem', [User::class, $data, false]))
            return response()->json(['errors' => ['اجازه حذف را ندارید']], 422);

        foreach ($data->docs as $doc) {
            Doc::deleteFile($doc);
        }
        $data->delete();

    }

    protected function search(Request $request)
    {


        $page = $request->page;
        $paginate = $request->paginate;
        $sport_id = $request->sport;
        $province_id = $request->province;
        $county_id = $request->county;
        $name = $request->name;

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


        $query = Club::query();

        if (is_numeric($sport_id))
            $query = $query->whereJsonContains('times', ['id' => (int)$sport_id]);
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


//        dd(Carbon::now()->subYears($age_l)->toDateTimeString() . "\n" . Carbon::createFromTimestamp(Player::first()->born_at));


        if ($gender == 'm' || $gender == 'w') {

            $query = $query->whereJsonContains('times', ['g' => ($gender == 'm' ? 0 : 1)]);
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
