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
                                            crop-ratio="{{.85}}"
                                            link="null"
                                            preload="null"
                                            height="10" mode="create">

                            </image-uploader>


                            <image-uploader key="2"
                                            class="  col-sm-12 col-md-6 my-1     overflow-auto" id="logo"
                                            label="تصویر لوگو فروشگاه (اختیاری)"
                                            for-id="logo" ref="logoUploader"
                                            crop-ratio="{{1}}"
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


@push('scripts')
    <script>


        document.addEventListener("DOMContentLoaded", function (event) {
            addSMSBtnListener(
                null,
                null,
                "{{auth()->user()->phone}}"
            );
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
                else if (data[i].id === 'logo')
                    fd.append(data[i].name, app.$refs.logoUploader.getCroppedData());
                else if (['phone', 'phone_verify'].includes(data[i].name)) {
                    fd.append(data[i].name, f2e(data[i].value));
                }
                else
                    fd.append(data[i].name, data[i].value);
            }

            axios.post("{{route('shop.create')}}", fd, {
                onUploadProgress: function (progressEvent) {
                    let percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    console.log(percentCompleted);
                }
            })

                .then((response) => {
                        console.log(response);
                        document.querySelector('#loading').classList.add('d-none');

                        if (response.status === 200)
                            window.location = '{{url('panel/shop')}}'

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
@endpush