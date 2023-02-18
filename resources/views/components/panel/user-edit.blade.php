@php
    $user=\App\Models\User::find($param);
@endphp
@if(!$user)
    <div class="text-center font-weight-bold text-danger mt-5">کاربر یافت نشد</div>
@else
    @cannot('editItem',[\App\Models\User::class,$user,false,(object)['role'=>$user->role]])
        @php
            header("Location: " . URL::to('/panel/users'), true, 302);
        exit();
        @endphp
    @endcannot
    {{--{{json_encode( $images)}}--}}

    @php
        $provinces=\App\Models\Province::select('id','name')->get();
                $players=\App\Models\Player::select('id','name','family')->where('user_id',$user->id)->get();
                $coaches=\App\Models\Coach::select('id','name','family')->where('user_id',$user->id)->get();
                $clubs=\App\Models\Club::select('id','name')->where('user_id',$user->id)->get();
                $shops=\App\Models\Shop::select('id','name')->where('user_id',$user->id)->get();
                $count=count($players)+count($coaches)+count($clubs)+count($shops);
    @endphp


    <div class="  my-3 ">
        <div class="row mx-auto justify-content-center">
            <div class="col-md-10 col-sm-12  ">
                <div class="card bg-light">
                    <h5 class="card-header text-center text-white bg-primary">
                        <div class=" input-group my-2">
                            <label for="addres-input"
                                   class="  ">تنظیمات کاربر </label>

                            <span class="mx-1 small {{$user->active? 'text-success':'text-danger'}}">{{$user->active? 'فعال':'غیر فعال'}}</span>
                            <button class="ms-auto btn btn-danger rounded ms-auto font-weight-bold " type="button"
                                    id="addres-addon" data-bs-toggle="modal" data-bs-target="#removeModal"
                                    onclick="">حذف
                            </button>
                            <!-- Modal -->

                            <div class="modal  fade  " id="removeModal" tabindex="-1"
                                 aria-labelledby="renewModalLabel"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary py-2">
                                            <h5 class="modal-title text-white font-weight-bold"
                                                id="exampleModalLabel">حذف کاربر
                                            </h5>
                                            <button type="button" class="btn-close text-white btn-white"
                                                    data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        @if($count>0)
                                            <div class="text-danger my-3 small small">
                                                برای این کاربر موارد زیر نیز ثبت شده
                                                است. در صورت
                                                انتخاب هرکدام، همراه با کاربر حذف خواهند شد
                                            </div>
                                        @else
                                            <div class="text-danger my-3 small small">
                                                از حذف اطمینان دارید؟
                                            </div>
                                        @endif
                                        <div class="modal-body col-md-10  mx-auto ">
                                            @foreach($players as  $data)

                                                <div class="form-check ">
                                                    <input class="form-check-input" type="checkbox"
                                                           id="player-check-{{$data->id}}"
                                                           value="player_{{$data->id}}"
                                                           name="data_{{$data->id}}"/>
                                                    <label class="form-check-label text-primary small mx-2"
                                                           for="player-check-{{$data->id}}">
                                                        <span class="font-weight-bold mx-2"> {{' (بازیکن) '.  $data->name.' '.$data->family}} </span>
                                                    </label>
                                                </div>

                                            @endforeach
                                            @foreach($coaches as   $data)

                                                <div class="form-check ">
                                                    <input class="form-check-input" type="checkbox"
                                                           id="coach-check-{{$data->id}}"
                                                           value="coach_{{$data->id}}"
                                                           name="data_{{$data->id}}"/>
                                                    <label class="form-check-label text-primary small mx-2"
                                                           for="coach-check-{{$data->id}}">

                                                        <span class="font-weight-bold mx-2">{{' (مربی) '.$data->name.' '.$data->family}} </span>
                                                    </label>
                                                </div>

                                            @endforeach
                                            @foreach($clubs as   $data)

                                                <div class="form-check ">
                                                    <input class="form-check-input" type="checkbox"
                                                           id="club-check-{{$data->id}}"
                                                           value="club_{{$data->id}}"
                                                           name="data_{{$data->id}}"/>
                                                    <label class="form-check-label text-primary small mx-2"
                                                           for="club-check-{{$data->id}}">

                                                        <span class="font-weight-bold mx-2">{{' (باشگاه) '.$data->name.' '.$data->family}} </span>
                                                    </label>
                                                </div>

                                            @endforeach

                                            @foreach($shops as   $data)

                                                <div class="form-check ">
                                                    <input class="form-check-input" type="checkbox"
                                                           id="shop-check-{{$data->id}}"
                                                           value="shop_{{$data->id}}"
                                                           name="data_{{$data->id}}"/>
                                                    <label class="form-check-label text-primary small mx-2"
                                                           for="club-check-{{$data->id}}">

                                                        <span class="font-weight-bold mx-2">{{' (فروشگاه) '.$data->name.' '.$data->family}} </span>
                                                    </label>
                                                </div>

                                            @endforeach

                                            <div class="  ">
                                                <div class=" my-2    input-group  ">


                                                </div>
                                                <div class=" text-danger text-start small     " role="alert">
                                                    <strong id="err-coupon"> </strong>
                                                </div>
                                                <div class=" text-danger text-start small     " role="alert">
                                                    <strong id="err-error"> </strong>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button class="btn btn-danger "
                                                    onclick="remove({{$user->id}})"
                                            >حذف
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </h5>

                    <div class="card-body  ">


                        <form id="form-create" method="POST" action=" "
                              class="text-right  row">
                            @csrf

                            @if($count>0)
                                <div class="col-md-10 mx-auto border border primary p-2">
                                    <span class="small mb-2 d-block">اطلاعات ثبت شده به نام کاربر</span>

                                    @foreach($players as  $data)

                                        <a class="d-inline-block m-1  btn btn-sm btn-primary"
                                           href="{{url('panel/player/edit/').'/'.$data->id}}">

                                            {{' (بازیکن) '.  $data->name.' '.$data->family}}

                                        </a>

                                    @endforeach
                                    @foreach($coaches as   $data)

                                        <a class="d-inline-block m-1 btn btn-sm btn-primary "
                                           href="{{url('panel/coach/edit/').'/'.$data->id}}">
                                            {{' (مربی) '.$data->name.' '.$data->family}}

                                        </a>

                                    @endforeach
                                    @foreach($clubs as   $data)

                                        <a class="d-inline-block m-1 btn btn-sm btn-primary "
                                           href="{{url('panel/club/edit/').'/'.$data->id}}">

                                            {{' (باشگاه) '.$data->name.' '.$data->family}}

                                        </a>

                                    @endforeach

                                    @foreach($shops as   $data)

                                        <a class=" d-inline-block m-1 btn btn-sm btn-primary"
                                           href="{{url('panel/shop/edit/').'/'.$data->id}}">

                                            {{' (فروشگاه) '.$data->name.' '.$data->family}}

                                        </a>

                                    @endforeach
                                </div>
                            @endif

                            <div class="col-md-10  mx-auto  ">
                                <div class=" mb-2 form-outline input-group  ">

                                    <input id="name" type="text"
                                           class="  px-4 form-control @error('name') is-invalid @enderror"
                                           name="name"
                                           value="{{ $user->name }}" autocomplete="name" autofocus>
                                    <label for="name"
                                           class="col-md-12    form-label  text-md-right">نام </label>
                                    <button class="btn btn-secondary rounded" type="button"
                                            id="name-addon"
                                            onclick=" submitWithFiles(event,{'name':document.getElementById('name').value})">
                                        ویرایش
                                    </button>
                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-name"> </strong>
                                </div>
                            </div>

                            <div class="col-md-10  mx-auto  ">
                                <div class=" my-4 form-outline input-group  ">

                                    <input id="family" type="text"
                                           class="  px-4 form-control @error('family') is-invalid @enderror"
                                           name="family"
                                           value="{{ $user->family }}" autocomplete="family" autofocus>
                                    <label for="family"
                                           class="col-md-12    form-label  text-md-right">نام خانوادگی </label>
                                    <button class="btn btn-secondary rounded" type="button"
                                            id="family-addon"
                                            onclick=" submitWithFiles(event,{'family':document.getElementById('family').value})">

                                        ویرایش
                                    </button>
                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-family"> </strong>
                                </div>
                            </div>

                            <div class="col-md-10  mx-auto  ">
                                <div class=" my-2 form-outline input-group  ">

                                    <input id="username" type="text"
                                           class="  px-4 form-control @error('username') is-invalid @enderror"
                                           name="username"
                                           value="{{ $user->username }}" autocomplete="username" autofocus>
                                    <label for="username"
                                           class="col-md-12    form-label  text-md-right">نام کاربری </label>
                                    <button class="btn btn-secondary rounded" type="button"
                                            id="username-addon"
                                            onclick=" submitWithFiles(event,{'username':document.getElementById('username').value})">
                                        ویرایش
                                    </button>

                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-username"> </strong>
                                </div>
                            </div>

                            <div class="col-md-10  mx-auto  ">
                                <div class=" my-4 form-outline input-group  ">

                                    <input id="email" type="text"
                                           class="  px-4 form-control @error('email') is-invalid @enderror"
                                           name="email"
                                           value="{{ $user->email }}" autocomplete="email" autofocus>
                                    <label for="email"
                                           class="col-md-12    form-label  text-md-right ">ایمیل
                                        <span class="font-weight-bold {{!$user->email?'d-none':''}} {{$user->email_verified?'text-success':'text-danger'}}">
                                            ({{$user->email_verified?' فعال ':' غیر فعال '}}
                                            ) </span> </label>
                                    <button class="btn btn-secondary rounded" type="button"
                                            id="email-addon"
                                            onclick=" submitWithFiles(event,{'email':document.getElementById('email').value})">
                                        ویرایش
                                    </button>

                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-username"> </strong>
                                </div>
                            </div>

                            <div class="col-md-10 mx-auto   border border-primary rounded-3    py-3">

                                <div class="my-2 mb-4  form-outline input-group">
                                    <input id="phone" type="tel"
                                           class="  px-4 form-control @error('phone') is-invalid @enderror"
                                           name="phone"
                                           value="{{ $user->phone }}"
                                           autocomplete="phone">
                                    <label for="phone"
                                           class="col-md-12  form-label text-md-right">شماره همراه</label>
                                    <button class="btn btn-secondary rounded" type="button"
                                            id="phone-addon"
                                            onclick=" submitWithFiles(event,{
                                                'phone':f2e(document.getElementById('phone').value),
                                           'phone_verify':f2e(document.getElementById('phone_verify').value),
                                           })">
                                        ویرایش
                                    </button>


                                </div>
                                <div class=" text-danger text-start small col-12    " role="alert">
                                    <strong id="err-phone"> </strong>
                                </div>

                                <div class=" col-md-12 mx-auto    ">
                                    <div class=" form-outline input-group my-2 mt-4  ">

                                        <input id="phone_verify" type="number"
                                               class=" px-4 form-control @error('phone_verify') is-invalid @enderror"
                                               name="phone_verify">
                                        <label for="phone_verify"
                                               class="col-md-12  form-label text-md-right">کد تایید </label>
                                        <button class="btn btn-secondary rounded px-1 px-sm-2" type="button"
                                                id="phone_verify-addon1">

                                            دریافت کد تایید
                                        </button>
                                    </div>
                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-phone_verify"> </strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-10 mx-auto   border border-primary rounded-3    py-3 my-2">
                                <div class="  mx-auto  ">
                                    <div class=" my-2 form-outline input-group  ">

                                        <input id="sheba" type="text"
                                               class="  px-4 form-control @error('sheba') is-invalid @enderror"
                                               name="sheba"
                                               value="{{ $user->sheba }}" autocomplete="sheba" autofocus>
                                        <label for="sheba"
                                               class="col-md-12    form-label  text-md-right">شماره شبا (بدون
                                            IR)</label>
                                        <button class="btn btn-secondary rounded" type="button"
                                                id="sheba-addon"
                                                onclick=" submitWithFiles(event,{'sheba':document.getElementById('sheba').value})">
                                            ویرایش
                                        </button>

                                    </div>
                                    <div class=" text-danger text-start small     " role="alert">
                                        <strong id="err-sheba"> </strong>
                                    </div>
                                </div>
                                <div class="mt-4   mx-auto  ">
                                    <div class=" my-2 form-outline input-group  ">

                                        <input id="cart" type="text"
                                               class="  px-4 form-control @error('cart') is-invalid @enderror"
                                               name="cart"
                                               value="{{ $user->cart }}" autocomplete="cart" autofocus>
                                        <label for="cart"
                                               class="col-md-12    form-label  text-md-right">شماره کارت </label>
                                        <button class="btn btn-secondary rounded" type="button"
                                                id="cart-addon"
                                                onclick=" submitWithFiles(event,{'cart':document.getElementById('cart').value})">
                                            ویرایش
                                        </button>

                                    </div>
                                    <div class=" text-danger text-start small     " role="alert">
                                        <strong id="err-cart"> </strong>
                                    </div>
                                </div>
                            </div>
                            @if($user->id != auth()->user()->id)
                                <div class="col-md-10 row mx-auto my-2 px-0 py-2 border border-primary rounded-3">
                                    <div class=" input-group my-2">
                                        <label for="addres-input"
                                               class="  ">نقش کاربری</label>
                                        <button class="btn btn-secondary rounded ms-auto" type="button"
                                                id="role-addon"
                                                onclick=" submitWithFiles(event,{
                                            'role':document.querySelector('input[type=radio][name=role]:checked').value,
                                            'owner':document.getElementsByName('owner')[0].value,
                                           })">ویرایش
                                        </button>
                                    </div>
                                    <div class="  my-2  shadow-0 p-2   ">
                                        @foreach(Helper::$roles as $role=>$rname)
                                            @can('createItem',[\App\Models\User::class,\App\Models\User::class,false,(object)['role'=>$role]])
                                                <span class="d-inline-block">
                                        <input type="radio" class="form-check-input  " name="role" value="{{$role}}"
                                               id="{{"option-$role"}}"
                                               autocomplete="off" {{$role==$user->role?  'checked':''}}/>
                                        <label class="form-check-label me-4" for="{{"option-$role"}}">{{$rname}}</label>
                                        </span>
                                            @endcan
                                        @endforeach

                                    </div>
                                    @if( in_array(auth()->user()->role,['go','ad'])  )
                                        <div class="px-3">
                                            <select id="agency" name="owner"
                                                    class="  my-2 form-control{{ $errors->has('agency')  ? ' is-invalid' : '' }}">
                                                <option value="">انتخاب نمایندگی</option>
                                                @foreach(\App\Models\Agency::select('id','name','province_id')->get() as $a)
                                                    <option value="{{$a->id}}"
                                                            {{ $user->agency_id==$a->id? ' selected ':''}} >{{ ($a->name .' '. optional($provinces->where('id',$a->province_id)->first())->name) }}</option>

                                                @endforeach
                                            </select>

                                            <div class=" text-danger text-start small  col-12   " role="alert">
                                                <strong id="err-agency"> </strong>
                                            </div>


                                        </div>
                                    @endif
                                </div>
                            @endif
                            <div class="col-md-10 mx-auto    py-3">

                                <a href="{{ route('password.request') }}"
                                   class="btn  btn-secondary btn-block  ">
                                    تغییر رمز عبور
                                </a>


                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center text-white bg-primary"></div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>


            document.addEventListener("DOMContentLoaded", function (event) {
                addSMSBtnListener(
                    "{{auth()->user()->name}}",
                    "{{auth()->user()->family}}",
                    "{{auth()->user()->phone}}"
                );

            });

            function remove(id) {
                document.querySelector('#loading').classList.remove('d-none');

                let attach = Array.from(document.querySelectorAll('input[name^=data_]:checked')).map(el => el.value);

//                event.preventDefault();
                data = {
                    'id': id,
                    'attach': attach,
                }
                ;
                axios.post("{{route('user.remove')}}", data, {})
                    .then((response) => {
//                        console.log(response);
                            document.querySelector('#loading').classList.add('d-none');
                            if (response.status === 200)
                                window.location = "{{url('panel/users')}}";
                        }
                    ).catch((error) => {
                    document.querySelector('#loading').classList.add('d-none');
                    let errors = '';
                    if (error.response && error.response.status === 422)
                        for (let idx in error.response.data.errors)
                            errors += error.response.data.errors[idx] + '<br>';
                    else {
                        if (error.response && error.response.data)
                            errors = error.response.data;
                        else
                            errors = error;
                    }
                    window.showToast('danger', errors);
                });
            }

            function submitWithFiles(event, data) {
                document.querySelector('#loading').classList.remove('d-none');

                event.preventDefault();
                data = {'id': "{{$user->id}}", ...data};

                axios.post("{{route('user.edit')}}", data, {})

                    .then((response) => {
                            console.log(response);
                            document.querySelector('#loading').classList.add('d-none');

                        if (response.status == 200) {
                            if (response.data && response.data.res)
                                window.showToast('success', response.data.res);
                            else
                                window.location.reload();
                        }
                                {{--window.location = '{{url('panel/club')}}'--}}

                        }
                    ).catch((error) => {
                    document.querySelector('#loading').classList.add('d-none');
//                console.log(error.response.data.errors);

                    invalidInputs(error.response.data.errors);
                    let errors = '';
                    if (error.response && error.response.status === 422)
                        for (let idx in error.response.data.errors)
                            errors += error.response.data.errors[idx] + '<br>';
                    else {
                        errors = error;
                    }
                    window.showToast('danger', errors);
//                    if (error.response && error.response.status === 422)
//                        for (let idx in error.response.data.errors)
//                            this.errors += '' + error.response.data.errors[idx] + '<br>';
//                    else {
//                        this.errors = error;
//                    }
//                    window.showDialog('danger', this.errors, onclick = null);
                });
            }

                    @php( $counties=\App\Models\County::get())

            let counties = @json($counties);


            function setCountyOptions(selValue) {
                let sel2 = document.querySelector('#county_id');
                while (sel2.firstChild && sel2.removeChild(sel2.firstChild)) ;
                for (let i = 0; i < counties.length; i++) {
                    if (counties[i].province_id == selValue) {

                        sel2.innerHTML += (`<option value='${counties[i].id}' pvalue='${counties[i].province_id}'>${counties[i].name}</option>`);

                    }
                }
//            console.log(sel2);

            }

            //        });
        </script>
    @endpush

@endif