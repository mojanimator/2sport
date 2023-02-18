<?php

namespace App\Http\Controllers;

use App\Models\Doc;
use App\Models\Sport;
use App\Models\Table;
use App\Models\Tournament;
use App\Models\User;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use stdClass;

class TableController extends Controller
{
    protected function create(Request $request)
    {
        $this->authorize('createItem', [User::class, Table::class, true]);


//        $date = Carbon::now();

        $request->validate([
            'title' => 'required|string|max:100|unique:tables',
            'data' => 'required|array',
            'sport_id' => 'nullable|' . Rule::in(Sport::pluck('id')),
            'tournament_id' => [Rule::requiredIf(!$request->tournament_name), !$request->tournament_name ? Rule::in(Tournament::pluck('id')) : 'nullable'],
            'tournament_name' => [Rule::requiredIf($request->tournament_id == null), Rule::unique('tournaments', 'name'), 'max:100'],
            'tags' => 'nullable|max:150',

            'img' => Rule::requiredIf($request->tournament_id == null) . '|base64_image|base64_size:20480',

//            'video' => 'nullable|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:10240'
        ], [
            'title.required' => 'تیتر جدول نمی تواند خالی باشد',
            'title.string' => 'تیتر جدول متنی باشد',
            'title.max' => 'تیتر جدول حداکثر 100 حرف باشد',
            'title.unique' => 'تیتر جدول تکراری است',

            'tournament_id.required' => ' تورنومنت جدید و یا انتخاب تورنومنت ضروری است',
            'tournament_id.in' => 'تورنومنت انتخابی نامعتبر است',

            'tournament_name.required' => ' تورنومنت جدید و یا انتخاب تورنومنت ضروری است',
            'tournament_name.unique' => 'نام تورنومنت تکراری است',
            'tournament_name.max' => 'نام تورنومنت حداکثر 100 حرف باشد',

            'tags.max' => 'تگ حداکثر 150 حرف باشد',

            'tournament.required' => 'نام تورنومنت ضروری است',
            'tournament.max' => 'تیتر حداکثر 100 حرف باشد',

            'content.required' => 'جدول  نمی تواند خالی باشد',
            'content.json' => 'جدول نامعتبر است',


            'category_id.required' => 'دسته بندی جدول ضروری است',
            'category_id.in' => 'دسته بندی جدول نا معتبر است',
            'img.required' => 'تصویر تورنومنت ضروری است',
            'img.base64_image' => 'فرمت تصویر نامعتبر است',
            'img.base64_size' => 'حداکثر حجم فایل 1 مگابایت باشد',
        ]);
        if ($request->tournament_id != null && $request->tournament_name != null) {
            throw ValidationException::withMessages(['tournament' => "در صورت جدید بودن تورنومنت، انتخاب تورنومنت را خالی بگذارید"]);
        }

        $tournament = null;
        if ($request->tournament_name != null) {
            $tournament = Tournament::create(['name' => $request->tournament_name, 'sport_id' => $request->sport_id]);
            Tournament::createImage($request->img, $tournament->id);
        }
        $table = Table::create([

            'tournament_id' => $tournament ? $tournament->id : $request->tournament_id,
            'title' => $request->title,
            'content' => json_encode($request->data),
            'active' => true,
            'tags' => $request->tags,

        ]);
        \Telegram::log(\Helper::$TELEGRAM_GROUP_ID, 'table-created', $table);
        //make title image

        return redirect(url('panel/table'))->with('success-alert', 'با موفقیت ثبت شد! با انتخاب آن می توانید اطلاعات ثبت شده را مشاهده و ویرایش کنید');


    }

    protected function edit(Request $request)
    {
        $table = Table::find($request->id);
        if (isset($request->active)) {
            $this->authorize('editItem', [User::class, $table, true]);
            $table->active = $request->active;
            $table->save();
            return;
        }


//        $date = Carbon::now();

        $request->validate([
            'id' => ['required', 'exists:tables,id'],
            'title' => 'required|string|max:100|unique:tables,title,' . $request->id,
            'data' => 'required|array',
            'tournament_id' => [Rule::requiredIf(!$request->tournament_name), Rule::in(Tournament::pluck('id'))],
            'tournament_name' => [Rule::requiredIf($request->tournament_id == null), $table->tournament_id == $request->tournament_id ? ('unique:tournaments,name,' . $request->tournament_id) : '', 'max:100'],
            'tags' => 'nullable|max:150',
            'img' => Rule::requiredIf($request->tournament_id == null) . '|base64_image|base64_size:20480',

//            'video' => 'nullable|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:10240'
        ], [
            'title.required' => 'تیتر جدول نمی تواند خالی باشد',
            'title.string' => 'تیتر جدول متنی باشد',
            'title.max' => 'تیتر جدول حداکثر 100 حرف باشد',
            'title.unique' => 'تیتر جدول تکراری است',

            'tournament_id.required_if' => 'اطلاعات تورنومنت جدید و یا انتخاب تورنومنت ضروری است',
            'tournament_id.in' => 'تورنومنت انتخابی نامعتبر است',

            'tournament_name.required_if' => 'اطلاعات تورنومنت جدید و یا انتخاب تورنومنت ضروری است',
            'tournament_name.unique' => 'نام تورنومنت تکراری است',
            'tournament_name.max' => 'نام تورنومنت حداکثر 100 حرف باشد',

            'tags.max' => 'تگ حداکثر 150 حرف باشد',

            'content.required' => 'جدول  نمی تواند خالی باشد',
            'content.json' => 'جدول نامعتبر است',

            'data.img.required' => 'تصویر ضروری است',
            'data.img.base64_image' => 'فرمت تصویر نامعتبر است',
            'data.img.base64_size' => 'حداکثر حجم فایل 1 مگابایت باشد',

            'category_id.required' => 'دسته بندی جدول ضروری است',
            'category_id.in' => 'دسته بندی جدول نا معتبر است',

        ]);


        if ($table->tournament_id == $request->tournament_id) {
            Tournament::where('id', $table->tournament_id)->update(['name' => $request->tournament_name, 'sport_id' => $request->sport_id]);

            Tournament::createImage($request->img, $table->tournament_id);
        }
        $table->title = $request->title;
        $table->tags = $request->tags;
        $table->tournament_id = $request->tournament_id;
        $table->content = json_encode($request->data);
        $table->save();

        //make title image

        return redirect(url('panel/table/edit/' . $request->id))->with('success-alert', 'با موفقیت ویرایش شد!');


    }


    protected function remove(Request $request)
    {

        $data = Table::where('id', $request->id)->first();
        if (!$this->authorize('editItem', [User::class, $data, false]))
            return response()->json(['errors' => ['اجازه حذف را ندارید']], 422);
        $data->delete();

    }

    public function search(Request $request)
    {

        $tournament_id = $request->tournament_id;
        $group = $request->group;
        $name = $request->name; //search in title,summary,content,tags
        $sport_id = $request->sport;

        $orderBy = $request->order_by;
        $paginate = $request->paginate;
        $page = $request->page;
        $dir = $request->dir;
        $panel = $request->panel;
        $active = $request->active;
        $user_id = $request->user_id;


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


        $query = Table::query();

        if (is_numeric($sport_id))
            $query = $query->whereIntegerInRaw('tournament_id', Tournament::where('sport_id', $sport_id)->pluck('id'));

        if (is_numeric($tournament_id))
            $query = $query->where('tournament_id', $tournament_id);


        if ($name) {
            foreach (explode(' ', $name) as $word) {
                $query = $query->where(function ($query) use ($word) {
                    $query->orWhere('title', 'LIKE', '%' . $word . '%')
//                        ->orWhere('content->>tags', 'LIKE', '%' . $word . '%')
                        ->orWhere('tags', 'LIKE', '%' . $word . '%');
                });
            }
        }

//        dd(Carbon::now()->subYears($age_l)->toDateTimeString() . "\n" . Carbon::createFromTimestamp(Player::first()->born_at));


        if (!$panel) {
            $query = $query->where('active', true);
        } else {

            $user = auth()->user() ?: auth('api')->user();
            if ($panel && !$user)
                $query = $query->where('id', null);

            if ($panel && $user->role == 'us')
                $query = $query->where('user_id', $user->id);

            if ($panel && is_numeric($active))
                $query = $query->where('active', $active == 1 ? true : false);

            if ($user_id && $panel && ($user->role == 'ad' || $user->role == 'go'))
                $query = $query->where('user_id', $user_id);
        }
//
        if ($orderBy)
            $query = $query->orderBy($orderBy, $dir);

        $cols = ['id', 'title', 'tournament_id', /*'content->tags as tags',*/
            'tags', 'active', 'updated_at',];
        if ($request->with_content) {
            $cols[] = 'content->table->header as header';
            $cols[] = 'content->table->body as body';
        }
        $query = $query->select($cols);


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
