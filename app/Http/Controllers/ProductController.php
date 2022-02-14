<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Doc;
use App\Models\Group;
use App\Models\Image;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Sport;
use App\Models\User;
use Carbon\Carbon;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    public function images(Request $request)
    {
        if ($request->p_id)
            return Image::where('type', 'p')->where('for_id', $request->p_id)->pluck('id');
    }

    public function groups(Request $request)
    {
        $level = $request->level;
        $showTree = $request->show_tree;
        $ids = $request->ids;

        if (!$ids)
            $ids = Group::where('level', 1)->distinct('parent')->pluck('parent');

        $query = Group::query();

        if ($showTree) {
            return $query->select('id', 'name')->whereIn('id', $ids)->orderByDesc('id')->get()->map(function ($data) {
                $data['childs'] = Group::where('parent', $data['id'])->select('id', 'name')->get()->map(function ($data) {
                    $data['selected'] = false;
                    return $data;
                });

                return $data;

            });
        }
        if ($level)
            $query->where('level' . $level);


        return $query->get();
    }

    public function create(Request $request)
    {

        $request->validate([

            'shop_id' => 'required',

            'group_id' => 'nullable|' . Rule::in(Sport::pluck('id')),

            'name' => 'required|string|max:100',
            'description' => 'required|string|max:1024',
            'price' => 'required|numeric' . ((int)$request->price > 0 ? "|digitsbetween:4,10|gt:discount_price" : "|gte:discount_price"),
            'discount_price' => 'required|numeric' . ((int)$request->discount_price > 0 ? "|digitsbetween:4,10|lte:price" : ''),
            'count' => 'required|numeric|min:0|digitsbetween:1,10',
            'tags' => 'nullable|string|max:100',
            'images' => 'required|array|min:1|max:' . \Helper::$product_image_limit,
            'images.*' => 'required|base64_image'/*.'|base64_size:2048'*/,
        ], [

            'shop_id.required' => 'انتخاب فروشگاه ضروری است',

            'group_id.required' => 'دسته بندی محصول ضروری است',
            'group_id.in' => 'دسته بندی نامعتبر است',
            'name.string' => 'نام باید متنی باشد',
            'name.required' => 'نام محصول ضروری است',
            'name.max' => 'حداکثر طول نام 100 باشد. طول فعلی: ' . mb_strlen($request->name),
            'description.required' => 'توضیحات محصول ضروری است',
            'description.string' => 'توضیحات باید متنی باشد',
            'description.max' => 'حداکثر طول توضیحات 1024 کلمه باشد. طول فعلی: ' . mb_strlen($request->description),
            'price.required' => 'قیمت  ضروری است',
            'price.numeric' => 'قیمت  اعداد انگلیسی باشد',
            'price.digitsbetween' => 'قیمت صفر باشد یا حداقل 4 رقم باشد',
//            'price.gt' => 'قیمت اصلی از قیمت حراج بیشتر باشد',
            'price.gte' => 'قیمت اصلی از قیمت با تخفیف بیشتر یا برابر باشد',
            'discount_price.required' => 'قیمت تخفیف  ضروری است',
            'discount_price.numeric' => 'قیمت  اعداد انگلیسی باشد',
            'discount_price.digitsbetween' => 'قیمت صفر باشد یا حداقل 4 رقم باشد',
            'discount_price.lte' => 'قیمت با تخفیف از قیمت اصلی کمتر یا برابر باشد',
            'count.required' => 'تعداد محصول ضروری است',
            'count.numeric' => 'تعداد محصول اعداد انگلیسی باشد',
            'count.min' => 'حداقل تعداد محصول صفر باشد',
            'count.digitsbetween' => 'تعداد ارقام نامعتبر است',
            'tags.required' => 'هشتگ های محصول ضروری است',
            'tags.string' => 'تگ های محصول متنی باشد',
            'tags.max' => 'حداکثر طول تگ ها 100 باشد. طول فعلی: ' . mb_strlen($request->tags),

            'images.required' => 'حداقل یک تصویر ضروری است',
            'images.array' => 'حداقل یک تصویر ضروری است',
            'images.min' => 'حداقل یک تصویر ضروری است',
            'images.max' => 'حداکثر تصاویر ' . \Helper::$product_image_limit . ' عدد است ',

            'images.*.image' => 'فرمت تصویر از نوع  jpg باشد',
            'images.*.mimes' => 'فرمت تصویر از نوع  jpg باشد',
            'images.*.max' => 'حداکثر حجم فایل 2 مگابایت باشد',


        ]);
        $user = auth()->user();
        $shop = Shop::find($request->shop_id);
        if (!$this->authorize('ownItem', [User::class, $shop, false]))
            throw ValidationException::withMessages(['name' => "مجاز نیستید"]);
        if ($shop->active == false)
            throw ValidationException::withMessages(['name' => "فروشگاه انتخابی غیر فعال است"]);

        $name = $request->name;

        $shop_id = $request->shop_id;
        $description = $request->description;
        $tags = $request->tags;

        $discount_price = $request->discount_price;
        $price = $request->price;
        $count = $request->count;
        $group_id = $request->group_id;

        $product = Product::create([
            'active' => false,
            'name' => $name,

            'group_id' => $group_id,
            'shop_id' => $shop_id,
            'description' => $description,
            'tags' => $tags,
            'price' => $price,
            'discount_price' => ($discount_price && $discount_price > 0) ? $discount_price : $price,

            'count' => $count,
        ]);
        if ($group_id) {
            $g = json_decode($shop->groups);
            if (!$g) $g = [];
            if (!in_array((int)$group_id, $g)) {
                array_push($g, (int)$group_id);
                $shop->groups = json_encode($g);
                $shop->save();
            }

        }
        foreach ($request->images as $image) {

            Doc::createImage($image, $product->id, Helper::$typesMap['products'], Helper::$docsMap['product']);
        }


//            $img->storeAs("public/shops", "$shop->id.jpg");

        \Telegram::log(Helper::$TELEGRAM_GROUP_ID, 'product_created', $shop);

//        $this->sendProductBanner($product);

        return redirect()->to('panel/products')->with('success-alert', 'محصول شما با موفقیت ساخته شد!');

    }

    protected function remove(Request $request)
    {
        $p = Product::where('id', $request->id)->firstOrNew();
        $shop = Shop::where('id', $p->shop_id)->first();

        if (!$this->authorize('ownItem', [User::class, $shop, false]))
            return response()->json(['errors' => ['اجازه حذف را ندارید']], 422);

        foreach ($p->docs as $doc) {
            Doc::deleteFile($doc);
        }
        $p->delete();

    }

    public function edit(Request $request)
    {


        $request->validate([
            'cmnd' => 'required_if:img,!=,null',
            'id' => 'required|numeric',
            'group_id' => 'sometimes|nullable|' . Rule::in(Sport::pluck('id')),
            'name' => 'sometimes|string|max:100',
            'description' => 'sometimes|string|max:1024',
            'price' => 'required_with:discount_price|numeric' . ((int)$request->price > 0 ? "|digitsbetween:4,10|gte:$request->discount_price" : "|gte:$request->discount_price"),
            'discount_price' => 'required_with:price|numeric' . ((int)$request->price > 0 ? "|digitsbetween:4,10|lte:$request->price" : 'lte:0'),
            'count' => 'sometimes|numeric|min:0|digitsbetween:1,10',
            'tags' => 'sometimes|string|max:100',
            'active' => 'sometimes|boolean',
//            'img' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
//            'img_id' => 'sometimes|' . Rule::in(Image::where('type', 'p')->where('for_id', $product->id)->pluck('id'))
        ], [
            'id.required' => 'شناسه ضروری است',
            'id.numeric' => 'شناسه عددی است',
            'cmnd.required' => 'نوع فرمان ضروری است',
            'group_id.in' => 'دسته بندی نامعتبر است',
            'name.string' => 'نام باید متنی باشد',
            'name.max' => 'حداکثر طول نام 100 باشد. طول فعلی: ' . mb_strlen($request->name),
            'description.string' => 'توضیحات باید متنی باشد',
            'description.max' => 'حداکثر طول توضیحات 1024 کلمه باشد. طول فعلی: ' . mb_strlen($request->description),
            'price.numeric' => 'قیمت با اعداد انگلیسی باشد',
            'price.digitsbetween' => 'قیمت صفر باشد یا حداقل 4 رقم باشد',
//            'price.gt' => 'قیمت اصلی از قیمت حراج بیشتر باشد',
            'price.gte' => 'قیمت اصلی از قیمت با تخفیف بیشتر باشد',
            'price.required_with' => 'قیمت اصلی و قیمت تخفیف ضروری است',
            'discount_price.numeric' => 'قیمت تخفیف با اعداد انگلیسی باشد',
            'discount_price.digitsbetween' => 'قیمت صفر باشد یا حداقل 4 رقم باشد',
            'discount_price.lte' => 'قیمت با تخفیف از قیمت اصلی کمتر باشد',
            'discount_price.required_with' => 'قیمت اصلی و قیمت تخفیف ضروری است',
            'count.numeric' => 'تعداد محصول اعداد انگلیسی باشد',
            'count.min' => 'حداقل تعداد صفر باشد',
            'count.digitsbetween' => 'تعداد ارقام نامعتبر است',
            'tags.string' => 'تگ های محصول متنی باشد',
            'tags.max' => 'حداکثر طول تگ ها 100 باشد. طول فعلی: ' . mb_strlen($request->tags),

            'img.image' => 'فایل از نوع تصویر باشد',
            'img.mimes' => 'فرمت تصویر از نوع  jpg باشد',
            'img.max' => 'حداکثر حجم فایل 2 مگابایت باشد',
            'img_id.in' => 'شناسه عکس نامعتبر است',

        ]);

        $id = $request->id;
        $user = auth()->user();


        $name = $request->name;
        $description = $request->description;
        $price = $request->price;
        $discount_price = $request->discount_price;
        $count = $request->count;
        $group_id = $request->group_id;
        $tags = $request->tags;
        $active = $request->active;

        if ($request->cmnd == 'delete-img') {

            $doc = Doc::find($request->id);
            $product = Product::where('id', $doc->docable_id)->firstOrNew();
            $shop = Shop::where('id', $product->shop_id)->first();

            if (!$this->authorize('ownItem', [User::class, $shop, false]))
                return response()->json(['errors' => ['تصویر متعلق به شما نیست']], 422);
            if (Doc::where('type_id', $request->type)->where('docable_id', $doc->docable_id)->count() < 2)
                return response()->json(['errors' => ['حداقل یک تصویر ضروری است']], 422);

            Doc::deleteFile($doc);
            return redirect()->back()->with('success-alert', 'تصویر با موفقیت حذف شد');

        } elseif ($request->cmnd == 'upload-img' && $request->img) {

            if ($request->replace) {

                $doc = Doc::find($request->id);
                $product = Product::where('id', $doc->docable_id)->firstOrNew();
                $shop = Shop::where('id', $product->shop_id)->first();

                if (!$this->authorize('ownItem', [User::class, $shop, false]))
                    return response()->json(['errors' => ['تصویر متعلق به شما نیست']], 422);
                Doc::createImage($request->img, $doc->docable_id, $doc->docable_type, $doc->type_id, $doc->id);

            } elseif ($request->type == Helper::$docsMap['product']) {
                $product = Product::where('id', $request->id)->firstOrNew();
                $shop = Shop::where('id', $product->shop_id)->first();

                if (!$this->authorize('ownItem', [User::class, $shop, false]))
                    return response()->json(['errors' => ['تصویر متعلق به شما نیست']], 422);

                if (Doc::where('type_id', $request->type)->where('docable_id', $product->id)->count() >= Helper::$product_image_limit)
                    return response()->json(['errors' => ['تعداد تصاویر بیش از حد مجاز (' . Helper::$product_image_limit . ') است']], 422);
                Doc::createImage($request->img, $product->id, Helper::$typesMap['products'], $request->type);

            }
            $this->dataEdited($product, 'product_edited', 'تصویر با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');


        }

        $product = Product::where('id', $request->id)->firstOrNew();
        $shop = Shop::where('id', $product->shop_id)->first();

        $this->authorize('ownItem', [User::class, $shop, true]);


        if (isset($request->active)) {
            if ($request->active == true && $product->active == false) { //activate
                if (Carbon::now()->timestamp > $shop->expires_at) {
                    return response()->json(['errors' => ['ابتدا فروشگاه را انتخاب کنید و از بالای صفحه، اشتراک آن را تمدید کنید']], 422);
                }
                if ($user->role != 'ad' && $user->role != 'go') {
                    return response()->json(['errors' => ['در صف فعالسازی است و پس از بررسی توسط ادمین فعال خواهد شد']], 422);
                }

            }
            $product->active = $request->active;
            $product->save();
        } elseif ($name) {
            if ($product->name == $request->name) return null;
            $product->name = $request->$product;
            $this->dataEdited($product, 'product_edited', 'نام با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');

        } elseif ($description) {
            if ($product->description == $request->description) return null;
            $product->description = $request->description;
            $this->dataEdited($product, 'product_edited', 'توضیحات با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');

        } elseif (is_numeric($price) && is_numeric($discount_price) && is_numeric($count)) {

            if ($product->price == $price && $product->discount_price == $discount_price && $product->count == $count && $product->group_id == $group_id) return null;
            $product->price = $price;
            $product->discount_price = $discount_price;
            $product->count = $count;
            $product->group_id = $group_id;

            if ($group_id) {
                $g = json_decode($shop->groups);
                if (!$g) $g = [];
                if (!in_array((int)$group_id, $g)) {
                    array_push($g, (int)$group_id);
                    $shop->groups = json_encode($g);
                    $shop->save();
                }

            }
            $this->dataEdited($product, 'product_edited', 'قیمت/دسته بندی/تعداد با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');

        } elseif ($tags) {
            if ($product->tags == $tags) return null;
            $product->tags = $tags;
            $this->dataEdited($product, 'product_edited', 'هشتگ با موفقیت ویرایش شد و در صف بررسی قرار گرفت!');

        }
    }

    protected function search(Request $request)
    {


        $page = $request->page;
        $paginate = $request->paginate;

        $name = $request->name;
        $shop_id = $request->shop;
        $price_l = $request->price_l;
        $price_h = $request->price_h;

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


        $query = Product::query();


        if ($name) {
            foreach (explode(' ', $name) as $word) {
                $query = $query->where(function ($query) use ($word) {
                    $query->orWhere('name', 'LIKE', '%' . $word . '%')
                        ->orWhere('tags', 'LIKE', '%' . $word . '%');
                });
            }
        }
        if ($price_l && is_numeric($price_l) && $price_l > 0) {

            $query = $query->where(function ($query) use ($price_l) {
                $query->orWhere('discount_price', '>=', $price_l)
                    ->orWhere('price', '>=', $price_l);
            });

        }
        if ($price_h && is_numeric($price_h) && $price_h < 100000000) {

            $query = $query->where(function ($query) use ($price_h) {
                $query->orWhere('discount_price', '<=', $price_h)
                    ->orWhere('price', '<=', $price_h);
            });

        }
        if ($shop_id && is_numeric($shop_id)) {

            $query = $query->where('shop_id', $shop_id);
        }
//        if ($user_id && is_numeric($user_id)) {
//
//            $query = $query->wherIn('shop_id', Shop::where('user_id', $user_id)->pluck('id'));
//        }
        if (!$user || !$panel) {

            $query = $query->where('active', true)->whereIn('shop_id', Shop::where('active', true)->pluck('id'));
        } else {

            if (!$panel)
                $query = $query->whereIn('shop_id', Shop::where('active', true)->pluck('id'));

            if ($panel && $user->role == 'us')
                $query = $query->whereIn('shop_id', Shop::where('user_id', $user->id)->pluck('id'));
            if (is_numeric($active))
                $query = $query->where('active', (boolean)$active);
            if ($user_id && $panel && ($user->role == 'ad' || $user->role == 'go'))
                $query = $query->whereIn('shop_id', Shop::where('user_id', $user_id)->pluck('id'));

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

    public function sendProductBanner($product)
    {

        $shop = Shop::where('id', $product->shop_id)->first();
        $channel = Channel::where('chat_id', "$shop->channel_address")->first();
        $tag = ($channel ? $channel->tag : '') . "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL . $channel->chat_username;

        $caption = ($product->discount_price > 0 ? "🔥 #حراج" : "") . PHP_EOL;
        $caption .= ' 🆔 ' . "کد محصول: #" . $product->id . PHP_EOL;
        $caption .= ' 🔻 ' . "نام: " . $product->name . PHP_EOL;
//                    $caption .= ' ▪️ ' . "تعداد موجود: " . $product->count . PHP_EOL;
        $caption .= ' 🔸 ' . "قیمت: " . ($product->price == 0 ? 'پیام دهید' : number_format($product->price) . ' ت ') . PHP_EOL;
        if ($product->discount_price > 0)
            $caption .= ' 🔹 ' . "قیمت حراج: " . number_format($product->discount_price) . ' ت ' . PHP_EOL;
        $caption .= ' 🔻 ' . "توضیحات: " . PHP_EOL . "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL . $product->description . PHP_EOL . "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $caption .= $product->tags . PHP_EOL;
        $caption .= $tag . PHP_EOL;
        $caption = \Telegram::MarkDown($caption);

        $images = [];

        foreach (Image::where('type', 'p')->where('for_id', $product->id)->get() as $item) {
            $images[] = ['type' => 'photo', 'media' => str_replace('https://', '', asset("storage/products/$item->id.jpg")), 'caption' => $caption, 'parse_mode' => 'Markdown',];
        }
        if (count($images) == 0) {
            if (Storage::exists("public/shops/$shop->id.jpg")) {
                \Telegram::sendPhoto(Helper::$channel, asset("storage/shops/$shop->id.jpg"), $caption, null, null);
                $res = \Telegram::sendPhoto($channel->chat_username, asset("storage/shops/$shop->id.jpg"), $caption, null, null);

            } else {
                \Telegram::sendMessage(Helper::$channel, $caption, null, null);
                $res = \Telegram::sendMessage($channel->chat_username, $caption, null, null);

            }
        } elseif (count($images) == 1) {

            $res = \Telegram::sendPhoto(Helper::$channel, $images[0]['media'], $caption, null, null);
            $res = sendPhoto($channel->chat_id, $images[0]['media'], $caption, null, null);
        } else {
            $images = [];
            foreach (Image::where('type', 'p')->where('for_id', $product->id)->get() as $item) {
                if ($caption) {
                    $images[] = ['type' => 'photo', 'media' => asset("storage/products/$item->id.jpg"), 'caption' => $caption, 'parse_mode' => 'Markdown',];
                    $caption = null;
                } else {

                    $images[] = ['type' => 'photo', 'media' => asset("storage/products/$item->id.jpg")];
                }

            }
            $res = \Telegram::sendMediaGroup(Helper::$channel, $images);
            $res = \Telegram::sendMediaGroup($channel->chat_id, $images);

        }

    }
}
