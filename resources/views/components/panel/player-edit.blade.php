@php
    $player=\App\Models\Player::where('id',$param)->with('docs:id,type_id,docable_id')->first();


@endphp
@if(!$player)
    <div class="text-center font-weight-bold text-danger mt-5">بازیکن یافت نشد</div>
@else
    @cannot('editItem',[\App\Models\User::class,$player,false ])
        @php
            header("Location: " . URL::to('/panel/players'), true, 302);
        exit();
        @endphp
    @endcannot
    @php

        $user=auth()->user();
        $docs=collect(  $player->docs );
        $profile=$docs->where('type_id',Helper::$docsMap['profile'])->first() ;
        $video=$docs->where('type_id',Helper::$docsMap['video'])->first() ;

        $tmp=Morilog\Jalali\Jalalian::fromDateTime($player->born_at);
        $player->d=$tmp->getDay();
        $player->m=$tmp->getMonth();
        $player->y=$tmp->getYear();

        if(!$player->expires_at)
            $expire_days=0;
        else{
        $now=\Carbon\Carbon::now();
            $expire_days= $now->diffInDays(\Carbon\Carbon::createFromTimestamp($player->expires_at),false);
         if($expire_days<0)
            $expire_days=0;

        }

    @endphp
    {{--{{json_encode( $images)}}--}}


    <div class="  my-3 ">
        <div class="row mx-auto justify-content-center">
            <div class="col-md-10 col-sm-12  ">
                <div class="card bg-light   ">
                    <div class="  card-header   text-white bg-primary  d-flex justify-content-between  ">
                        <div class="    ">
                            <label for="delete-input"
                                   class="  ">{{$player->name.' '.$player->family}}</label>
                            <button class="btn btn-sm btn-danger rounded  ms-2 font-weight-bold" type="button"
                                    id="delete-addon"
                                    onclick=" window.showDialog('confirm','از حذف اطمینان دارید؟',()=>remove,{{$player->id}})">
                                حذف
                            </button>
                        </div>
                        <div class="    ">
                            <span class="  small">{{'اعتبار: '.$expire_days.' روز'}}</span>

                            <button class="mx-1    btn btn-secondary btn-sm rounded   font-weight-bold"
                                    type="button" data-bs-toggle="modal" data-bs-target="#renewModal"
                                    id="renew-addon">
                                تمدید
                            </button>
                            <!-- Modal -->
                            <div class="modal  fade  " id="renewModal" tabindex="-1"
                                 aria-labelledby="renewModalLabel"
                                 aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary py-2">
                                            <h5 class="modal-title text-white font-weight-bold"
                                                id="exampleModalLabel">تمدید
                                                اشتراک</h5>
                                            <button type="button" class="btn-close text-white btn-white"
                                                    data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body col-md-10  mx-auto ">
                                        @foreach(\App\Models\Setting::where('key','like','player%')->where('key','like','%_price')->orderBy('id','ASC')->get() as $idx=> $sub)
                                            <!-- Default radio -->
                                                <div class="form-check ">
                                                    <input class="" type="radio"
                                                           id="price-check-{{$idx}}"
                                                           value="{{count( explode('_',$sub->key))>1? explode('_',$sub->key)[1]:null }}"
                                                           name="renew-month" {{$idx==0? 'checked' : ''}} />
                                                    <label class="form-check-label text-primary small mx-2"
                                                           for="price-check-{{$idx}}">
                                                        <span>{{str_replace('(ت)','',$sub->name)}} </span>
                                                        <span id="{{$sub->key}}-label"
                                                              class="font-weight-bold mx-2">{{$sub->value .' تومان '}} </span>
                                                    </label>
                                                </div>

                                            @endforeach

                                            <div class="  ">
                                                <div class=" my-2    input-group  ">

                                                    <input id="coupon" type="text" placeholder="کد تخفیف"
                                                           class="  px-4 form-control @error('coupon') is-invalid @enderror"
                                                           name="coupon"
                                                           autocomplete="coupon" autofocus>

                                                    <button class="btn btn-secondary rounded" type="button"
                                                            id="coupon-addon"
                                                            onclick=" calculateCoupon(event,{'coupon':document.getElementById('coupon').value,'type':'player', })">

                                                        اعمال
                                                    </button>

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
                                            <button class="btn btn-success "
                                                    onclick=" makePayment(event,{'coupon':document.getElementById('coupon').value,'type':'player','month':document.querySelector('input[name=renew-month]:checked').value,'id':'{{$player->id}}' })"
                                            >پرداخت
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body  ">


                        <form id="form-create" method="POST" action="{{ route('club.create') }}"
                              class="text-right  row">
                            @csrf

                            <div class="row">
                                {{--   style="  width:{{.85*10}}rem;min-height:{{10}}rem" --}}
                                <image-uploader key="-1"
                                                class=" my-1 col-6 mx-auto   overflow-auto" id="license"
                                                label="تصویر چهره"
                                                id="{{$player->id}}"
                                                for-id="{{$profile?$profile->id:''}}" ref="profileUploader"
                                                crop-ratio="{{.85}}"
                                                link="{{route('player.edit')}}"
                                                type="{{Helper::$docsMap['profile']}}"
                                                required="yes"
                                                preload="{{$profile?asset('storage')."/$profile->type_id/$profile->id.jpg":''}}"
                                                height="10" mode="edit">

                                </image-uploader>
                                <video-uploader
                                        class="col-12 col-lg-12 mx-auto  " id="video"
                                        label=" ویدیو کوتاه معرفی خود و مهارت های ورزشی"
                                        id="{{$player->id}}"
                                        for-id="{{$video->id}}" ref="videoUploader"
                                        crop-ratio="{{1.25}}"
                                        link="{{route('player.edit')}}"
                                        required="yes"
                                        type="{{Helper::$docsMap['video']}}"
                                        link="{{route('player.edit')}}"
                                        preload="{{asset('storage')."/$video->type_id/$video->id.mp4"}}"

                                        height="10" mode="edit">

                                </video-uploader>
                            </div>

                            <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-profile"> </strong>
                                    </span>


                            <div class="col-md-10  mx-auto  ">
                                <div class=" my-2 form-outline input-group  ">

                                    <input id="name" type="text"
                                           class="  px-4 form-control @error('name') is-invalid @enderror"
                                           name="name"
                                           value="{{ $player->name }}" autocomplete="name" autofocus>
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


                            <div class="col-md-10 mx-auto   ">
                                <div class="my-2  form-outline input-group">

                                    <input id="family" type="text"
                                           class="  px-4 form-control @error('family') is-invalid @enderror"
                                           name="family"
                                           value="{{ $player->family }}" autocomplete="family" autofocus>
                                    <label for="name"
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

                            <div class="col-md-10 mx-auto   border border-primary rounded-3    py-3">

                                <div class="my-2   form-outline input-group">
                                    <input id="phone" type="tel"
                                           class="  px-4 form-control @error('phone') is-invalid @enderror"
                                           name="phone"
                                           value="{{ $player->phone }}"
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
                                    <div class=" form-outline input-group my-2   ">

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


                            <div class="col-md-10 mx-auto    border border-primary rounded-3 my-2 ">
                                <div class="text-end my-1">
                                    <button class="btn btn-secondary rounded     " type="button"
                                            id="is_man-addon"
                                            onclick=" submitWithFiles(event,{
                                                    'is_man':document.getElementById('option1').checked,
                                                    'd':f2e(document.getElementById('d').value),
                                                    'm':f2e(document.getElementById('m').value),
                                                    'y':f2e(document.getElementById('y').value),
                                                    'height':f2e(document.getElementById('height').value),
                                                    'weight':f2e(document.getElementById('weight').value),
                                                })">

                                        ویرایش
                                    </button>
                                </div>
                                <div class="  form-check-inline my-2  shadow-0 px-0 ">
                                    <input type="radio" class="form-check-input  " name="is_man" value="1"
                                           id="option1"
                                           autocomplete="off" {{$player->is_man?'checked':''}}/>
                                    <label class="form-check-label me-4" for="option1">مرد</label>

                                    <input type="radio" class="form-check-input  " name="is_man" value="0"
                                           id="option2"
                                           autocomplete="off" {{!$player->is_man?'checked':''}} />
                                    <label class="form-check-label me-4  " for="option2">زن</label>

                                </div>
                                <div class="col-md-10 mx-auto row  px-0  ">
                                    <div class="my-2     col-6">
                                        <div class="form-outline">
                                            <input id="height" type="number"
                                                   class="px-4 form-control @error('height') is-invalid @enderror"
                                                   value="{{ $player->height }}"
                                                   name="height"
                                                   autocomplete="height">
                                            <label for="height"
                                                   class="col-md-12  form-label text-md-right">قد (سانتی متر)</label>

                                        </div>
                                        <div class=" text-danger text-start small  col-12   " role="alert">
                                            <strong id="err-height"> </strong>
                                        </div>
                                    </div>

                                    <div class="my-2   col-6">
                                        <div class="form-outline">
                                            <input id="weight" type="number"
                                                   class="  px-4 form-control @error('weight') is-invalid @enderror"
                                                   value="{{ $player->weight }}"
                                                   name="weight"
                                                   autocomplete="weight">
                                            <label for="weight"
                                                   class="col-md-12  form-label text-md-right">وزن (کیلوگرم)</label>

                                        </div>
                                        <div class=" text-danger text-start small  col-12   " role="alert">
                                            <strong id="err-weight"> </strong>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-12 mx-auto row   ">
                                    <div class="my-2   form-outline col-3">
                                        <input id="d" type="number"
                                               class="  px-1 form-control @error('d') is-invalid @enderror"
                                               value="{{$player->d }}"
                                               name="d"
                                               autocomplete="d">
                                        <label for="d"
                                               class="col-md-12  form-label text-md-right">روز تولد</label>


                                    </div>
                                    <div class="col-auto  "></div>
                                    <div class="my-2   form-outline col-3">
                                        <input id="m" type="number"
                                               class="  px-1 form-control @error('m') is-invalid @enderror"
                                               value="{{ $player->m }}"
                                               name="m"
                                               autocomplete="m">
                                        <label for="m"
                                               class="col-md-12  form-label text-md-right">ماه تولد</label>


                                    </div>
                                    <div class="col-auto   "></div>
                                    <div class="my-2   form-outline col-4">
                                        <input id="y" type="number"
                                               class="  px-1 form-control @error('y') is-invalid @enderror"
                                               value="{{ $player->y }}"
                                               name="y"
                                               autocomplete="y">
                                        <label for="y"
                                               class="col-md-12  form-label text-md-right">سال تولد</label>


                                    </div>
                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-d"> </strong>
                                    </div>
                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-m"> </strong>
                                    </div>
                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-y"> </strong>
                                    </div>

                                </div>
                            </div>


                            <div class="col-md-10 row mx-auto my-2 ">
                                <div class="input-group px-0 ">
                                    <select id="sport_id" name="sport_id"
                                            class="px-4 form-control{{ $errors->has('sport_id')  ? ' is-invalid' : '' }}">
                                        <option value="0">انتخاب رشته ورزشی</option>
                                        @foreach(\App\Models\Sport::get() as $s)
                                            <option value="{{$s->id}}"
                                                    {{ $player->sport_id==$s->id? ' selected ':''}} >{{$s->name}}</option>

                                        @endforeach
                                    </select>
                                    <button class="btn btn-secondary rounded ms-auto" type="button"
                                            id="addres-addon"
                                            onclick=" submitWithFiles(event,{
                                            'sport_id':document.getElementById('sport_id').value,

                                           })">ویرایش
                                    </button>
                                </div>
                                <div class=" text-danger text-start small  col-12   " role="alert">
                                    <strong id="err-sport_id"> </strong>
                                </div>

                            </div>

                            <div class="col-md-10 row mx-auto my-2 px-0 py-2 border border-primary rounded-3">
                                <div class=" input-group my-2">
                                    <label for="addres-input"
                                           class="  ">استان و شهر</label>
                                    <button class="btn btn-secondary rounded ms-auto" type="button"
                                            id="addres-addon"
                                            onclick=" submitWithFiles(event,{
                                            'county_id':document.getElementById('county_id').value,
                                            'province_id':document.getElementById('province_id').value,
                                           })">ویرایش
                                    </button>
                                </div>
                                <div class="col-sm-6 my-1 my-sm-0 ">
                                    {{--<label for="province-input"--}}
                                    {{--class="col-12 col-form-label text-right">استان</label>--}}
                                    <select id="province_id" name="province_id" onchange="setCountyOptions(this.value)"
                                            class="px-4 form-control{{ $errors->has('province_id')  ? ' is-invalid' : '' }}">
                                        <option value="0">انتخاب استان</option>
                                        @foreach(\Illuminate\Support\Facades\DB::table('province')->get() as $p)
                                            <option value="{{$p->id}}"
                                                    {{ $player->province_id==$p->id? ' selected ':''}} >{{$p->name}}</option>

                                        @endforeach
                                    </select>

                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-province_id"> </strong>
                                    </div>

                                </div>
                                <div class="col-sm-6  my-1 my-sm-0">
                                    {{--<label for="county-input"--}}
                                    {{--class="col-12 col-form-label text-right">شهر </label>--}}
                                    <select id="county_id" name="county_id"
                                            class="px-4 form-control{{ $errors->has('county_id')  ? ' is-invalid' : '' }}">
                                        @if(   $player->county_id  )
                                            @foreach(\App\Models\County::where('province_id',$player->province_id)->get() as $c)

                                                <option value="{{$c->id}}" {{ $player->county_id==$c->id? ' selected ':''}}>{{$c->name}}</option>
                                            @endforeach
                                        @else
                                            <option value="0">انتخاب شهر</option>
                                        @endif
                                    </select>

                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-county_id"> </strong>
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-10 mx-auto      ">
                                <div class="my-2 form-outline">
                                <textarea id="description" rows="4"
                                          class="  px-4 form-control @error('description') is-invalid @enderror"
                                          name="description"
                                          autocomplete="description" autofocus>{{ $player->description }}</textarea>

                                    <label for="description"
                                           class="col-md-12 col-form-label form-label  text-md-right">
                                        توضیحات
                                    </label>
                                    <div class=" input-group my-1">

                                        <button class="btn btn-secondary rounded ms-auto" type="button"
                                                id="times-addon"
                                                onclick=" submitWithFiles(event,{
                                            'description':document.getElementById('description').value,
                                           })">ویرایش
                                        </button>
                                    </div>
                                </div>
                                <div class=" text-danger text-start small  col-12   " role="alert">
                                    <strong id="err-description"> </strong>
                                </div>
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

//                var myModal = new bootstrap.Modal(document.getElementById('renewModal'), {
//                    keyboard: false
//                });
//                myModal.show();
            });


            function remove(id) {
                document.querySelector('#loading').classList.remove('d-none');
//                event.preventDefault();
                data = {'id': id};
                axios.post("{{route('player.remove')}}", data, {})
                    .then((response) => {
//                        console.log(response);
                            document.querySelector('#loading').classList.add('d-none');
                            if (response.status === 200)
                                window.location = "{{url('panel/player')}}";
                        }
                    ).catch((error) => {
                    document.querySelector('#loading').classList.add('d-none');
                    let errors = '';
                    if (error.response && error.response.status === 422)
                        for (let idx in error.response.data.errors)
                            errors += error.response.data.errors[idx] + '<br>';
                    else {
                        errors = error;
                    }
                    window.showToast('danger', errors);
                });
            }

            function submitWithFiles(event, data) {
                document.querySelector('#loading').classList.remove('d-none');
                validInputs();
                event.preventDefault();
                data = {'id': "{{$player->id}}", ...data};
                axios.post("{{route('player.edit')}}", data, {
                    onUploadProgress: function (progressEvent) {
                        let percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
//                        console.log(percentCompleted);
                    }
                })

                    .then((response) => {
//                        console.log(response);
                            document.querySelector('#loading').classList.add('d-none');

                            if (response.status === 200) {

                                if (response.data && response.data.status === 'success' && response.data.msg)
                                    window.showToast('success', response.data.msg);
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