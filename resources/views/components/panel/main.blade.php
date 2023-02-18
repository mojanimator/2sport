@php
    $user=auth()->user();
    $god= $user && (  $user->role=='go');
    $admin= $user && ($user->role=='ad'  );
    $user= $user && in_array($user->role,array_keys(Helper::$roles)) ;


@endphp

<div class="row mt-3  mx-auto ">
    @if($god)
        @php($agenciesNum=\App\Models\Agency::count())
        <div class="col-md-6   ">
            <a href="{{url('panel/system-setting')}}" class="my-1  d-block ">
                <div class="card move-on-hover">
                    <div class="card-body  p-3  blur">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <h5 class="  mb-0 text-primary font-weight-bold">
                                        تنظیمات سیستم
                                    </h5>
                                    <h5 class=" text-sm text-black-50 font-weight-bolder mb-0">


                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="  ">
                                    <i class="fa fa-3x fa-cog text-primary m-1"
                                       aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6   ">
            <a href="{{url('panel/agencies')}}" class="my-1  d-block ">
                <div class="card move-on-hover">
                    <div class="card-body  p-3  blur">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <h5 class="  mb-0 text-primary font-weight-bold">
                                        نمایندگی ها
                                    </h5>

                                    <span class=" small text-black-50    my-1">
                                            {{ 'تعداد: '. $agenciesNum }}
                                        </span>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="  ">
                                    <i class="fa fa-3x fa-house-user text-primary m-1"
                                       aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif
    @if($user)
        <div class="col-md-6   ">
            <a href="{{url('panel/system-logs')}}" class="my-1  d-block ">
                <div class="card move-on-hover">
                    <div class="card-body  p-3  blur">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <h5 class="  mb-0 text-primary font-weight-bold">
                                        گزارشات سیستم
                                    </h5>
                                    <h5 class=" text-sm text-black-50 font-weight-bolder mb-0">

                                    </h5>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="  ">
                                    <i class="fa fa-3x fa-list text-primary m-1"
                                       aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>


        <div class="col-md-6   ">
            <a href="{{url('panel/referral')}}" class="my-1  d-block ">
                <div class="card move-on-hover">
                    <div class="card-body  p-3  blur">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <h5 class="  mb-0 text-primary font-weight-bold">
                                        بازاریابی
                                    </h5>
                                    @php($ref=\App\Models\Ref::refs())


                                    <span class=" small text-black-50    my-1">
                                            {{ 'تعداد: '. $ref['count'] }}
                                        </span>
                                    <span class=" small text-danger      my-1">
                                            {{      'تسویه نشده: '. $ref['unpaid']}}
                                        </span>


                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="  ">
                                    <i class="fa fa-3x fa-dollar-sign text-primary m-1"
                                       aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    @endif
    @php($user=auth()->user())
    @php($admin?$c=\App\Models\Coupon::count():$c=\App\Models\Coupon::where(function ($query) {
return $query->orWhere('expires_at','>',\Illuminate\Support\Carbon::now())->orWhereNull('expires_at' );})
->where(function($query) use ($user){
return $query->orWhere('user_id',$user->id)->orWhereNull('user_id');})
->where(function ($query) use ($user){
return $query->whereNotIn('id',\App\Models\Payment::where('user_id',$user->id)->whereNotNull('coupon_id')->pluck('coupon_id'));
})->count())
    <div class="col-md-6   ">
        <a href="{{url('panel/setting')}}" class="my-1  d-block ">
            <div class="card move-on-hover">
                <div class="card-body  p-3  blur">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <h5 class="  mb-0 text-primary font-weight-bold">
                                    تنظیمات کاربری
                                </h5>
                                <h5 class=" text-sm text-black-50 font-weight-bolder mb-0">


                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="  ">
                                <i class="fa fa-3x fa-user-circle text-primary m-1"
                                   aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-6   ">
        <a href="{{url('panel/coupons')}}" class="my-1  d-block ">
            <div class="card move-on-hover">
                <div class="card-body  p-3  blur">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <h5 class="  mb-0 text-primary font-weight-bold">
                                    کوپن تخفیف
                                </h5>
                                <span class=" small  text-black-50   my-1">
                                            {{ 'تعداد: '. $c }}
                                        </span>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="  ">
                                <i class="fa fa-3x fa-money-bill text-primary m-1"
                                   aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
