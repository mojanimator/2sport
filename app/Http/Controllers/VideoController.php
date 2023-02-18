<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogDoc;
use App\Models\Category;
use App\Models\County;
use App\Models\Doc;
use App\Models\Player;
use App\Models\Sport;
use App\Models\User;
use App\Models\Video;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Morilog\Jalali\Jalalian;

class VideoController extends Controller
{

    protected function create(Request $request)
    {
        if (!$this->authorize('createItem', [User::class, Blog::class, false]))
            return response()->json(['errors' => ['مجاز نیستید']], 422);

//        $date = Carbon::now();

        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string|max:2048',
            'category_id' => 'required|' . Rule::in(Category::where('type_id', \Helper::$categoryType['videos'])->pluck('id')),
            'tags' => 'nullable|string|max:255',
            'img' => 'required|base64_image'/*|base64_size:20480'*/,
            'video' => 'required_without:upload_pending|mimes:mp4' /*. ',m4v,avi,flv,mov'. '|max:10240'*/
        ], [
            'title.required' => 'عنوان ویدیو  نمی تواند خالی باشد',
            'title.string' => 'عنوان ویدیو متنی باشد',
            'title.max' => 'عنوان ویدیو  حداکثر 200 حرف باشد',

            'description.required' => 'توضیحات ضروری است',
            'description.string' => 'توضیحات متنی باشد',
            'description.max' => 'حداکثر طول توضیحات 2048 کلمه باشد. طول فعلی: ' . mb_strlen($request->description),


            'tags.required' => 'تگ ویدیو نمی تواند خالی باشد',
            'tags.string' => 'تگ ویدیو متنی باشد',
            'tags.max' => 'تگ ویدیو حداکثر 255 حرف باشد',

            'category_id.required' => 'دسته بندی ویدیو ضروری است',
            'category_id.in' => 'دسته بندی ویدیو وجود ندارد',

            'img.required' => 'پوستر ویدیو ضروری است',
            'img.base64_image' => 'فرمت پوستر ویدیو  نامعتبر است',
            'img.base64_size' => 'حداکثر حجم پوستر ویدیو  1 مگابایت باشد',

            'video.required_without' => 'ویدیو ضروری است',
            'video.mimes' => 'ویدیو ارسالی با فرمت mp4 باشد',
            'video.max' => 'حجم ویدیو حداکثر 50 مگابایت باشد',

        ]);
//request video after validating other fields
        if (isset($request->upload_pending))
            return response()->json(['resume' => true], 200);


        $video = Video::create([
            'user_id' => auth()->user()->id,
            'category_id' => $request->category_id,
            'duration' => $request->duration ? intval($request->duration) : null,
            'title' => $request->title,
            'description' => $request->description,
            'tags' => $request->tags,
            'active' => true,

        ]);

        Video::createImage($request->img, Helper::$docsMap['videos'], $video->id);
        Video::createVideo($request->file('video'), Helper::$docsMap['videos'], $video->id);
        \Telegram::log(Helper::$TELEGRAM_GROUP_ID, 'video_created', $video);
        return redirect(url('panel/videos'))->with('success-alert', 'با موفقیت ثبت شد! با انتخاب آن می توانید اطلاعات ثبت شده را مشاهده و ویرایش کنید');


    }


    protected function search(Request $request)
    {


        $name = $request->name; //search in title,tags
        $category_id = $request->category;


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


        $query = Video::query();

        if (is_numeric($category_id))
            $query = $query->where('category_id', $category_id);


        if ($name) {
            foreach (explode(' ', $name) as $word) {
                $query = $query->where(function ($query) use ($word) {
                    $query->orWhere('title', 'LIKE', '%' . $word . '%')
                        ->orWhere('tags', 'LIKE', '%' . $word . '%');
                });
            }
        }

//        dd(Carbon::now()->subYears($age_l)->toDateTimeString() . "\n" . Carbon::createFromTimestamp(Player::first()->born_at));


        if (!$panel) {
            $query = $query->where('active', true);
        } else {
            $user = auth()->user() ? auth()->user() : auth('api')->user();

            if ($panel && !$user)
                $query = $query->where('id', null);
            if ($panel && $user->role == 'us')
                $query = $query->where('user_id', $user->id);

            if ($panel && is_numeric($active))
                $query = $query->where('active', $active);
            if ($user_id && $panel && ($user->role == 'ad' || $user->role == 'go'))
                $query = $query->where('user_id', $user_id);
        }
//
        if ($orderBy)
            $query = $query->orderBy($orderBy, $dir);

        $cols = ['id', 'user_id', 'category_id', 'title', 'duration', 'views', 'active', 'created_at'];
        $query = $query->select($cols);


        $data = $query->paginate($paginate, ['*'], 'page', $page);

        return $data;
    }

    protected function edit(Request $request)
    {


        $request->validate([
            'id' => 'required',
            'title' => 'sometimes|string|max:200',
            'category_id' => 'sometimes|' . Rule::in(Category::where('type_id', \Helper::$categoryType['videos'])->pluck('id')),
            'tags' => 'sometimes|string|max:255',
            'img' => 'sometimes|base64_image'/*|base64_size:20480'*/,
            'description' => 'sometimes|string|max:2048',
            'video' => 'sometimes|mimes:mp4' /*. ',m4v,avi,flv,mov'. '|max:10240'*/
        ], [
            'id.required' => 'شناسه ضروری است',

            'title.required' => 'عنوان ویدیو  نمی تواند خالی باشد',
            'title.string' => 'عنوان ویدیو متنی باشد',
            'title.max' => 'عنوان ویدیو  حداکثر 200 حرف باشد',

            'description.required' => 'توضیحات ضروری است',
            'description.string' => 'توضیحات متنی باشد',
            'description.max' => 'حداکثر طول توضیحات 2048 کلمه باشد. طول فعلی: ' . mb_strlen($request->description),


            'tags.required' => 'تگ ویدیو نمی تواند خالی باشد',
            'tags.string' => 'تگ ویدیو متنی باشد',
            'tags.max' => 'تگ ویدیو حداکثر 255 حرف باشد. طول فعلی: ' . mb_strlen($request->tags),

            'category_id.required' => 'دسته بندی ویدیو ضروری است',
            'category_id.in' => 'دسته بندی ویدیو وجود ندارد',

            'img.required' => 'پوستر ویدیو ضروری است',
            'img.base64_image' => 'فرمت پوستر ویدیو  نامعتبر است',
            'img.base64_size' => 'حداکثر حجم پوستر ویدیو  1 مگابایت باشد',

            'video.required' => 'ویدیو ضروری است',
            'video.mimes' => 'ویدیو ارسالی با فرمت mp4 باشد',
            'video.max' => 'حجم ویدیو حداکثر 50 مگابایت باشد',

        ]);

        $video = Video::where('id', $request->id)->first();

        if (!$video)
            return response()->json(['errors' => ['ویدیو پیدا نشد']], 422);

        $this->authorize('editItem', [User::class, $video, true]);

//        if (!$this->authorize('editItem', [User::class, $video, false]))
//            return response()->json(['errors' => ['ویدیو متعلق به شما نیست']], 422);


        if ($request->cmnd == 'delete-vid') {
            return response()->json(['errors' => ['حداقل یک ویدیو ضروری است']], 422);

            if (!$this->authorize('editItem', [User::class, $video, false]))
                return response()->json(['errors' => ['ویدیو متعلق به شما نیست']], 422);
            if (Doc::where('type_id', $request->type)->where('docable_id', $doc->docable_id)->count() < 2)
                return response()->json(['errors' => ['حداقل یک ویدیو ضروری است']], 422);

            Video::deleteFile($request->id, 'mp4');

            return redirect()->back()->with('success-alert', 'ویدیو با موفقیت حذف شد');

        } elseif ($request->cmnd == 'upload-vid' && $request->video) {

            Video::createVideo($request->file('video'), Helper::$docsMap['videos'], $video->id);
            return $this->dataEdited($video, 'video_edited', 'فایل');

        } elseif ($request->cmnd == 'delete-img') {

            return response()->json(['errors' => ['حداقل یک تصویر ضروری است']], 422);

        } elseif ($request->cmnd == 'upload-img' && $request->img) {

            Video::createImage($request->img, Helper::$docsMap['videos'], $video->id);
            return $this->dataEdited($video, 'video_edited', 'تصویر');

        }

        $active = isset($request->active) ? boolval($request->active) : null;

        if (isset($request->active)) {
            return $this->dataEdited($video, 'video_edited', 'وضعیت', $active);
        }
        if ($request->title) {
            if ($video->title == $request->title) return null;
            $video->title = $request->title;
            return $this->dataEdited($video, 'video_edited', 'نام');
        }

        if ($request->category_id !== null) {
            if ($video->category_id == $request->category_id) return null;
            $video->category_id = $request->category_id;
            return $this->dataEdited($video, 'video_edited', 'دسته بندی');
        }

        if ($request->tags) {
            if ($video->tags == $request->tags) return null;
            $video->tags = $request->tags;
            return $this->dataEdited($video, 'video_edited', 'تگ');

        }
        if ($request->description) {
            if ($video->description == $request->description) return null;
            $video->description = $request->description;
            return $this->dataEdited($video, 'video_edited', 'توضیحات');

        }
    }
}
