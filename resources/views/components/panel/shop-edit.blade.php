@php
    $shop=\App\Models\Shop::where('id',$param)->with('docs:id,type_id,docable_id')->first();
@endphp
@if(!$shop)
    <div class="text-center font-weight-bold text-danger mt-5">فروشگاه یافت نشد</div>

@else
    @cannot('editItem',[\App\Models\User::class,$shop,false ])
        @php
            header("Location: " . URL::to('/panel/shops'), true, 302);
        exit();
        @endphp
    @endcannot

    @php
        $user=auth()->user();
        if ($shop){
        $docs=collect(  $shop->docs );
         $license=$docs->where('type_id',Helper::$docsMap['license'])->first() ;
         $logo=$docs->where('type_id',Helper::$docsMap['logo'])->first() ;

       if(!$shop->expires_at)
            $expire_days=0;
        else{
        $now=\Carbon\Carbon::now();
            $expire_days= $now->diffInDays(\Carbon\Carbon::createFromTimestamp($shop->expires_at),false);
         if($expire_days<0)
            $expire_days=0;
        }
        }

    @endphp
    {{--{{json_encode( $images)}}--}}




    <div class="  my-3 ">
        <div class="row mx-auto justify-content-center">
            <div class="col-md-10 col-sm-12  ">
                <div class="card bg-light">
                    <div class="  card-header   text-white bg-primary  d-flex justify-content-between  ">
                        <div class="    ">
                            <label for="delete-input"
                                   class="  ">{{$shop->name }}</label>
                            <button class="btn btn-sm btn-danger rounded  ms-2 font-weight-bold" type="button"
                                    id="delete-addon"
                                    onclick=" window.showDialog('confirm','از حذف اطمینان دارید؟',()=>remove,{{$shop->id}})">
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
                                        @foreach(\App\Models\Setting::where('key','like','shop%')->where('key','like','%_price')->orderBy('id','ASC')->get() as $idx=> $sub)
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
                                                            onclick=" calculateCoupon(event,{'coupon':document.getElementById('coupon').value,'type':'shop', })">

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
                                                    onclick=" makePayment(event,{'coupon':document.getElementById('coupon').value,'type':'shop','month':document.querySelector('input[name=renew-month]:checked').value,'id':'{{$shop->id}}' })"
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
                                <image-uploader
                                        class=" my-1 col-md-8 mx-auto   overflow-auto" id="license"
                                        label="تصویر جواز کسب"
                                        for-id="{{$license?$license->id:''}}" ref="licenseUploader"
                                        crop-ratio="{{.85}}"
                                        link="{{route('shop.edit')}}"
                                        type="{{Helper::$docsMap['license']}}"
                                        required="yes"
                                        preload="{{$license?asset('storage')."/$license->type_id/$license->id.jpg":''}}"
                                        height="10" mode="edit">

                                </image-uploader>
                            </div>
                            <div class="row">

                                <image-uploader
                                        class=" my-1 col-md-8 mx-auto   overflow-auto" id="logo"
                                        label="تصویر لوگو فروشگاه (اختیاری)"
                                        for-id="{{$logo?$logo->id:$shop->id}}" ref="profileUploader"
                                        crop-ratio="{{1}}"
                                        link="{{route('shop.edit')}}"
                                        type="{{Helper::$docsMap['logo']}}"
                                        required=""
                                        preload="{{$logo?asset('storage')."/$logo->type_id/$logo->id.jpg":''}}"
                                        height="10" mode="edit">

                                </image-uploader>


                            </div>
                            <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-img"> </strong>
                                    </span>


                            <div class="col-md-12 mx-auto   ">
                                <div class="my-2  form-outline input-group">

                                    <input id="name" type="text"
                                           class="  px-4 form-control @error('name') is-invalid @enderror"
                                           name="name"
                                           value="{{ $shop->name }}" autocomplete="name" autofocus>
                                    <label for="name"
                                           class="col-md-12    form-label  text-md-right">نام فروشگاه</label>
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


                            <div class="col-md-12 mx-auto   border border-primary rounded-3    py-3">

                                <div class="my-2   form-outline input-group">
                                    <input id="phone" type="tel"
                                           class="  px-4 form-control @error('phone') is-invalid @enderror"
                                           name="phone"
                                           value="{{ $shop->phone }}"
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

                                <div class=" col-md-12 mx-auto  ">
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


                            <div class="col-md-12 row mx-auto my-2 px-0 border border-primary rounded-3">
                                <div class=" input-group my-2">
                                    <label for="addres-input"
                                           class="  ">آدرس</label>
                                    <button class="btn btn-secondary rounded ms-auto" type="button"
                                            id="addres-addon"
                                            onclick=" submitWithFiles(event,{
                                            'county_id':document.getElementById('county_id').value,
                                            'province_id':document.getElementById('province_id').value,
                                            'address':document.getElementById('address').value,
                                            'location':leaflet(null,null,'getLocation',map)
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
                                                    {{ $shop->province_id==$p->id? ' selected ':''}} >{{$p->name}}</option>

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
                                        @if(   $shop->county_id  )
                                            @foreach(\App\Models\County::where('province_id',$shop->province_id)->get() as $c)

                                                <option value="{{$c->id}}" {{ $shop->county_id==$c->id? ' selected ':''}}>{{$c->name}}</option>
                                            @endforeach
                                        @else
                                            <option value="0">انتخاب شهر</option>
                                        @endif
                                    </select>

                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-county_id"> </strong>
                                    </div>

                                </div>
                                <div class="col-md-12      ">
                                    <div class="m-2" id="map"></div>
                                    <div class="  my-2 form-outline">
                                <textarea id="address" rows="3"
                                          class="  px-4 form-control @error('address') is-invalid @enderror"
                                          name="address"
                                          autocomplete="address" autofocus>{{ $shop->address }}</textarea>

                                        <label for="address"
                                               class="col-md-12 col-form-label form-label  text-md-right">
                                            آدرس
                                        </label>

                                    </div>
                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-address"> </strong>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12 mx-auto    ">
                                <div class="my-2 form-outline">
                                <textarea id="description" rows="4"
                                          class="  px-4 form-control @error('description') is-invalid @enderror"
                                          name="description"
                                          autocomplete="description" autofocus>{{ $shop->description }}</textarea>

                                    <label for="description"
                                           class="col-md-12 col-form-label form-label  text-md-right">
                                        توضیحات
                                    </label>
                                    <div class=" input-group my-1">

                                        <button class="btn btn-secondary rounded ms-auto" type="button"
                                                id="description-addon"
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
                    null,
                    null,
                    "{{auth()->user()->phone}}"
                );
                let map = leaflet('{{$shop->location}}', '{{$shop->name}}', 'edit');
            });


            function remove(id) {
                document.querySelector('#loading').classList.remove('d-none');

                data = {'id': id};
                axios.post("{{route('shop.remove')}}", data, {})
                    .then((response) => {
//                        console.log(response);
                            document.querySelector('#loading').classList.add('d-none');
                            if (response.status === 200)
                                window.location = "{{url('panel/shop')}}";
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
                data = {'id': "{{$shop->id}}", ...data};
                axios.post("{{route('shop.edit')}}", data, {
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

                    @php( $counties=\Illuminate\Support\Facades\DB::table('county')->get())

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