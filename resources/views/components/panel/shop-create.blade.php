@cannot('createItem',[\App\Models\User::class,\App\Models\Shop::class,false ])
    @php
        header("Location: " . URL::to('/panel/shops'), true, 302);
    exit();
    @endphp
@endcannot
<div class="  my-3 ">
    <div class="row mx-auto justify-content-center">
        <div class="col-md-10 col-sm-12  ">
            <div class="card bg-light">
                <h5 class="card-header text-center text-white bg-primary">ثبت فروشگاه</h5>

                <div class="card-body  ">


                    <form id="form-create" method="POST" action="{{ route('shop.create') }}" class="text-right  row">
                        @csrf

                        <div class="row mx-auto">
                            <image-uploader key="1"
                                            class="col-sm-12 col-md-6  my-1    overflow-auto" id="license"
                                            label="تصویر جواز کسب"
                                            for-id="license" ref="licenseUploader"
                                            crop-ratio="{{Helper::$cropsRatio['profile']}}"
                                            link="null"
                                            preload="null"
                                            height="10" mode="create">

                            </image-uploader>


                            <image-uploader key="2"
                                            class="  col-sm-12 col-md-6 my-1     overflow-auto" id="logo"
                                            label="تصویر لوگو فروشگاه (اختیاری)"
                                            for-id="logo" ref="logoUploader"
                                            crop-ratio="{{Helper::$cropsRatio['logo']}}"
                                            link="null"
                                            preload="null"
                                            height="10" mode="create">

                            </image-uploader>


                        </div>

                        <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-license"> </strong>
                                    </span>
                        <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-logo"> </strong>
                                    </span>


                        <div class="col-md-10 mx-auto   ">
                            <div class="m-2 form-outline">

                                <input id="name" type="text"
                                       class="  px-4 form-control @error('name') is-invalid @enderror"
                                       name="name"
                                       value="{{ old('name') }}" autocomplete="name" autofocus>
                                <label for="name"
                                       class="col-md-12    form-label  text-md-right">نام فروشگاه</label>
                            </div>
                            <div class=" text-danger text-start small     " role="alert">
                                <strong id="err-name"> </strong>
                            </div>
                        </div>


                        <div class="col-md-10 mx-auto   ">
                            <div class="  form-outline">
                                <input id="myinfo" type="checkbox"
                                       class="    "
                                       name="myinfo"
                                       value="{{ old('myinfo') }}" autocomplete="myinfo" autofocus>
                                <label for="myinfo"
                                       class="  form-label  text-md-right"> شماره خودم </label>
                            </div>
                            <div class="m-2   form-outline input-group">
                                <input id="phone" type="tel"
                                       class="  px-4 form-control @error('phone') is-invalid @enderror"
                                       name="phone"
                                       value="{{ old('phone') }}"
                                       autocomplete="phone">
                                <label for="phone"
                                       class="col-md-12  form-label text-md-right">شماره همراه</label>
                                <button class="btn btn-secondary rounded px-1 px-sm-2" type="button"
                                        id="phone_verify-addon1">

                                    دریافت کد تایید
                                </button>

                            </div>
                            <div class=" text-danger text-start small col-12    " role="alert">
                                <strong id="err-phone"> </strong>
                            </div>
                        </div>
                        <div class=" col-md-10 mx-auto  ">
                            <div class=" form-outline m-2   ">

                                <input id="phone_verify" type="number"
                                       class=" px-4 form-control @error('phone_verify') is-invalid @enderror"
                                       name="phone_verify">
                                <label for="phone_verify"
                                       class="col-md-12  form-label text-md-right">کد تایید</label>
                            </div>
                            <div class=" text-danger text-start small  col-12   " role="alert">
                                <strong id="err-phone_verify"> </strong>
                            </div>
                        </div>

                        <div class="col-md-10 row mx-auto my-2 px-0">
                            <div class="col-sm-6 my-1 my-sm-0 ">
                                {{--<label for="province-input"--}}
                                {{--class="col-12 col-form-label text-right">استان</label>--}}
                                <select id="province" name="province" onchange="setCountyOptions(this.value)"
                                        class="px-4 form-control{{ $errors->has('province')  ? ' is-invalid' : '' }}">
                                    <option value="0">انتخاب استان</option>
                                    @foreach(\Illuminate\Support\Facades\DB::table('province')->get() as $p)
                                        <option value="{{$p->id}}"
                                                {{ old('province')==$p->id? ' selected ':''}} >{{$p->name}}</option>

                                    @endforeach
                                </select>

                                <div class=" text-danger text-start small  col-12   " role="alert">
                                    <strong id="err-province"> </strong>
                                </div>

                            </div>
                            <div class="col-sm-6  my-1 my-sm-0">
                                {{--<label for="county-input"--}}
                                {{--class="col-12 col-form-label text-right">شهر </label>--}}
                                <select id="county" name="county"
                                        class="px-4 form-control{{ $errors->has('county')  ? ' is-invalid' : '' }}">
                                    @if(  $cId=\App\Models\County::find(old('county') ))

                                        <option value="{{$cId->id}}" selected>{{$cId->name}}</option>
                                    @else
                                        <option value="0">انتخاب شهر</option>
                                    @endif
                                </select>

                                <div class=" text-danger text-start small  col-12   " role="alert">
                                    <strong id="err-county"> </strong>
                                </div>

                            </div>
                        </div>
                        <div class="m-2 col-md-10 mx-auto" id="map"></div>

                        <div class="col-md-10 mx-auto    ">
                            <div class="m-2 form-outline">
                                <textarea id="address" rows="3"
                                          class="  px-4 form-control @error('address') is-invalid @enderror"
                                          name="address"
                                          autocomplete="address" autofocus>{{ old('address') }}</textarea>

                                <label for="address"
                                       class="col-md-12 col-form-label form-label  text-md-right">
                                    آدرس
                                </label>

                            </div>
                            <div class=" text-danger text-start small  col-12   " role="alert">
                                <strong id="err-address"> </strong>
                            </div>
                        </div>
                        <div class="col-md-10 mx-auto    ">
                            <div class="m-2 form-outline">
                                <textarea id="description" rows="4"
                                          class="  px-4 form-control @error('description') is-invalid @enderror"
                                          name="description"
                                          autocomplete="description" autofocus>{{ old('description') }}</textarea>

                                <label for="description"
                                       class="col-md-12 col-form-label form-label  text-md-right">
                                    توضیحات (سوابق، ویژگی ها...)
                                </label>

                            </div>
                            <div class=" text-danger text-start small  col-12   " role="alert">
                                <strong id="err-description"> </strong>
                            </div>
                        </div>
                        {{--//payment--}}
                        <div class="col-md-10 mx-auto  my-2  ">
                            <div class="modal-dialog modal-dialog-centered  ">
                                <div class="modal-content bg-light-transparent">
                                    <div class="modal-header bg-primary py-2">
                                        <h5 class="modal-title text-white font-weight-bold"
                                            id="exampleModalLabel">نوع
                                            اشتراک</h5>

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

                                                <button class="btn btn-secondary rounded px-2 px-sm-3" type="button"
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

                                </div>
                            </div>
                        </div>
                        <div class="form-group   mb-0">
                            <div class="col-md-12  mt-2">
                                <button onclick=" submitWithFiles(event,{upload_pending:true})" type="button"
                                        class="btn btn-success btn-block font-weight-bold py-3">
                                    پرداخت و ثبت
                                </button>
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
            let map = leaflet('{{null}}', '{{'مکان فروشگاه'}}', 'edit');

        });

        function submitWithFiles(event, extra = {}) {
            document.querySelector('#loading').classList.remove('d-none');
            validInputs();

            event.preventDefault();
            let fd = new FormData();
            let data = document.querySelector('#form-create').querySelectorAll('input, textarea, select');
            for (let i in data) {
                if (data[i].type === 'radio' && data[i].checked === false)
                    continue;
                if (data[i].id === 'license' && extra['upload_pending'] !== true)
                    fd.append(data[i].name, app.$refs.licenseUploader.getCroppedData());
                else if (data[i].id === 'logo' && extra['upload_pending'] !== true)
                    fd.append(data[i].name, app.$refs.logoUploader.getCroppedData());
                else if (['phone', 'phone_verify'].includes(data[i].name)) {
                    fd.append(data[i].name, f2e(data[i].value));
                }
                else
                    fd.append(data[i].name, data[i].value);
            }
            for (let i in extra)
                fd.append(i, extra[i]);
            fd.append('location', leaflet(null, null, 'getLocation', map));
            axios.post("{{route('shop.create')}}", fd, {
                onUploadProgress: function (progressEvent) {
                    let percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
//                    console.log(percentCompleted);
                }
            })

                .then((response) => {
//                        console.log(response);
                        document.querySelector('#loading').classList.add('d-none');

                        if (response.status == 200)
                            if (response.data.resume == true)
                                submitWithFiles(event);
                            else
                                window.location = response.data.url;

                    }
                ).catch((error) => {
                document.querySelector('#loading').classList.add('d-none');
//                console.log(error.response.data.errors);
                let errors = '';
                invalidInputs(error.response.data.errors);
                if (error.response && error.response.status === 422)
                    for (let idx in error.response.data.errors)
                        errors += '' + error.response.data.errors[idx] + '<br>';
                else {
                    errors = error;
                }
                window.showToast('danger', errors);
            });
        }

                @php( $counties=\Illuminate\Support\Facades\DB::table('county')->get())

        let counties = @json($counties);


        function setCountyOptions(selValue) {
            let sel2 = document.querySelector('#county');
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