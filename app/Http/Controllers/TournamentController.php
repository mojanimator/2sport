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
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TournamentController extends Controller
{
    protected function create(Request $request)
    {
        $this->authorize('createItem', [User::class, Table::class, true]);


//        $date = Carbon::now();

        $request->validate([
            'title' => 'required|string|max:100|unique:tournaments',
            'sport_id' => 'nullable|' . Rule::in(Sport::pluck('id')),
            'img' => 'required|base64_image|base64_size:20480',

//            'video' => 'nullable|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:10240'
        ], [
            'title.required' => 'عنوان نمی تواند خالی باشد',
            'title.string' => 'عنوان متنی باشد',
            'title.max' => 'عنوان حداکثر 100 حرف باشد',
            'title.unique' => 'عنوان تکراری است',

            'tournament.required' => 'نام تورنومنت ضروری است',
            'tournament.max' => 'نام تورنومنت حداکثر 100 حرف باشد',

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
            'tournament_id' => $request->tournament,
            'content' => json_encode($request->data),
            'active' => false,

        ]);

        //make title image

        return redirect(url('panel/table'))->with('success-alert', 'با موفقیت ثبت شد! با انتخاب آن می توانید اطلاعات ثبت شده را مشاهده و ویرایش کنید');


    }

    protected function edit(Request $request)
    {
        $tournament = Tournament::find($request->id);
        if (!$this->authorize('createItem', [User::class, Tournament::class, true]))
            return response()->json(['errors' => ['تصویر متعلق به شما نیست']], 422);

        if ($request->cmnd == 'upload-img' && $request->img) {

//                 id=tournament_id
//                 data_id=table_id
            if ($request->replace)
                Tournament::createImage($request->img, $tournament->id);


            return redirect(url('panel/table/edit/' . $request->data_id))->with('success-alert', 'با موفقیت ویرایش شد!');

        } elseif ($request->cmnd == 'delete-img') {

//                 id=tournament_id
//                 data_id=table_id
            $docType = \Helper::$docsMap['tournament'];
            if (Storage::exists("public/$docType/$tournament->id.jpg")) {
                Storage::delete("public/$docType/$tournament->id.jpg");
            }

            return redirect(url('panel/table/edit/' . $request->data_id))->with('success-alert', 'با موفقیت ویرایش شد!');

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


        //make title image

        return redirect(url('panel/table/edit/' . $request->id))->with('success-alert', 'با موفقیت ویرایش شد!');


    }


    protected function remove(Request $request)
    {

        $data = Tournament::where('id', $request->id)->first();
        if (!$this->authorize('editItem', [User::class, $data, false]))
            return response()->json(['errors' => ['اجازه حذف را ندارید']], 422);

        $tables = Table::where('tournament_id', $data->id)->delete();
        $docType = \Helper::$docsMap['tournament'];

        if (Storage::exists("public/$docType/$data->id.jpg")) {
            Storage::delete("public/$docType/$data->id.jpg");
        }

        $data->delete();
    }


    public function search(Request $request)
    {

        $id = $request->id;
        $name = $request->name; //search in title,summary,content,tags
        $sport_id = $request->sport;

        $orderBy = $request->order_by;
        $paginate = $request->paginate;
        $page = $request->page;
        $dir = $request->dir;
        $panel = $request->panel;
        $active = $request->active;


//        $user = auth()->user() ?: auth('api')->user();

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


        $query = Tournament::query();

        if (is_numeric($id))
            $query = $query->where('id', $id);


        if (is_numeric($sport_id))
            $query = $query->where('sport_id', $sport_id);


        if ($name) {
            foreach (explode(' ', $name) as $word) {
                $query = $query->where(function ($query) use ($word) {
                    $query->orWhere('name', 'LIKE', '%' . $word . '%');
                });

                $sport_id = json_decode($sport_id);

                if (is_array($sport_id))
                    $query = $query->orWhereIn('sport_id', $sport_id);
            }
        }

//        dd(Carbon::now()->subYears($age_l)->toDateTimeString() . "\n" . Carbon::createFromTimestamp(Player::first()->born_at));


        if (!$panel) {
            $query = $query->where('active', true);
        } else {

            if ($panel && is_numeric($active))
                $query = $query->where('active', $active == 1 ? true : false);
        }
//
        if ($orderBy)
            $query = $query->orderBy($orderBy, $dir);


        if ($request->with_tables) {
            $query = $query->with('tables');
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
