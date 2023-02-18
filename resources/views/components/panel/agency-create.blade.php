@cannot('createItem',[\App\Models\User::class,\App\Models\Agency::class,false])

    @php
        header("Location: " . URL::to('/panel/agencies'), true, 302);
        exit();
    @endphp

@else
    @php
        $user=auth()->user();
        $provinces=\App\Models\Province::select('id','name')->get();
        $counties=\App\Models\Province::select('id','name')->get();
    @endphp

    {{--{{json_encode( $images)}}--}}

    <div class="  my-2 ">
        <div class="row mx-auto justify-content-center">
            <div class="col-md-10 col-lg-8 col-sm-12 col-xl-6  ">
                <div class="card bg-light">
                    <div class="card-header text-center text-white bg-primary">
                        <div class="   my-2 text-center">

                            <h5> ثبت نمایندگی</h5>

                        </div>
                    </div>

                    <div class="card-body  ">


                        <form id="form-create" method="POST" action=" "
                              class="text-right  row">
                            @csrf

                            <div class="col-md-10  mx-auto  ">
                                <div class="    ">
                                    {{--<label for="province-input"--}}
                                    {{--class="col-12 col-form-label text-right">استان</label>--}}
                                    <small class="text-primary">ابتدا از قسمت کاربران، کاربر با نقش "مالک نمایندگی"
                                        بسازید
                                    </small>
                                    <select id="owner" name="owner"
                                            class="px-4 my-2 form-control{{ $errors->has('owner')  ? ' is-invalid' : '' }}">
                                        <option value="">انتخاب مالک</option>
                                        @foreach(\App\Models\User::select('id','name','family','username','phone')->where('role','ag')->get() as $a)
                                            <option value="{{$a->id}}"
                                                    {{ old('owner')==$a->id? ' selected ':''}} >{{($a->name || $a->family) ?($a->name .' '. $a->family) : ($a->username? $a->username: $a->phone)}}</option>

                                        @endforeach
                                    </select>

                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-owner"> </strong>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-10  mx-auto  ">
                                <div class="    ">
                                    {{--<label for="province-input"--}}
                                    {{--class="col-12 col-form-label text-right">استان</label>--}}

                                    <select id="parent" name="parent"
                                            class="px-4 my-2 form-control{{ $errors->has('parent')  ? ' is-invalid' : '' }}">
                                        <option value="">انتخاب نمایندگی والد</option>
                                        @foreach(\App\Models\Agency::select('id','name','province_id','county_id'  )->get() as $a)
                                            @php
                                                $name=$a->name;
                                                $province=optional($provinces->where('id',$a->province_id)->first())->name;
                                                $county=optional($counties->where('id',$a->county_id)->first())->name;
                                            @endphp
                                            <option value="{{$a->id}}"
                                                    {{ old('parent')==$a->id? ' selected ':''}} >{{  $name.($name && ($province || $county)?' | ':'').$province.($province && $county? ' | ':'').$county  }}</option>

                                        @endforeach
                                    </select>

                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-parent"> </strong>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-10  mx-auto  ">
                                <div class="   my-2 form-outline input-group  ">

                                    <input id="name" type="text"
                                           class="  px-4 form-control @error('name') is-invalid @enderror"
                                           name="name"
                                           value="{{ old('name') }}" autocomplete="name" autofocus>
                                    <label for="name"
                                           class="col-md-12    form-label  text-md-right">نام نمایندگی </label>

                                </div>
                                <div class=" text-danger text-start small  " role="alert">
                                    <strong id="err-name"> </strong>
                                </div>
                            </div>

                            <div class="col-md-10  mx-auto  ">
                                <div class=" my-2 form-outline input-group  ">

                                    <input id="phone" type="text"
                                           class="  px-4 form-control @error('phone') is-invalid @enderror"
                                           name="phone"
                                           value="{{ old('phone') }}" autocomplete="phone" autofocus>
                                    <label for="phone"
                                           class="col-md-12    form-label  text-md-right ">شماره تماس
                                    </label>

                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-phone"> </strong>
                                </div>
                            </div>
                            <div class="col-md-10  mx-auto  ">
                                <div class=" my-2 form-outline input-group  ">

                                    <input id="email" type="text"
                                           class="  px-4 form-control @error('email') is-invalid @enderror"
                                           name="email"
                                           value="{{ old('email') }}" autocomplete="email" autofocus>
                                    <label for="email"
                                           class="col-md-12    form-label  text-md-right ">ایمیل
                                    </label>
                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-email"> </strong>
                                </div>
                            </div>
                            <div class="col-md-10 row mx-auto my-2 px-0">
                                <div class="col-sm-6  my-2 my-sm-0">
                                    {{--<label for="province-input"--}}
                                    {{--class="col-12 col-form-label text-right">استان</label>--}}
                                    <select id="province" name="province" onchange="setCountyOptions(this.value)"
                                            class="px-4 form-control{{ $errors->has('province')  ? ' is-invalid' : '' }}">
                                        <option value="">انتخاب استان</option>
                                        @foreach(\Illuminate\Support\Facades\DB::table('province')->get() as $p)
                                            <option value="{{$p->id}}"
                                                    {{ old('province')==$p->id? ' selected ':''}} >{{$p->name}}</option>

                                        @endforeach
                                    </select>

                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-province"> </strong>
                                    </div>

                                </div>
                                <div class="col-sm-6  my-2 my-sm-0">
                                    {{--<label for="county-input"--}}
                                    {{--class="col-12 col-form-label text-right">شهر </label>--}}
                                    <select id="county" name="county"
                                            class="px-4   form-control{{ $errors->has('county')  ? ' is-invalid' : '' }}">
                                        <option value="">انتخاب شهر</option>
                                        @if(  $cId=\App\Models\County::find(old('county') ))

                                            <option value="{{$cId->id}}" selected>{{$cId->name}}</option>
                                        @else
                                        @endif
                                    </select>

                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-county"> </strong>
                                    </div>

                                </div>
                            </div>
                            <div class="m-2 col-md-10 mx-auto" id="map"></div>

                            <div class="col-md-10 mx-auto    ">
                                <div class=" my-2 form-outline">
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
                            <div class="col-md-10 mx-auto  my-2  ">
                                <div class="m-2 mx-0 form-outline">
                                <textarea id="description" rows="4"
                                          class="  px-4 form-control @error('description') is-invalid @enderror"
                                          name="description"
                                          autocomplete="description" autofocus>{{ old('description') }}</textarea>

                                    <label for="description"
                                           class="col-md-12 col-form-label form-label  text-md-right">
                                        توضیحات
                                    </label>

                                </div>
                                <div class=" text-danger text-start small  col-12   " role="alert">
                                    <strong id="err-description"> </strong>
                                </div>
                            </div>
                            <div class=" col-md-10 mx-auto  my-2  ">
                                <div class="  ">
                                    <button onclick=" submitWithFiles(event,{'upload_pending': true})" type="button"
                                            class="btn btn-success btn-block font-weight-bold py-3">
                                        ثبت نام
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
                    "{{auth()->user()->name}}",
                    "{{auth()->user()->family}}",
                    "{{auth()->user()->phone}}"
                );

                let map = leaflet('{{null}}', '{{'مکان نمایندگی'}}', 'edit');
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
                    else if (['phone', 'phone_verify',].includes(data[i].name)) {
                        fd.append(data[i].name, f2e(data[i].value));
                    }
                    else
                        fd.append(data[i].name, data[i].value);
                }
                for (let i in extra)
                    fd.append(i, extra[i]);

                fd.append('location', leaflet(null, null, 'getLocation', map));

                axios.post("{{route('agency.create')}}", fd, {})

                    .then((response) => {
//                            console.log(response);
                            document.querySelector('#loading').classList.add('d-none');

                            if (response.status == 200) {
//                                if (response.data && response.data.res)
//                                    window.showToast('success', response.data.res);
//                                else
//                                    window.location.reload();
                                window.location = '{{url('panel/agency/edit' )}}/' + response.data.id
                            }

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
                let sel2 = document.querySelector('#county');
                while (sel2.firstChild && sel2.removeChild(sel2.firstChild)) ;
                sel2.innerHTML = (`<option value='' pvalue=''>انتخاب شهر</option>`);
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

@endcannot