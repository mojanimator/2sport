<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\RegisterController;
use App\Mail\RegisterEditUserMail;
use App\Models\Blog;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Doc;
use App\Models\Player;
use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Laravel\Passport\Exceptions\OAuthServerException;
use function PHPSTORM_META\type;
use PHPUnit\TextUI\Help;
use SMS;

class UserController extends Controller
{
    public function __construct()
    {
//        $this->middleware('api')->only('get');
    }


    protected function remove(Request $request)
    {


        $request->validate([
            'id' => 'required|numeric',
        ], [
            'id.required' => 'شناسه ضروری است',
            'id.numeric' => 'شناسه نامعتبر است',

        ]);
        $user = User::find($request->id);
        if (!auth()->user()->can('editItem', [User::class, $user, false]))
            return response()->json('اجازه حذف این مورد را ندارید', 403);
        Schema::disableForeignKeyConstraints();

        if (is_array($request->attach))
            foreach ($request->attach as $data) {
                $tmp = explode("_", $data);
                if (count($tmp) != 2) continue;
                if ($tmp[0] == 'player')
                    $d = Player::find($tmp[1]);

                if ($tmp[0] == 'coach')
                    $d = Coach::find($tmp[1]);
                if ($tmp[0] == 'club')
                    $d = Club::find($tmp[1]);
                if ($tmp[0] == 'shop')
                    $d = Shop::find($tmp[1]);

                if (!$d) continue;
                if ($d instanceof Shop) {
                    foreach (Product::where('shop_id', $d->id)->get() as $da) {
                        foreach ($da->docs as $doc) {
                            Doc::deleteFile($doc);
                        }
                        $da->delete();
                    }
                }
                foreach ($d->docs as $doc) {
                    Doc::deleteFile($doc);
                }
                $d->delete();

            }
        $user->delete();
        Schema::enableForeignKeyConstraints();

        return redirect('/panel/users');
    }


    protected function create(Request $request)
    {
        $this->authorize('createItem', [User::class, User::class, true]);

        $request->validate([

            'name' => 'required|string|min:3|max:50',
            'family' => 'required|string|min:3|max:50',
            'username' => 'required|min:5|max:50|regex:/^[A-Za-z]+[A-Za-z0-9_][A-Za-z0-9]{1,28}$/|unique:users,username',
            'email' => ['nullable', 'string', 'email', 'min:6', 'max:50', 'unique:users,email'],
            'phone' => 'required|numeric|digits:11|regex:/^09[0-9]+$/' . '|unique:users,phone',
            'phone_verify' => ['required', Rule::exists('sms_verify', 'code')->where(function ($query) use ($request) {
                return $query->where('phone', $request->phone);
            }),],
            'password' => 'nullable|string|min:6|max:50|confirmed',
            'sheba' => 'nullable|numeric|digits:24',
            'cart' => 'nullable|numeric|digits:16',
            'role' => 'required|' . Rule::in(array_keys(\Helper::$roles)),
        ],
            [
                'id.exists' => 'کاربر نامعتبر است',
                'name.required' => 'نام ضروری است',
                'name.string' => 'نام نمی تواند عدد باشد',
                'name.min' => 'نام حداقل 3 حرف باشد',
                'name.max' => 'نام حداکثر 50 حرف باشد',

                'family.required' => 'نام خانوادگی ضروری است',
                'family.string' => 'نام خانوادگی  نمی تواند عدد باشد',
                'family.min' => 'نام خانوادگی حداقل 3 حرف باشد',
                'family.max' => 'نام خانوادگی حداکثر 50 حرف باشد',

                'username.required' => 'نام کاربری ضروری است',
                'username.min' => 'طول نام کاربری حداقل 5 باشد',
                'username.max' => 'طول نام کاربری حداکثر 50 باشد',
                'username.unique' => 'نام کاربری تکراری است',
//            'username.alpha_dash' => 'نام کاربری فقط شامل حروف، عدد و - و _ باشد',
                'username.regex' => 'نام کاربری با حروف انگلیسی شروع شود و می تواند شامل عدد و _  باشد',
                'email.email' => 'ایمیل نامعتبر است',
                'email.min' => 'ایمیل حداقل 6 حرف باشد',
                'email.max' => 'ایمیل حداکثر 50 حرف باشد',
                'email.unique' => 'ایمیل تکراری است',

                'phone.required' => 'شماره تماس نمی تواند خالی باشد',
                'phone.numeric' => 'شماره تماس باید عدد باشد',
                'phone.digits' => 'شماره تماس  11 رقم و با 09 شروع شود',
                'phone.regex' => 'شماره تماس  11 رقم و با 09 شروع شود',
                'phone.unique' => 'شماره تماس تکراری است',

                'phone_verify.required' => 'کد تایید شماره همراه ضروری است',
                'phone_verify.required_with' => 'کد تایید شماره همراه ضروری است',
                'phone_verify.required_if' => 'کد تایید شماره همراه ضروری است',
                'phone_verify.exists' => 'کد تایید شماره همراه نامعتبر است',

                'sheba.numeric' => 'شماره شبا باید عدد باشد',
                'sheba.digits' => 'شماره شبا 24 رقم باشد',
                'cart.numeric' => 'شماره کارت باید عدد باشد',
                'cart.digits' => 'شماره کارت 16 رقم باشد',

                'role.required' => 'نقش کاربر ضروری است',
                'role.in' => 'نقش کاربر نامعتبر است',

            ]);
        $token = bin2hex(openssl_random_pseudo_bytes(30));
        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'family' => $request->family,
            'email' => $request->email,
            'cart' => $request->cart,
            'sheba' => $request->sheba,
            'role' => $request->role,
            'phone' => f2e($request->phone),
            'password' => isset($request->password) ? Hash::make(f2e($request->password)) : null,
            'score' => \Helper::$initScore,
            'remember_token' => $token,
            'active' => true,
            'phone_verified' => true,
            'ref_code' => User::makeRefCode(),
            'agency_id' => auth()->user()->agency_id,
//                'expires_at' => $data['ex_date'] ? CalendarUtils::createCarbonFromFormat('Y/m/d', $data['ex_date'])->addDays(1)->timezone('Asia/Tehran') : null,
        ]);
        (new SMS())->deleteActivationSMS($request->phone);
        \Telegram::log(\Helper::$TELEGRAM_GROUP_ID, 'user_created', $user);

        if ($user->email)
            Mail::to($user->email)->queue(new RegisterEditUserMail($token, 'register'));


        return response(['id' => $user->id]);
    }

    protected function edit(Request $request)
    {
        $user = User::find($request->id);

        $this->authorize('editItem', [User::class, $user, true]);


        $request->validate([

            'id' => 'sometimes|exists:users,id',
            'name' => 'sometimes|string|min:3|max:50',
            'family' => 'sometimes|string|min:3|max:50',
            'sheba' => 'sometimes|nullable|numeric|digits:24',
            'cart' => 'sometimes|nullable|numeric|digits:16',
            'username' => 'sometimes|min:5|max:50|regex:/^[A-Za-z]+[A-Za-z0-9_][A-Za-z0-9]{1,28}$/|unique:users,username',
            'email' => ['sometimes', 'email', 'min:6', 'max:50', Rule::unique('users')->ignore($user->id)],
            'phone' => 'sometimes|numeric|digits:11|regex:/^09[0-9]+$/' . '|unique:users,phone,' . auth()->user()->id,
            'phone_verify' => ['required_with:phone', Rule::exists('sms_verify', 'code')->where(function ($query) use ($request) {
                return $query->where('phone', $request->phone);
            }),],
            'role' => 'sometimes|' . Rule::in(array_keys(\Helper::$roles)),
        ],
            [
                'id.exists' => 'کاربر نامعتبر است',
                'name.string' => 'نام  نمی تواند عدد باشد',
                'name.min' => 'نام  حداقل 3 حرف باشد',
                'name.max' => 'نام  حداکثر 50 حرف باشد',

                'family.string' => 'نام خانوادگی  نمی تواند عدد باشد',
                'family.min' => 'نام خانوادگی حداقل 3 حرف باشد',
                'family.max' => 'نام خانوادگی حداکثر 50 حرف باشد',


                'username.min' => 'طول نام کاربری حداقل 5 باشد',
                'username.max' => 'طول نام کاربری حداکثر 50 باشد',
                'username.unique' => 'نام کاربری تکراری است',
//            'username.alpha_dash' => 'نام کاربری فقط شامل حروف، عدد و - و _ باشد',
                'username.regex' => 'نام کاربری با حروف انگلیسی شروع شود و می تواند شامل عدد و _  باشد',
                'email.email' => 'ایمیل نامعتبر است',
                'email.min' => 'ایمیل حداقل 6 حرف باشد',
                'email.max' => 'ایمیل حداکثر 50 حرف باشد',
                'email.unique' => 'ایمیل تکراری است',

                'phone.required' => 'شماره تماس نمی تواند خالی باشد',
                'phone.numeric' => 'شماره تماس باید عدد باشد',
                'phone.digits' => 'شماره تماس  11 رقم و با 09 شروع شود',
                'phone.regex' => 'شماره تماس  11 رقم و با 09 شروع شود',
                'phone.unique' => 'شماره تماس تکراری است',

                'phone_verify.required' => 'کد تایید شماره همراه ضروری است',
                'phone_verify.required_with' => 'کد تایید شماره همراه ضروری است',
                'phone_verify.required_if' => 'کد تایید شماره همراه ضروری است',
                'phone_verify.exists' => 'کد تایید شماره همراه نامعتبر است',

                'sheba.numeric' => 'شماره شبا باید عدد باشد',
                'sheba.digits' => 'شماره شبا 24 رقم باشد',
                'cart.numeric' => 'شماره کارت باید عدد باشد',
                'cart.digits' => 'شماره کارت 16 رقم باشد',
            ]);

//


        if ($request->name) {
            if ($user->name == $request->name) return null;
            $user->name = $request->name;
            $this->dataEdited($user, 'user_edited', 'نام با موفقیت ویرایش شد !');
        }
        if ($request->family) {
            if ($user->family == $request->family) return null;
            $user->family = $request->family;
            $this->dataEdited($user, 'user_edited', 'نام خانوادگی با موفقیت ویرایش شد !');

        } elseif ($request->username) {
            if ($user->username == $request->username) return null;
            $user->username = f2e($request->username);
            $this->dataEdited($user, 'user_edited', 'نام کاربری با موفقیت ویرایش شد  !');
        } elseif ($request->email) {
            $emailChanged = $user->email != $request->email ? true : false;
            if ($emailChanged || !$user->email_verified) {
                $user->email = $request->email;
                $user->email_verified = false;
                $user->remember_token = bin2hex(openssl_random_pseudo_bytes(30));
                $user->save();
                Mail::to($request->email)->queue(new RegisterEditUserMail($user->remember_token, 'edit'));

                return response()->json(['res' => 'لینک تایید برای شما ارسال شد. وارد ایمیل خود شده و روی لینک کلیک کنید.'], 200);
                return redirect()->back()->with('success-alert', 'لینک تایید ایمیل به شما ارسال شد. وارد ایمیل خود شده و روی لینک کلیک کنید.');
            }

//                return redirect()->to('panel/user-settings')->with('success-alert', 'لینک تایید ایمیل به شما ارسال شد. وارد ایمیل خود شده و روی لینک کلیک کنید.');
        } elseif ($request->phone && $request->phone_verify) {
            $user->phone = $request->phone;
            (new SMS())->deleteActivationSMS($request->phone);
            $user->save();

        } elseif ($request->sheba) {
            if ($user->sheba == $request->sheba) return null;
            $user->sheba = $request->sheba;
            $this->dataEdited($user, 'user_edited', 'شبا با موفقیت ویرایش شد !');
        } elseif ($request->cart) {
            if ($user->cart == $request->cart) return null;
            $user->cart = $request->cart;
            $this->dataEdited($user, 'user_edited', 'کارت با موفقیت ویرایش شد !');
        } elseif (isset($request->active)) {
            $user->active = $request->active;
            $this->dataEdited($user, 'user_edited', ' با موفقیت ویرایش شد !');

        } elseif ($request->role || $request->agency_id) {
            $user->role = $request->role;
            $this->authorize('editItem', [User::class, $user, true]);
            if (in_array(auth()->user()->role, ['ag', 'aa']))
                $user->agency_id = auth()->user()->agency_id;
            elseif (in_array(auth()->user()->role, ['go', 'ad']))
                $user->agency_id = $request->agency_id;

            $this->dataEdited($user, 'user_edited', ' با موفقیت ویرایش شد !');
        }

        return response()->json(['status' => 'success']);
    }

    protected function search(Request $request)
    {


        $page = $request->page;
        $paginate = $request->paginate;
        $search = $request->name;
        $orderBy = $request->order_by;
        $dir = $request->dir;
        $panel = $request->panel;
        $active = $request->active;

        $user = auth()->user();


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
//        $roles = [];
//        foreach (\Helper::$roles as $role => $name) {
//            if ($user->can('createItem', [User::class, User::class, false, (object)['role' => $role]]))
//                $roles[] = $role;
//        }

        $query = User::query();

        if (isset($user->agency_id))
            $query = $query->where('agency_id', $user->agency_id);
        if (isset($active))
            $query = $query->where('active', (boolean)$active);

        if (isset($search)) {
            foreach (explode(' ', $search) as $word) {
                $query = $query->where(function ($query) use ($word) {
                    $query->orWhere('name', 'LIKE', '%' . $word . '%')
                        ->orWhere('family', 'LIKE', '%' . $word . '%')
                        ->orWhere('username', 'LIKE', '%' . $word . '%')
                        ->orWhere('phone', 'LIKE', '%' . $word . '%')
                        ->orWhere('email', 'LIKE', '%' . $word . '%');
                });
            }
        }


//        if (isset($roles))
//            $query = $query->whereIn('role', $roles);

//
        if ($orderBy)
            $query = $query->orderBy($orderBy, $dir);


        $data = $query->paginate($paginate, ['*'], 'page', $page);

//        foreach ($data as $idx => $item) {
//            $img = \App\Models\Image::on(env('DB_CONNECTION'))->where('type', 'p')->where('for_id', $item->id)->inRandomOrder()->first();
//            if ($img)
//                $item['img'] = $img->id . '.jpg';
//        }

        return $data;
    }

    function get(Request $request)
    {
        $user = auth()->user();
        $info = [];
        $ref = [];
        if ($user) {
            $shops = Shop::where('user_id', $user->id)->select('id', 'name', 'user_id')->orDerByDesc('id')->get();
            $info = [

                'player' => Player::where('user_id', $user->id)->count(),
                'coach' => Coach::where('user_id', $user->id)->count(),
                'club' => Club::where('user_id', $user->id)->count(),
                'shop' => count($shops),
                'shops' => $shops,

            ];
            if (in_array($user->role, ['go', 'ad', 'bl']))
                $info = ['blog' => Blog::where('user_id', $user->id)->count()] + $info;

//            if (!in_array($user->role, ['go', 'ad'])) {
            $rf = new RefController();
            $ref = $rf->search();
//            }
        }

        return response()->json(['user' => $user, 'info' => $info, 'ref' => $ref,], 200);
    }

    public function login(Request $request)
    {
        $data = $request->all();
        if (!$data['phone'] || !$data['phone_verify'])
            return response()->json(['res' => 'LOGIN_FAIL', 'status' => 400]);

        $user = User::where('phone', $data['phone'])->first();
        if (!$user)
            return $this->registerAPI($data);

        $http = new
        \GuzzleHttp\Client([/*'base_uri' => 'http://localhost:81/_laravelProjects/magnetgram/public/',*/
        ]);

        if (!str_contains(url('/'), '.ir'))
            $url = 'http://localhost:81/_laravelProjects/2sport/public/oauth/token';
        else
            $url = route('passport.token');
        try {
            $response = $http->post(
//                route('passport.token')
            // 'oauth/token'
                $url
                , [

                'headers' => ['cache-control' => 'no-cache',
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'password' => @$data['phone_verify'],
                    'username' => @$data['phone'],
                ]
            ]);

            $res = json_decode($response->getBody());
            return response()->json(['access_token' => $res->access_token, 'user' => $user], 200);
        } catch (\Guzzlehttp\Exception\BadResponseException $e) {
//            $m = json_decode($e->getResponse()->getBody());
            return response()->json(['res' => 'LOGIN_FAIL'], 401);

        } catch (OAuthServerException $e) {
            return response()->json(['res' => 'LOGIN_FAIL',], $e->getCode());


        } catch (\Exception $e) {
            return response()->json(['res' => 'LOGIN_FAIL',], $e->getCode());


        }
    }

    public function refreshToken()
    {
        $http = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:81/_laravelProjects/ashayer/public/',
        ]);

        $response = $http->post('oauth/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => 'the-refresh-token',
                'client_id' => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'scope' => '',
            ],
        ]);

        return json_decode((string)$response->getBody(), true); //return new token and refresh token
    }

    // public function getUser()
    // {
    //     $user = Auth::user();
    //     return response()->json(['success' => $user], $this->successStatus);
    // }

    public function logout()
    {
        if (!auth()->user())
            return response()->json('NOT_EXISTS', 400);

        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });
//        auth()->guard()->logout();
        return response()->json(['message' => 'SUCCESS_LOGOUT', 'status' => 200]);
    }

    public function registerAPI(Array $data)
    {
        $rc = new   RegisterController();
        $username = 'd' . sprintf("%09d", mt_rand(1, 999999999));
        while (User::where('username', $username)->exists())
            $username = 'd' . sprintf("%09d", mt_rand(1, 999999999));
        $data['username'] = $username;
        $rc->validator($data)->validate();

        $user = $rc->create($data);

        return $this->login(new Request(['phone' => $user->phone, 'phone_verify' => $data['phone_verify']]));

    }
}
