<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogDoc;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Telegram;

class BlogController extends Controller
{
    protected function find(Request $request)
    {
        return Blog::whereId($request->id)->with('docs')->first();
    }

    protected function create(Request $request)
    {
        if (!$this->authorize('createItem', [User::class, Blog::class, false]))
            return response()->json(['errors' => ['مجاز نیستید']], 422);


//        $date = Carbon::now();

        $request->validate([
            'title' => 'required|string|max:200',
            'summary' => 'nullable|string|max:500',
            'blocks' => 'required|',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string|max:255',
            'is_draft' => 'required|boolean',
            'published_at' => 'required|numeric|min:0',
            'img' => 'required|base64_image'/*|base64_size:20480'*/,

//            'video' => 'nullable|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:10240'
        ], [
            'title.required' => 'تیتر خبر  نمی تواند خالی باشد',
            'title.string' => 'تیتر خبر متنی باشد',
            'title.max' => 'تیتر خبر  حداکثر 200 حرف باشد',

            'summary.required' => 'خلاصه خبر  نمی تواند خالی باشد',
            'summary.string' => 'خلاصه خبر متنی باشد',
            'summary.max' => 'خلاصه خبر  حداکثر 500 حرف باشد',

            'content.required' => 'متن خبر نمی تواند خالی باشد',

            'tags.required' => 'تگ خبر  نمی تواند خالی باشد',
            'tags.string' => 'تگ خبر متنی باشد',
            'tags.max' => 'تگ خبر  حداکثر 255 حرف باشد',

            'is_draft.required' => 'وضعیت ضروری است',
            'is_draft.boolean' => 'وضعیت نا معتبر است',

            'published_at.required' => 'زمان انتشار نمی تواند خالی باشد',
            'published_at.numeric' => 'زمان انتشار عدد باشد',
            'published_at.min' => 'زمان انتشار حداقل ۰ باشد',

            'category_id.required' => 'دسته بندی خبر ضروری است',
            'category_id.exists' => 'دسته بندی خبر نا معتبر است',

            'img.required' => 'تصویر ضروری است',
            'img.base64_image' => 'فرمت تصویر نامعتبر است',
            'img.base64_size' => 'حداکثر حجم تصویر 1 مگابایت باشد',

        ]);

        DB::transaction(function () use ($request, & $user) {


            $blog = Blog::create([
                'user_id' => auth()->user()->id,
                'category_id' => $request->category_id,
                'title' => $request->title,
                'summary' => $request->summary,
                'content' => '{}',
                'tags' => $request->tags,
                'published_at' => Carbon::now()->addHours($request->published_at),
                'is_draft' => $request->is_draft,
                'active' => !$request->is_draft,

            ]);

            //create future image first
            $photo = BlogDoc::createImage($request->img, $blog->id, Helper::$docsMap['blog']);


            //save base64 images to file
            $blocks = [];

            foreach (json_decode($request->blocks) as $idx => $block) {
                $tmp = $block;
                if ($block->type == 'image') {
                    if (isset($block->data->file->url))
                        $tmp->data->file->url = BlogDoc::createImage($block->data->file->url, $blog->id, Helper::$docsMap['blog']);


                }
                $blocks[] = $tmp;

            }
            $blog->content = json_encode($blocks);
            $blog->save();

            if ($blog->active) {

                Telegram::log(Helper::$TELEGRAM_GROUP_ID, 'blog_created', $blog);

                if ($request->published_at == 0 || $request->published_at == null) {
                    $photo = url("$photo");
                    $link = url('/') . "/blog/$blog->id/" . str_replace(' ', '-', str_replace('/', '-', $blog->title));
                    $caption = " 🚩 " . $blog->title . PHP_EOL . $link;
                    if (str_contains(request()->url(), '.ir'))
                        Telegram::sendPhoto(Helper::$TELEGRAM_CHANNEL_ID, $photo, $caption);

                }
            }


            return redirect(url('panel/blog'))->with('success-alert', 'با موفقیت ثبت شد! با انتخاب آن می توانید اطلاعات ثبت شده را مشاهده و ویرایش کنید');

        });


    }

    protected function edit(Request $request)
    {
        if (isset($request->active)) {
            $blog = Blog::find($request->id);
            $this->authorize('editItem', [User::class, $blog, true]);
            $blog->active = $request->active;
            $blog->save();
            return;
        }

//        $date = Carbon::now();

        $request->validate([
            'id' => ['required', 'exists:blogs,id'],
            'title' => 'required|string|max:200',
            'summary' => 'nullable|string|max:500',
            'blocks' => 'required|',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'nullable|string|max:255',
            'is_draft' => 'required|boolean',
            'published_at' => 'required|numeric|min:0',
            'img' => 'required|base64_image|base64_size:20480',

//            'video' => 'nullable|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:10240'
        ], [
            'title.required' => 'تیتر خبر  نمی تواند خالی باشد',
            'title.string' => 'تیتر خبر متنی باشد',
            'title.max' => 'تیتر خبر  حداکثر 200 حرف باشد',

            'summary.required' => 'خلاصه خبر  نمی تواند خالی باشد',
            'summary.string' => 'خلاصه خبر متنی باشد',
            'summary.max' => 'خلاصه خبر  حداکثر 500 حرف باشد',

            'content.required' => 'متن خبر نمی تواند خالی باشد',

            'tags.required' => 'متن خبر  نمی تواند خالی باشد',
            'tags.string' => 'متن خبر متنی باشد',
            'tags.max' => 'متن خبر  حداکثر 255 حرف باشد',

            'is_draft.required' => 'وضعیت ضروری است',
            'is_draft.boolean' => 'وضعیت نا معتبر است',

            'published_at.required' => 'زمان انتشار نمی تواند خالی باشد',
            'published_at.numeric' => 'زمان انتشار عدد باشد',
            'published_at.min' => 'زمان انتشار حداقل ۰ باشد',

            'id.required' => 'شناسه خبر ضروری است',
            'id.exists' => 'شناسه خبر نا معتبر است',

            'category_id.required' => 'دسته بندی خبر ضروری است',
            'category_id.exists' => 'دسته بندی خبر نا معتبر است',

            'img.required' => 'تصویر ضروری است',
            'img.base64_image' => 'فرمت تصویر نامعتبر است',
            'img.base64_size' => 'حداکثر حجم تصویر 1 مگابایت باشد',

        ]);

        DB::transaction(function () use ($request, & $user) {
            $blog = Blog::find($request->id);
            if (!$this->authorize('editItem', [User::class, $blog, false]))
                return response()->json(['errors' => ['category_id' => ['اجازه ویرایش را ندارید']]], 422);

            $blog->category_id = $request->category_id;
            $blog->title = $request->title;
            $blog->summary = $request->summary;
            $blog->tags = $request->tags;
            $blog->published_at = $request->published_at > 0 ? Carbon::now()->addHours($request->published_at) : $blog->published_at;
            $blog->is_draft = isset($request->active) ? !$request->active : $request->is_draft;
            $blog->active = isset($request->active) ? $request->active : !$request->is_draft;

            $blog->save();

            //remove before images
            foreach ($blog->docs as $doc) {
                BlogDoc::deleteFile($doc);
            }

            //make future image first
            BlogDoc::createImage($request->img, $blog->id, Helper::$docsMap['blog']);

            //save base64 images to file
            $blocks = [];

            foreach (json_decode($request->blocks) as $idx => $block) {
                $tmp = $block;
                if ($block->type == 'image') {
                    if (isset($block->data->file->url))
                        $tmp->data->file->url = BlogDoc::createImage($block->data->file->url, $blog->id, Helper::$docsMap['blog']);


                }
                $blocks[] = $tmp;

            }
            $blog->content = json_encode($blocks);
            $blog->save();


            return redirect(url('panel/blog'))->with('success-alert', 'با موفقیت ویرایش شد!');

        });


    }

    protected function remove(Request $request)
    {

        $data = Blog::where('id', $request->id)->first();
        if (!$this->authorize('editItem', [User::class, $data, false]))
            return response()->json(['errors' => ['اجازه حذف را ندارید']], 422);

        foreach ($data->docs as $doc) {
            BlogDoc::deleteFile($doc);
        }
        $data->delete();

    }

    protected function search(Request $request)
    {


        $name = $request->name; //search in title,summary,content,tags
        $category_id = $request->category;
        $is_draft = $request->is_draft;


        $orderBy = $request->order_by;
        $paginate = $request->paginate;
        $page = $request->page;
        $dir = $request->dir;
        $panel = $request->panel;
        $active = $request->active;
        $user_id = $request->user;



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
            $orderBy = 'created_at';
        }


        $query = Blog::query();

        if (is_numeric($category_id))
            $query = $query->where('category_id', $category_id);


        if ($name) {
            foreach (explode(' ', $name) as $word) {
                $query = $query->where(function ($query) use ($word) {
                    $query->orWhere('title', 'LIKE', '%' . $word . '%')
                        ->orWhere('summary', 'LIKE', '%' . $word . '%')
                        ->orWhere('tags', 'LIKE', '%' . $word . '%');
                });
            }
        }

//        dd(Carbon::now()->subYears($age_l)->toDateTimeString() . "\n" . Carbon::createFromTimestamp(Player::first()->born_at));

        if (is_bool($is_draft))
            $query = $query->where('is_draft', $is_draft);


        if (!$panel) {
            $query = $query->where('active', true);
        } else {

            $user = auth()->user() ?: auth('api')->user();
            if ($panel && !$user)
                $query = $query->where('id', null);

            if ($panel && $user->role == 'us')
                $query = $query->where('user_id', $user->id);

            if ($panel && is_numeric($active))
                $query = $query->where('active', $active);
            if ($panel && is_bool($is_draft))
                $query = $query->where('is_draft', $is_draft);
            if ($user_id && $panel && ($user->role == 'ad' || $user->role == 'go'))
                $query = $query->where('user_id', $user_id);
        }
//
        if ($orderBy)
            $query = $query->orderBy($orderBy, $dir);

        $cols = ['id', 'user_id', 'category_id', 'title', 'summary', 'tags', 'active', 'is_draft', 'published_at',];
        $query = $query->select($cols)->with('docs');


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
