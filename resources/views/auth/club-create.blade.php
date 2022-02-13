@php

    if(auth()->user()){
    header("Location: " . URL::to('/'), true, 302);
            exit();
    }
@endphp

@extends('layouts.app')

@section('content')

    <div class="  my-3 position-relative">
        <div class="    m-2  position-absolute w-100   right-0 bottom-0" style="z-index: 10;">
            <div id="loading" class="spinner-border    text-danger d-none
               "
                 role="status">
                <span class="sr-only"></span>
            </div>
        </div>
        <div class="row w-100 mx-auto justify-content-center">
            <div class="col-md-8  ">
                <div class="btn-group w-100 my-2 mb-3">
                    <a href="/register"
                       class="btn px-1 {{  url()->current()==url('/register') ? 'btn-primary':'btn-outline-primary' }}">کاربر</a>
                    <a href="/register-player"
                       class="btn px-1 {{ url()->current()==url('/register-player') ? 'btn-primary':'btn-outline-primary' }}">بازیکن</a>
                    <a href="/register-coach"
                       class="btn px-1 {{ url()->current()==url('/register-coach') ? 'btn-primary':'btn-outline-primary' }}">مربی</a>
                    <a href="/register-club"
                       class="btn px-1 {{ url()->current()==url('/register-club') ? 'btn-primary':'btn-outline-primary' }}">باشگاه</a>
                    <a href="/register-shop"
                       class="btn px-1 {{ url()->current()==url('/register-shop') ? 'btn-primary':'btn-outline-primary' }}">فروشگاه</a>
                </div>
            </div>
            <div class="col-md-10  col-xl-6 col-xxl-6 ">
                <div class="card bg-light">
                    <h5 class="card-header text-center text-white bg-primary">ثبت باشگاه</h5>

                    <div class="card-body  ">


                        <form id="form-create" method="POST" action="{{ route('club.create') }}"
                              class="text-right  row">
                            @csrf

                            <div class="row">
                                {{--   style="  width:{{.85*10}}rem;min-height:{{10}}rem" --}}
                                <image-uploader key="-1"
                                                class=" my-1 col-6 mx-auto   overflow-auto" id="license"
                                                label="تصویر جواز کسب"
                                                for-id="img" ref="licenseUploader"
                                                crop-ratio="{{.85}}"
                                                link="null"
                                                preload="null"
                                                height="10" mode="create">

                                </image-uploader>
                            </div>
                            <div class="row">
                                @for ($i = 0; $i < Helper::$club_image_limit; $i++)

                                    <image-uploader key="{{$i}}" style=" min-width:{{1*10}}rem;min-height:{{10}}rem"
                                                    class="  col-sm-12 col-md-6  mx-auto my-1 overflow-auto"
                                                    id="img{{$i}}"
                                                    label="تصویر محیط باشگاه"
                                                    for-id="img{{$i}}" ref="imageUploader-img{{$i}}"
                                                    crop-ratio="{{1.2}}"
                                                    link="null"
                                                    preload="null"
                                                    height="10" mode="create">

                                    </image-uploader>

                                @endfor

                            </div>
                            <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-license"> </strong>
                                    </span>
                            <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-images"> </strong>
                                    </span>
                            <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-images.0"> </strong>
                                    </span>
                            <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-images.1"> </strong>
                                    </span>
                            <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-images.2"> </strong>
                                    </span>


                            <div class="col-md-10 mx-auto   ">
                                <div class="m-2 form-outline">

                                    <input id="name" type="text"
                                           class="  px-4 form-control @error('name') is-invalid @enderror"
                                           name="name"
                                           value="{{ old('name') }}" autocomplete="name" autofocus>
                                    <label for="name"
                                           class="col-md-12    form-label  text-md-right">نام باشگاه</label>
                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-name"> </strong>
                                </div>
                            </div>


                            <div class="col-md-10 mx-auto   ">

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

                            <div class="  my-2 ">

                                <label for="sport-input"
                                       class="col-12 col-form-label text-right">برنامه کاری</label>
                                <club-times ref="clubTimes"
                                            class="row border border-2 border-primary rounded-2 py-4  px-1"
                                            data="{{\App\Models\Sport::get(['id','name'])}}">

                                </club-times>

                                <div class=" text-danger text-start small  col-12   " role="alert">
                                    <strong id="err-times"> </strong>
                                </div>

                            </div>
                            <div class="col-md-10 row mx-auto my-2 px-0">
                                <div class="col-sm-6 my-1 my-sm-0 ">
                                    {{--<label for="province-input"--}}
                                    {{--class="col-12 col-form-label text-right">استان</label>--}}
                                    <select id="province_id" name="province_id" onchange="setCountyOptions(this.value)"
                                            class="px-4 form-control{{ $errors->has('province_id')  ? ' is-invalid' : '' }}">
                                        <option value="0">انتخاب استان</option>
                                        @foreach(\Illuminate\Support\Facades\DB::table('province')->get() as $p)
                                            <option value="{{$p->id}}"
                                                    {{ old('province_id')==$p->id? ' selected ':''}} >{{$p->name}}</option>

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
                                        @if(  $cId=\App\Models\County::find(old('county_id') ))

                                            <option value="{{$cId->id}}" selected>{{$cId->name}}</option>
                                        @else
                                            <option value="0">انتخاب شهر</option>
                                        @endif
                                    </select>

                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-county_id"> </strong>
                                    </div>

                                </div>
                            </div>


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
                                          autocomplete="description" autofocus>{{ old('description') }}
                                </textarea>
                                    <label for="description"
                                           class="col-md-12 col-form-label form-label  text-md-right">
                                        توضیحات
                                    </label>

                                </div>
                                <div class=" text-danger text-start small  col-12   " role="alert">
                                    <strong id="err-description"> </strong>
                                </div>
                            </div>

                            <div class="form-group   mb-0">
                                <div class="col-md-12  mt-2">
                                    <button onclick=" submitWithFiles(event)" type="button"
                                            class="btn btn-success btn-block font-weight-bold py-3">
                                        ثبت
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
@endsection

@section('scripts')
    <script>


        document.addEventListener("DOMContentLoaded", function (event) {
            addSMSBtnListener();
        });

        function submitWithFiles(event) {
            document.querySelector('#loading').classList.remove('d-none');
            validInputs();

            event.preventDefault();
            let fd = new FormData();
            let data = document.querySelector('#form-create').querySelectorAll('input, textarea, select');
            for (let i in data) {
                if (data[i].type === 'radio' && data[i].checked === false)
                    continue;
                if (data[i].id === 'license')
                    fd.append(data[i].name, app.$refs.licenseUploader.getCroppedData());
                else if (data[i].id && data[i].id.includes('img') && !data[i].id.includes('file')) {

                    let res = app.$refs['imageUploader-' + data[i].id].getCroppedData();
                    if (res)
                        fd.append('images[]', res);

                } else if (['phone', 'phone_verify',].includes(data[i].name)) {
                    fd.append(data[i].name, f2e(data[i].value));
                }
                else
                    fd.append(data[i].name, data[i].value);
            }
            fd.append('times', app.$refs.clubTimes.getTimes());
            axios.post("{{route('club.create')}}", fd, {
                onUploadProgress: function (progressEvent) {
                    let percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    console.log(percentCompleted);
                }
            })

                .then((response) => {
//                        console.log(response);
                        document.querySelector('#loading').classList.add('d-none');

                        if (response.status === 200)
                            window.location = '{{url('panel/club')}}'

                    }
                ).catch((error) => {
                document.querySelector('#loading').classList.add('d-none');
//                console.log(error.response.data.errors);

                invalidInputs(error.response.data.errors);
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
@endsection