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
use stdClass;

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
        $this->authorize('createItem', [User::class, Product::class, true]);

        $request->validate([

            'shop' => 'required|',

            'group_id' => 'nullable|' . Rule::in(Sport::pluck('id')),

            'name' => 'required|string|max:100',
            'price' => 'required|numeric' . ((int)$request->price > 0 ? "|digitsbetween:4,10|gt:discount_price" : "|gte:discount_price"),
            'discount_price' => 'required|numeric' . ((int)$request->discount_price > 0 ? "|digitsbetween:4,10|lte:price" : ''),
            'count' => 'required|numeric|min:0|digitsbetween:1,10',
            'description' => 'nullable|string|max:1024',
            'tags' => 'nullable|string|max:100',

        ], [

            'shop.required' => 'انتخاب فروشگاه ضروری است',

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
            'price.gt' => 'قیمت اصلی از قیمت حراج بیشتر باشد',
            'price.gte' => 'قیمت اصلی از قیمت حراج بیشتر یا برابر باشد',
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


        ]);

        //request images after validating other fields
        if (isset($request->upload_pending))
            return response()->json(['resume' => true], 200);
//
        $request->validate([
            'images' => 'required|array|min:1|max:' . \Helper::$product_image_limit,
            'images.*' => 'required|base64_image'/*.'|base64_size:2048'*/,
        ], [


            'images.required' => 'حداقل یک تصویر ضروری است',
            'images.array' => 'حداقل یک تصویر ضروری است',
            'images.min' => 'حداقل یک تصویر ضروری است',
            'images.max' => 'حداکثر تصاویر ' . \Helper::$product_image_limit . ' عدد است ',

            'images.*.image' => 'فرمت تصویر از نوع  jpg باشد',
            'images.*.mimes' => 'فرمت تصویر از نوع  jpg باشد',
            'images.*.max' => 'حداکثر حجم فایل 2 مگابایت باشد',

        ]);

        $user = auth()->user();
        $shop = Shop::find($request->shop);

        if (!$this->authorize('editItem', [User::class, $shop, false])) {
            throw ValidationException::withMessages(['name' => "مجاز نیستید"]);
        }
        if ($shop->active == false) {
            throw ValidationException::withMessages(['name' => "فروشگاه انتخابی غیر فعال است"]);
        }
        $name = $request->name;

        $shop_id = $request->shop;
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

        \Telegram::log(Helper::$TELEGRAM_GROUP_ID, 'product_created', $product);

//        $this->sendProductBanner($product);
        if (str_contains($request->url(), '/api/'))
            return response()->json(['status' => 'success', 'url' => url('panel/products')]);

        return redirect()->to('panel/products')->with('success-alert', 'محصول شما با موفقیت ساخته شد!');

    }

    protected function remove(Request $request)
    {
        $p = Product::where('id', $request->id)->firstOrNew();
        $shop = Shop::where('id', $p->shop_id)->first();

        if (!$this->authorize('editItem', [User::class, $shop, false]))
            return response()->json(['errors' => ['اجازه حذف را ندارید']], 422);


        $group_id = $p->group_id;

        if ($group_id) {
            $g = json_decode($shop->groups);
            if (!$g) $g = [];
            if (Product::where('shop_id', $shop->id)->where('group_id', $group_id)->count() <= 1) {
                //remove from shop group ids if no more this product group   exists
                $arr = array_diff($g, [$group_id]);
                $shop->groups = json_encode($arr);
                $shop->save();
            }
            if (!in_array((int)$group_id, $g)) {
                array_push($g, (int)$group_id);
                $shop->groups = json_encode($g);
                $shop->save();
            }

        }

        foreach ($p->docs as $doc) {
            Doc::deleteFile($doc);
        }
        $p->delete();
        return $this->dataEdited($p, 'product_deleted', 'محصول با موفقیت حذف شد!');

    }

    public function edit(Request $request)
    {


        $request->validate([
            'cmnd' => 'required_if:img,!=,null',
            'id' => 'sometimes|numeric',
            'group_id' => 'sometimes|nullable|' . Rule::in(Sport::pluck('id')),
            'name' => 'sometimes|string|max:100',
            'description' => 'sometimes|string|max:1024',
            'price' => 'required_with:discount_price|numeric' . ((int)$request->price > 0 ? "|digitsbetween:4,10|gte:$request->discount_price" : "|gte:$request->discount_price"),
            'discount_price' => 'required_with:price|numeric' . ((int)$request->price > 0 ? "|digitsbetween:4,10|lte:$request->price" : 'lte:0'),
            'count' => 'sometimes|numeric|min:0|digitsbetween:1,10',
            'tags' => 'sometimes|string|max:100',
//            'active' => 'sometimes|boolean',

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
            'price.gte' => 'قیمت اصلی از قیمت حراج بیشتر باشد',
            'price.required_with' => 'قیمت اصلی و قیمت حراج ضروری است',
            'discount_price.numeric' => 'قیمت حراج با اعداد انگلیسی باشد',
            'discount_price.digitsbetween' => 'قیمت حراج صفر باشد یا حداقل 4 رقم باشد',
            'discount_price.lte' => 'قیمت حراج از قیمت اصلی کمتر باشد',
            'discount_price.required_with' => 'قیمت اصلی و قیمت حراج ضروری است',
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


        $shop_id = $request->shop_id;
        $name = $request->name;
        $description = $request->description;
        $price = $request->price;
        $discount_price = $request->discount_price;
        $count = $request->count;
        $group_id = $request->group_id;
        $tags = $request->tags;

        if ($request->cmnd == 'delete-img') {

            $doc = Doc::find($request->id);
            $product = Product::where('id', $doc->docable_id)->firstOrNew();
            $shop = Shop::where('id', $product->shop_id)->first();

            if (!$this->authorize('editItem', [User::class, $shop, false]))
                return response()->json(['errors' => ['تصویر متعلق به شما نیست']], 422);
            if (Doc::where('type_id', $request->type)->where('docable_id', $doc->docable_id)->count() < 2)
                return response()->json(['errors' => ['حداقل یک تصویر ضروری است']], 422);

            Doc::deleteFile($doc);
            return redirect()->back()->with('success-alert', 'تصویر با موفقیت حذف شد');

        } elseif ($request->cmnd == 'upload-img' && $request->img) {

            if ($request->replace) {

                $doc = Doc::find($request->id);
                if ($doc == null) {
                    $doc = new stdClass;
                    $doc->id = null;
                    $doc->docable_id = $request->data_id;
                    $doc->docable_type = Helper::$typesMap['products'];
                    $doc->type_id = $request->type ?: Helper::$docsMap['product'];
                }
                $product = Product::where('id', $doc->docable_id)->firstOrNew();
                $shop = Shop::where('id', $product->shop_id)->first();

                if (!$this->authorize('editItem', [User::class, $shop, false]))
                    return response()->json(['errors' => ['تصویر متعلق به شما نیست']], 422);
                Doc::createImage($request->img, $doc->docable_id, $doc->docable_type, $doc->type_id, $doc->id);

            } elseif ($request->type == Helper::$docsMap['product']) {
                $product = Product::where('id', $request->id)->firstOrNew();
                $shop = Shop::where('id', $product->shop_id)->first();

                if (!$this->authorize('editItem', [User::class, $shop, false]))
                    return response()->json(['errors' => ['تصویر متعلق به شما نیست']], 422);

                if (Doc::where('type_id', $request->type)->where('docable_id', $product->id)->count() >= Helper::$product_image_limit)
                    return response()->json(['errors' => ['تعداد تصاویر بیش از حد مجاز (' . Helper::$product_image_limit . ') است']], 422);
                Doc::createImage($request->img, $product->id, Helper::$typesMap['products'], $request->type);

            }
            return $this->dataEdited($product, 'product_edited', 'تصویر');


        }

        $product = Product::where('id', $request->id)->firstOrNew();
        $shop = Shop::where('id', $product->shop_id)->first();
        if ($shop_id && $shop->id != $shop_id)
            $shop = Shop::where('id', $shop_id)->first();


        $this->authorize('editItem', [User::class, $shop, true]);

        $active = isset($request->active) ? boolval($request->active) : null;

        if (isset($request->active)) {
            if ($active == true && $product->active == false) { //activate
                if (Carbon::now()->timestamp > $shop->expires_at) {
                    return response()->json(['errors' => ['ابتدا فروشگاه را انتخاب کنید و از بالای صفحه، اشتراک آن را تمدید کنید']], 422);
                }
//                if ($user->role != 'ad' && $user->role != 'go') {
//                    return response()->json(['errors' => ['در صف فعالسازی است و پس از بررسی توسط ادمین فعال خواهد شد']], 422);
//                }

            }
            return $this->dataEdited($product, 'product_edited', 'وضعیت', $active);

        }
        if ($name) {
            if ($product->name == $request->name) return null;
            $product->name = $request->name;
            return $this->dataEdited($product, 'product_edited', 'نام');

        }
        if ($shop_id) {
            if ($product->shop_id == $request->shop_id) return null;
            $product->shop_id = $request->shop_id;
            return $this->dataEdited($product, 'product_edited', 'نام فروشگاه محصول');

        }
        if ($description) {
            if ($product->description == $request->description) return null;
            $product->description = $request->description;
            return $this->dataEdited($product, 'product_edited', 'توضیحات');

        }
        if (is_numeric($price) && is_numeric($discount_price) && is_numeric($count)) {

            if ($product->price == $price && $product->discount_price == $discount_price && $product->count == $count && $product->group_id == $group_id) return null;
            $product->price = $price;
            $product->discount_price = $discount_price;
            $product->count = $count;
            $product->group_id = $group_id;

            if ($group_id) {
                $g = json_decode($shop->groups, true);
                if (!$g) $g = [];
                if (!in_array((int)$group_id, $g)) {
                    array_push($g, (int)$group_id);
                    $shop->groups = json_encode($g);
                    $shop->save();
                }

            }
            return $this->dataEdited($product, 'product_edited', 'قیمت/دسته بندی/تعداد');

        }
        if (is_numeric($price) || is_numeric($discount_price)) {

            if ($product->price != $price)
                $product->price = $price;
            if ($product->discount_price != $discount_price)
                $product->discount_price = $discount_price;

            return $this->dataEdited($product, 'product_edited', 'قیمت');

        }
        if (is_numeric($count)) {

            if ($product->count == $count) return null;

            $product->count = $count;

            return $this->dataEdited($product, 'product_edited', 'تعداد');

        }
        if (is_numeric($group_id)) {

            if ($product->group_id == $group_id) return null;

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
            return $this->dataEdited($product, 'product_edited', 'دسته بندی');

        }
        if ($tags) {
            if ($product->tags == $tags) return null;
//            foreach ($tags as $idx => $tag) {
//                $tag[$idx] = str_replace(' ', '_', trim($tag[$idx]));
//            }
            $product->tags = $tags;
            return $this->dataEdited($product, 'product_edited', 'هشتگ');

        }
    }

    protected function search(Request $request)
    {

        $id = $request->id;
        $page = $request->page;
        $paginate = $request->paginate;

        $name = $request->name;
        $shop_id = $request->shop;
        $group_id = $request->sport;
        $price_l = $request->price_l;
        $price_h = $request->price_h;

        $orderBy = $request->order_by;
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


        $query = Product::query();

        if (is_numeric($id))
            $query = $query->where('id', $id);

        if ($name) {
            foreach (explode(' ', $name) as $word) {
                $query = $query->where(function ($query) use ($word, $group_id) {
                    $query->orWhere('name', 'LIKE', '%' . $word . '%')
                        ->orWhere('tags', 'LIKE', '%' . $word . '%');

                    $group_id = json_decode($group_id);

                    if (is_array($group_id))
                        $query = $query->orWhereIn('group_id', $group_id);
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
        if ($group_id && is_numeric($group_id)) {

            $query = $query->where('group_id', $group_id);
        }
//        if ($user_id && is_numeric($user_id)) {
//
//            $query = $query->wherIn('shop_id', Shop::where('user_id', $user_id)->pluck('id'));
//        }
        if (!$panel) {

            $query = $query->where('active', true)->whereIntegerInRaw('shop_id', Shop::where('active', true)->pluck('id'));
        } else {

            $user = auth()->user() ?: auth('api')->user();
            if ($panel && !$user)
                $query = $query->where('id', null);


            if ($panel && $user->role == 'us')
                $query = $query->whereIntegerInRaw('shop_id', Shop::where('user_id', $user->id)->pluck('id'));
            if (is_numeric($active))
                $query = $query->where('active', (boolean)$active);
            if ($user_id && $panel && ($user->role == 'ad' || $user->role == 'go'))
                $query = $query->whereIntegerInRaw('shop_id', Shop::where('user_id', $user_id)->pluck('id'));

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
