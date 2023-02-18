<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Coach;
use App\Models\County;
use App\Models\Doc;
use App\Models\User;
use Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AgencyController extends Controller
{
    public function __construct()
    {
//        $this->middleware('api')->only('get');
    }

    protected function remove(Request $request)
    {

        $data = Agency::where('id', $request->id)->first();
        if (!$this->authorize('editItem', [User::class, $data, false]))
            return response()->json(['errors' => ['اجازه حذف را ندارید']], 422);

        User::where('agency_id', $data->id)->update(['agency_id' => null, 'role' => 'us']);

        $data->delete();

    }

    protected function edit(Request $request)
    {
        $agency = Agency::where('id', $request->id)->first();
        $user = auth()->user();
        $this->authorize('editItem', [User::class, $agency, true]);


        $request->validate([
            'name' => 'sometimes|string|min:3|max:100',
            'county_id' => 'nullable|' . Rule::in(County::pluck('id')),
            'province_id' => 'sometimes' . ($request->county_id ? ('|in:' . County::where('id', $request->county_id)->firstOrNew()->province_id) : ''),
            'email' => ['sometimes', 'email', 'min:6', 'max:50', 'unique:agencies,email,' . $request->id],
            'phone' => 'sometimes|max:11|unique:agencies,phone,' . $request->id,
            'address' => 'sometimes|string|max:1024',
            'description' => 'sometimes|string|max:1024',
            "location" => "sometimes|regex:" . "/^[-+]?[0-9]{1,7}(\\.[0-9]+)?,[-+]?[0-9]{1,7}(\\.[0-9]+)?$/",
            'owner_id' => 'sometimes|' . Rule::in(User::where('role', 'ag')->pluck('id'))

        ], [
            'name.required' => 'نام نمی تواند خالی باشد',
            'name.string' => 'نام نامعتبر است',
            'name.min' => 'نام حداقل 3 حرف باشد',
            'name.max' => 'نام حداکثر 100 حرف باشد',

            'owner_id.in' => 'نقش کاربر باید ' . Helper::$roles['ag'] . ' باشد',

            'county_id.required' => 'شهر ضروری است',
            'county_id.in' => 'شهر نامعتبر است',
            'province_id.required' => 'استان ضروری است',
            'province_id.in' => 'استان نامعتبر است',


            'email.email' => 'ایمیل نامعتبر است',
            'email.max' => 'ایمیل حداکثر 50 حرف باشد',
            'email.min' => 'ایمیل حداقل 6 حرف باشد',
            'email.unique' => 'ایمیل تکراری است',

            'phone.required' => 'شماره تماس نمی تواند خالی باشد',
            'phone.max' => 'شماره تماس حداکثر  11 رقم باشد',
            'phone.unique' => 'شماره تماس تکراری است',

            'address.required' => 'آدرس ضروری است',
            'address.string' => 'آدرس متنی باشد',
            'address.max' => 'حداکثر طول آدرس 1024 کلمه باشد. طول فعلی: ' . mb_strlen($request->address),


            'description.required' => 'توضیحات ضروری است',
            'description.string' => 'توضیحات متنی باشد',
            'description.max' => 'حداکثر طول توضیحات 1024 کلمه باشد. طول فعلی: ' . mb_strlen($request->description),

            'location' => 'مکان نقشه نامعتبر است',
        ]);


        if (isset($request->active)) {

            $agency->active = $request->active;
            return $this->dataEdited($agency, 'agency_edited', 'وضعیت');
        }
        if ($request->owner_id) {
            if ($agency->owner_id == $request->owner_id) return null;
            if ($request->owner_id != null)
                $agency->owner_id = $request->owner_id;
            return $this->dataEdited($agency, 'agency_edited', 'مالک');
        }
        if ($request->name) {
            if ($agency->name == $request->name) return null;
            if ($request->name != null)
                $agency->name = $request->name;
            return $this->dataEdited($agency, 'agency_edited', 'نام');
        }
        if ($request->email) {
            if ($agency->email == $request->email) return null;
            $agency->email = $request->email;
            return $this->dataEdited($agency, 'agency_edited', 'ایمیل');
        }

        if ($request->phone) {
            if ($agency->phone == $request->phone) return null;
            if ($request->phone != null)
                $agency->phone = $request->phone;

            return $this->dataEdited($agency, 'agency_edited', 'شماره تماس');

        }

        if ($request->province_id || $request->county_id || $request->address) {
            if ($agency->province_id == $request->province_id && $agency->county_id == $request->county_id && $agency->address == $request->address && $agency->location == $request->location) return null;
            $agency->province_id = $request->province_id;
            $agency->county_id = $request->county_id;
            $agency->address = $request->address;
            $agency->location = $request->location;

            return $this->dataEdited($agency, 'agency_edited', 'استان/شهر/آدرس/مکان');

        }
        if ($request->description) {
            if ($agency->description == $request->description) return null;
            $agency->description = $request->description;
            return $this->dataEdited($agency, 'agency_edited', 'توضیحات');

        }
    }


    protected function create(Request $request)
    {

        $this->authorize('createItem', [User::class, Agency::class, true]);

        $request->validate([
            'name' => 'string|min:3|max:100',
            'county' => 'nullable|' . Rule::in(County::pluck('id')),
            'province' => 'required' . ($request->county ? ('|in:' . County::where('id', $request->county)->firstOrNew()->province_id) : ''),
            'email' => ['nullable', 'email', 'min:6', 'max:50', Rule::unique('agencies', 'email')],
            'phone' => 'required|max:11|unique:agencies,phone',
            'address' => 'nullable|string|max:1024',
            'description' => 'nullable|string|max:1024',
            "location" => "nullable|regex:" . "/^[-+]?[0-9]{1,7}(\\.[0-9]+)?,[-+]?[0-9]{1,7}(\\.[0-9]+)?$/",
            'owner' => 'nullable|' . Rule::in(User::where('role', 'ag')->pluck('id')),
            'parent' => 'nullable|' . Rule::in(Agency::pluck('id'))

//            'video' => 'nullable|mimes:mp4' /*. ',m4v,avi,flv,mov'*/ . '|max:10240'
        ], [
            'name.required' => 'نام  نمی تواند خالی باشد',
            'name.string' => 'نام  نامعتبر است',
            'name.min' => 'نام  حداقل 3 حرف باشد',
            'name.max' => 'نام  حداکثر 100 حرف باشد',

            'owner.in' => 'نقش کاربر باید ' . Helper::$roles['ag'] . ' باشد',
            'parent.in' => 'نمایندگی والد نامعتبر است',

            'county.required' => 'شهر ضروری است',
            'county.in' => 'شهر نامعتبر است',
            'province.required' => 'استان ضروری است',
            'province.in' => 'استان نامعتبر است',


            'email.email' => 'ایمیل نامعتبر است',
            'email.max' => 'ایمیل حداکثر 50 حرف باشد',
            'email.min' => 'ایمیل حداقل 6 حرف باشد',
            'email.unique' => 'ایمیل تکراری است',

            'phone.required' => 'شماره تماس نمی تواند خالی باشد',
            'phone.max' => 'شماره تماس حداکثر  11 رقم باشد',
            'phone.unique' => 'شماره تماس تکراری است',

            'address.required' => 'آدرس ضروری است',
            'address.string' => 'آدرس متنی باشد',
            'address.max' => 'حداکثر طول آدرس 1024 کلمه باشد. طول فعلی: ' . mb_strlen($request->address),


            'description.required' => 'توضیحات ضروری است',
            'description.string' => 'توضیحات متنی باشد',
            'description.max' => 'حداکثر طول توضیحات 1024 کلمه باشد. طول فعلی: ' . mb_strlen($request->description),

            'location' => 'مکان نقشه نامعتبر است',

        ]);


        $agency = Agency::create([
            'parent_id' => $request->parent,
            'owner_id' => $request->owner,
            'province_id' => $request->province,
            'county_id' => $request->county,
            'name' => $request->name,
            'active' => true,
            'phone' => $request->phone,
            'location' => $request->location,
            'email' => $request->email,
            'description' => $request->description,
            'address' => $request->address,
        ]);
        $u = User::find($request->owner);
        if ($u) {
            $u->agency_id = $agency->id;
            $u->save();
        }

        \Telegram::log(Helper::$TELEGRAM_GROUP_ID, 'agency_created', $agency);

        return response(['id' => $agency->id]);
        return redirect(url('panel/agency/edit/' . $agency->id))->with('success-alert', 'با موفقیت ثبت شد! با انتخاب آن می توانید اطلاعات ثبت شده را مشاهده و ویرایش کنید');


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
        $province_id = $request->province;
        $county_id = $request->county;
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

        $query = Agency::query();

        if (is_numeric($province_id))
            $query = $query->where('province_id', $province_id);
        if (is_numeric($county_id))
            $query = $query->where('county_id', $county_id);

        if (isset($active))
            $query = $query->where('active', (boolean)$active);

        if (isset($search)) {
            foreach (explode(' ', $search) as $word) {
                $query = $query->where(function ($query) use ($word, $province_id, $county_id) {
                    $query->orWhere('name', 'LIKE', '%' . $word . '%')
                        ->orWhere('phone', 'LIKE', '%' . $word . '%')
                        ->orWhere('email', 'LIKE', '%' . $word . '%');

                    $province_id = json_decode($province_id);
                    $county_id = json_decode($county_id);

                    if (is_array($province_id))
                        $query = $query->orWhereIn('province_id', $province_id);
                    if (is_array($county_id))
                        $query = $query->orWhereIn('county_id', $county_id);
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
}
