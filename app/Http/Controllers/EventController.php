<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Sport;
use App\Models\User;
use Carbon\Carbon;
use DateTimeZone;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Morilog\Jalali\Jalalian;

class EventController extends Controller
{
    protected function create(Request $request)
    {
        $request->validate([

            'title' => 'string|min:3|max:150',
            'team1' => 'nullable|string|min:3|max:100',
            'team2' => 'nullable|string|min:3|max:100',
            'score1' => 'nullable|string|max:10',
            'score2' => 'nullable|string|max:10',
            'sport' => 'required|' . Rule::in(Sport::pluck('id')),
            'status' => 'nullable|' . Rule::in(\Helper::$eventStatus),

            'y' => 'required|numeric|min:1400|max:9999',
            'm' => 'required|numeric|min:1|max:12',
            'd' => 'required|numeric|min:1|max:31',
            'mm' => 'required|numeric|between:0,59',
            'hh' => 'required|numeric|between:0,24',

            'details' => 'nullable|string|max:2048',

            'source' => 'nullable|max:100',
            'link' => 'nullable|starts_with:http',

//            'video' => 'nullable|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:10240'
        ], [

            'title.required' => 'عنوان نمی تواند خالی باشد',
            'title.string' => 'عنوان  نامعتبر است',
            'title.min' => 'عنوان  حداقل 3 حرف باشد',
            'title.max' => 'عنوان  حداکثر 150 حرف باشد',

            'team1.required' => 'آیتم اول نمی تواند خالی باشد',
            'team1.string' => 'آیتم اول  نامعتبر است',
            'team1.min' => 'آیتم اول  حداقل 3 حرف باشد',
            'team1.max' => 'آیتم اول  حداکثر 100 حرف باشد',


            'score1.max' => 'امتیاز آیتم اول  حداکثر 10 حرف باشد',
            'score2.max' => 'امتیاز آیتم دوم  حداکثر 10 حرف باشد',

            'team2.required' => 'آیتم دوم نمی تواند خالی باشد',
            'team2.string' => 'آیتم دوم  نامعتبر است',
            'team2.min' => 'آیتم دوم  حداقل 3 حرف باشد',
            'team2.max' => 'آیتم دوم  حداکثر 100 حرف باشد',

            'source.max' => 'منبع  حداکثر 100 حرف باشد',
            'link.max' => 'لینک  حداکثر 100 حرف باشد',
            'link.starts_with' => 'لینک با http شروع شود',

            'status.in' => 'وضعیت انتخابی نامعتبر است',

            'sport.required' => 'رشته ورزشی ضروری است',
            'sport.in' => 'رشته ورزشی نامعتبر است',
            'sport-rule_id.in' => 'پست رشته ورزشی نامعتبر است',


            'details.required' => 'جزییات ضروری است',
            'details.string' => 'جزییات متنی باشد',
            'details.max' => 'حداکثر طول جزییات 2048 کلمه باشد. طول فعلی: ' . mb_strlen($request->details),

            'mm.required' => 'دقیقه  ضروری است',
            'mm.numeric' => 'دقیقه نامعتبر است',
            'mm.between' => 'دقیقه بین 0 و 59 باشد',

            'hh.required' => 'ساعت  ضروری است',
            'hh.numeric' => 'ساعت نامعتبر است',
            'hh.between' => 'ساعت بین 0 و 24 باشد',


            'y.required' => 'سال  ضروری است',
            'y.numeric' => 'سال نامعتبر است',
            'y.min' => 'سال بزرگتر از 1400 باشد',
            'y.max' => 'سال 4 رقمی باشد',

            'm.required' => 'ماه  ضروری است',
            'm.numeric' => 'ماه  بین 1 تا 12 است',
            'm.min' => 'ماه  بین 1 تا 12 است',
            'm.max' => 'ماه  بین 1 تا 12 است',

            'd.required' => 'روز  ضروری است',
            'd.numeric' => 'روز  بین 1 تا 31 است',
            'd.min' => 'روز  بین 1 تا 31 است',
            'd.max' => 'روز  بین 1 تا 31 است',

            'img.required' => 'تصویر ضروری است',
            'img.base64_image' => 'فرمت تصویر نامعتبر است',
            'img.base64_size' => 'حداکثر حجم فایل 1 مگابایت باشد',


        ]);
        if (!$this->authorize('createItem', [User::class, Event::class, true]))
            throw ValidationException::withMessages(['title' => "مجاز نیستید"]);

        $t = (new Jalalian((int)$request->y, (int)$request->m, (int)$request->d, (int)$request->hh, (int)$request->mm, 0, new DateTimeZone('Asia/Tehran')))->toCarbon()->setTimezone(new DateTimeZone('utc'));
        $event = Event::create([
            'user_id' => auth()->user()->id,
            'title' => $request->title,
            'team1' => $request->team1,
            'team2' => $request->team2,
            'score1' => $request->score1,
            'score2' => $request->score2,
            'status' => $request->status,
            'sport_id' => $request->sport,
            'details' => $request->details,

            'link' => $request->link,
            'source' => $request->source,
            'time' => $t
        ]);

        \Telegram::log(Helper::$TELEGRAM_GROUP_ID, 'event_created', $event);
        return response()->json(['url' => url('panel/events')]);
    }

    protected function edit(Request $request)
    {
        $event = Event::find($request->id);

        if (!$this->authorize('createItem', [User::class, Event::class, true]))
            throw ValidationException::withMessages(['title' => "مجاز نیستید"]);

        $request->validate([

            'id' => 'required',
            'title' => 'string|min:3|max:150',
            'team1' => 'nullable|string|min:3|max:100',
            'team2' => 'nullable|string|min:3|max:100',
            'score1' => 'nullable|max:10',
            'score2' => 'nullable|max:10',
            'sport' => 'required|' . Rule::in(Sport::pluck('id')),
            'status' => 'nullable|' . Rule::in(\Helper::$eventStatus),

            'y' => 'required|numeric|min:1400|max:9999',
            'm' => 'required|numeric|min:1|max:12',
            'd' => 'required|numeric|min:1|max:31',
            'mm' => 'required|numeric|between:0,59',
            'hh' => 'required|numeric|between:0,24',

            'details' => 'nullable|string|max:2048',

            'source' => 'nullable|max:100',
            'link' => 'nullable|starts_with:http',

//            'video' => 'nullable|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:10240'
        ], [
            'title.required' => 'عنوان نمی تواند خالی باشد',
            'title.string' => 'عنوان  نامعتبر است',
            'title.min' => 'عنوان  حداقل 3 حرف باشد',
            'title.max' => 'عنوان  حداکثر 150 حرف باشد',

            'team1.required' => 'آیتم اول نمی تواند خالی باشد',
            'team1.string' => 'آیتم اول  نامعتبر است',
            'team1.min' => 'آیتم اول  حداقل 3 حرف باشد',
            'team1.max' => 'آیتم اول  حداکثر 100 حرف باشد',

            'team2.required' => 'آیتم دوم نمی تواند خالی باشد',
            'team2.string' => 'آیتم دوم  نامعتبر است',
            'team2.min' => 'آیتم دوم  حداقل 3 حرف باشد',
            'team2.max' => 'آیتم دوم  حداکثر 100 حرف باشد',

            'source.max' => 'منبع  حداکثر 100 حرف باشد',
            'link.max' => 'لینک  حداکثر 100 حرف باشد',
            'link.starts_with' => 'لینک با http شروع شود',

            'status.in' => 'وضعیت انتخابی نامعتبر است',

            'sport.required' => 'رشته ورزشی ضروری است',
            'sport.in' => 'رشته ورزشی نامعتبر است',
            'sport-rule_id.in' => 'پست رشته ورزشی نامعتبر است',


            'details.required' => 'جزییات ضروری است',
            'details.string' => 'جزییات متنی باشد',
            'details.max' => 'حداکثر طول جزییات 2048 کلمه باشد. طول فعلی: ' . mb_strlen($request->details),

            'mm.required' => 'دقیقه  ضروری است',
            'mm.numeric' => 'دقیقه نامعتبر است',
            'mm.between' => 'دقیقه بین 0 و 59 باشد',

            'hh.required' => 'ساعت  ضروری است',
            'hh.numeric' => 'ساعت نامعتبر است',
            'hh.between' => 'ساعت بین 0 و 24 باشد',


            'y.required' => 'سال  ضروری است',
            'y.numeric' => 'سال نامعتبر است',
            'y.min' => 'سال بزرگتر از 1400 باشد',
            'y.max' => 'سال 4 رقمی باشد',

            'm.required' => 'ماه  ضروری است',
            'm.numeric' => 'ماه  بین 1 تا 12 است',
            'm.min' => 'ماه  بین 1 تا 12 است',
            'm.max' => 'ماه  بین 1 تا 12 است',

            'd.required' => 'روز  ضروری است',
            'd.numeric' => 'روز  بین 1 تا 31 است',
            'd.min' => 'روز  بین 1 تا 31 است',
            'd.max' => 'روز  بین 1 تا 31 است',

            'img.required' => 'تصویر ضروری است',
            'img.base64_image' => 'فرمت تصویر نامعتبر است',
            'img.base64_size' => 'حداکثر حجم فایل 1 مگابایت باشد',


        ]);

        $t = (new Jalalian((int)$request->y, (int)$request->m, (int)$request->d, (int)$request->hh, (int)$request->mm, 0, new DateTimeZone('Asia/Tehran')))->toCarbon()->setTimezone(new DateTimeZone('utc'));

        $event->update([
            'user_id' => auth()->user()->id,
            'title' => $request->title,
            'team1' => $request->team1,
            'team2' => $request->team2,
            'score1' => $request->score1,
            'score2' => $request->score2,
            'status' => $request->status,
            'sport_id' => $request->sport,
            'details' => $request->details,
            'link' => $request->link,
            'source' => $request->source,
            'time' => $t,
        ]);

        \Telegram::log(\Helper::$TELEGRAM_GROUP_ID, 'event_edited', $event);


        if (!str_contains(request()->url(), 'api/'))
            return redirect()->back()->with('success-alert', 'رویداد ویرایش شد');
        else return response()->json(['status' => 'success', 'msg' => 'رویداد ویرایش شد']);

    }

    protected function remove(Request $request)
    {

        $data = Event::where('id', $request->id)->first();
        if (!$this->authorize('createItem', [User::class, Event::class, false]))
            return response()->json(['errors' => ['اجازه حذف را ندارید']], 422);

        $data->delete();
        if (!str_contains($request->url(), 'api/'))
            return redirect(url('panel/event'))->with('success-alert', 'با موفقیت حذف شد!');
        else return response()->json(['status' => 'success', 'msg' => 'با موفقیت حذف شد!']);

    }

    function search(Request $request)
    {


        $group = $request->group; //search in title,summary,content,tags
        $name = $request->name; //search in title,summary,content,tags
        $category_id = $request->sport;

        $orderBy = $request->order_by;
        $paginate = $request->paginate;
        $page = $request->page;
        $dir = $request->dir;
        $panel = $request->panel;
        $active = $request->active;
        $user_id = $request->user;

        $user = auth()->user() ?: auth('api')->user();

        if (!$paginate) {
            $paginate = 24;
        }
        if (!$page) {
            $page = 1;
        }
        if (!$dir) {
            $dir = 'DESC';
        }
        if (!$orderBy) {
            $orderBy = 'updated_at';
        }


        $query = Event::query();

        if (is_numeric($category_id))
            $query = $query->where('sport_id', $category_id);


        if ($name) {
            foreach (explode(' ', $name) as $word) {
                $query = $query->where(function ($query) use ($word) {
                    $query->orWhere('title', 'LIKE', '%' . $word . '%')
                        ->orWhere('team1', 'LIKE', '%' . $word . '%')
                        ->orWhere('team1', 'LIKE', '%' . $word . '%')
                        ->orWhere('source', 'LIKE', '%' . $word . '%');
                });
            }
        }

//        dd(Carbon::now()->subYears($age_l)->toDateTimeString() . "\n" . Carbon::createFromTimestamp(Player::first()->born_at));


        if ($panel && $user->role == 'us')
            $query = $query->where('user_id', $user->id);

        if ($panel && is_numeric($active))
            $query = $query->where('active', $active);

        if ($user_id && $panel && ($user->role == 'ad' || $user->role == 'go'))
            $query = $query->where('user_id', $user_id);

//
        if ($orderBy)
            $query = $query->orderBy($orderBy, $dir);


//        get last week  (today is in middle)
        if ($group) {
            $zone = new DateTimeZone('Asia/Tehran');
            $from = Carbon::now($zone)->subDays(3);
            $to = Carbon::now($zone)->addDays(3);
            $data = $query->whereBetween('time', [$from, $to])->get()->sortBy('time')->groupBy([function ($query) use ($zone) {
                return Jalalian::forge($query->time, $zone)->format('%A');
            }, 'title']);
            $today = Jalalian::forge(Carbon::now($zone))->format('%A');
            if (!key_exists($today, $data->all()))
                $data[$today] = [str_replace(' 0', ' ', Jalalian::now()->format('%A %d %B', 'Asia/Tehran')) => [['title' => '...', 'team1' => '', 'team2' => '', 'score1' => null, 'score2' => null, 'status' => '', 'time' => null]]];
            return response()->json(['today' => $today, 'days' => $data]);
//            $data = collect($data)->groupBy('time')->all();
        } else
            $data = $query->paginate($paginate, ['*'], 'page', $page);

//        foreach ($data as $idx => $item) {
//            $img = \App\Models\Image::on(env('DB_CONNECTION'))->where('type', 'p')->where('for_id', $item->id)->inRandomOrder()->first();
//            if ($img)
//                $item['img'] = $img->id . '.jpg';
//        }

        return $data;
    }
}
