<?php

namespace App\Http\Controllers;

use App\Models\Table;
use App\Models\User;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TableController extends Controller
{
    protected function create(Request $request)
    {
        $this->authorize('createItem', [User::class, Table::class, true]);


//        $date = Carbon::now();

        $request->validate([
            'title' => 'required|string|max:100|unique:tables',
            'data' => 'required|array',
            'category_id' => 'required|in:' . implode(',', array_values(\Helper::$tableType)),
            'tournament' => [Rule::requiredIf($request->category_id == \Helper::$tableType['تورنومنت'], 'max:100')],
            'data.img' => 'required|base64_image|base64_size:20480',

//            'video' => 'nullable|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:10240'
        ], [
            'title.required' => 'تیتر نمی تواند خالی باشد',
            'title.string' => 'تیتر متنی باشد',
            'title.max' => 'تیتر حداکثر 100 حرف باشد',
            'title.unique' => 'تیتر تکراری است',

            'tournament.required' => 'نام تورنومنت ضروری است',
            'tournament.max' => 'تیتر حداکثر 100 حرف باشد',

            'content.required' => 'جدول  نمی تواند خالی باشد',
            'content.json' => 'جدول نامعتبر است',


            'category_id.required' => 'دسته بندی جدول ضروری است',
            'category_id.in' => 'دسته بندی جدول نا معتبر است',
            'data.img.required' => 'تصویر ضروری است',
            'data.img.base64_image' => 'فرمت تصویر نامعتبر است',
            'data.img.base64_size' => 'حداکثر حجم فایل 1 مگابایت باشد',
        ]);


        $table = Table::create([

            'type_id' => $request->category_id,
            'title' => $request->title,
            'tournament' => $request->tournament,
            'content' => json_encode($request->data),
            'active' => false,

        ]);

        //make title image

        return redirect(url('panel/table'))->with('success-alert', 'با موفقیت ثبت شد! با انتخاب آن می توانید اطلاعات ثبت شده را مشاهده و ویرایش کنید');


    }

    protected function edit(Request $request)
    {
        $table = Table::find($request->id);
        if (isset($request->active)) {
            $this->authorize('ownItem', [User::class, $table, true]);
            $table->active = $request->active;
            $table->save();
            return;
        }


//        $date = Carbon::now();

        $request->validate([
            'id' => ['required', 'exists:tables,id'],
            'title' => 'required|string|max:100|unique:tables,title,' . $request->id,
            'data' => 'required|array',
            'category_id' => 'required|in:' . implode(',', array_values(\Helper::$tableType)),
            'tournament' => [Rule::requiredIf($request->category_id == \Helper::$tableType['تورنومنت'], 'max:100')],

            'data.img' => 'required|base64_image|base64_size:20480',

//            'video' => 'nullable|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:10240'
        ], [
            'title.required' => 'تیتر نمی تواند خالی باشد',
            'title.string' => 'تیتر متنی باشد',
            'title.max' => 'تیتر حداکثر 100 حرف باشد',
            'title.unique' => 'تیتر تکراری است',
            'tournament.required' => 'نام تورنومنت ضروری است',
            'tournament.max' => 'تیتر حداکثر 100 حرف باشد',

            'content.required' => 'جدول  نمی تواند خالی باشد',
            'content.json' => 'جدول نامعتبر است',

            'data.img.required' => 'تصویر ضروری است',
            'data.img.base64_image' => 'فرمت تصویر نامعتبر است',
            'data.img.base64_size' => 'حداکثر حجم فایل 1 مگابایت باشد',

            'category_id.required' => 'دسته بندی جدول ضروری است',
            'category_id.in' => 'دسته بندی جدول نا معتبر است',

        ]);

        $table->type_id = $request->category_id;
        $table->title = $request->title;
        $table->tournament = $request->tournament;
        $table->content = json_encode($request->data);


        $table->save();


        //make title image

        return redirect(url('panel/table/edit/' . $request->id))->with('success-alert', 'با موفقیت ویرایش شد!');


    }


    protected function remove(Request $request)
    {

        $data = Table::where('id', $request->id)->first();
        if (!$this->authorize('ownItem', [User::class, $data, false]))
            return response()->json(['errors' => ['اجازه حذف را ندارید']], 422);
        $data->delete();

    }

    public function search(Request $request)
    {

        $group = $request->group;
        $name = $request->name; //search in title,summary,content,tags
        $category_id = $request->category;

        $orderBy = $request->order_by;
        $paginate = $request->paginate;
        $page = $request->page;
        $dir = $request->dir;
        $panel = $request->panel;
        $active = $request->active;
        $user_id = $request->user_id;


        $user = auth()->user() ?: auth('api')->user();

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
            $orderBy = 'updated_at';
        }


        $query = Table::query();

        if (is_numeric($category_id))
            $query = $query->where('type_id', $category_id);


        if ($name) {
            foreach (explode(' ', $name) as $word) {
                $query = $query->where(function ($query) use ($word) {
                    $query->orWhere('title', 'LIKE', '%' . $word . '%')
                        ->orWhere('content->>tags', 'LIKE', '%' . $word . '%');
                });
            }
        }

//        dd(Carbon::now()->subYears($age_l)->toDateTimeString() . "\n" . Carbon::createFromTimestamp(Player::first()->born_at));


        if (!$user || !$panel) {
            $query = $query->where('active', true);
        } else {

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

        $cols = ['id', 'type_id', 'title', 'tournament', 'content->tags as tags', 'content->img as img', 'active', 'updated_at',];
        if ($request->with_content) {
            $cols[] = 'content->table->header as header';
            $cols[] = 'content->table->body as body';
        }
        $query = $query->select($cols);
        if ($group) {
            $query->get()->groupBy('tournament');
        }

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
