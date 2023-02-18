@php
    $agency=\App\Models\Agency::find($param);
@endphp
@if(!$agency)
    <div class="text-center font-weight-bold text-danger mt-5">نمایندگی یافت نشد</div>
@else
    @cannot('editItem',[\App\Models\User::class,$agency,false ])
        @php
            header("Location: " . URL::to('/panel/agencies'), true, 302);
        exit();
        @endphp
    @endcannot
    {{--{{json_encode( $images)}}--}}

    @php
        $provinces=\App\Models\Province::select('id','name')->get();
    @endphp


    <div class="  my-3 ">
        <div class="row mx-auto justify-content-center">
            <div class="col-md-10 col-sm-12 col-lg-8 col-xl-6">
                <div class="card bg-light">
                    <div class="  card-header   text-white bg-primary  d-flex justify-content-between  ">

                        <label for="delete-input"
                               class="  ">{{$agency->name }}</label>
                        <button class="btn btn-sm btn-danger rounded  ms-2 font-weight-bold" type="button"
                                id="delete-addon"
                                onclick=" window.showDialog('confirm','از حذف اطمینان دارید؟',()=>remove,{{$agency->id}})">
                            حذف
                        </button>


                    </div>


                    <div class="card-body  ">


                        <form id="form-create" method="POST" action="{{ route('agency.edit') }}"
                              class="text-right  row">
                            @csrf

                            <div class="row">
                                {{--   style="  width:{{.85*10}}rem;min-height:{{10}}rem" --}}

                            </div>
                            <div class="col-md-12  mx-auto  ">
                                <small class="mx-3"> مالک</small>
                                <div class=" m-2  input-group   ">
                                    {{--<label for="province-input"--}}
                                    {{--class="col-12 col-form-label text-right">استان</label>--}}
                                    <select id="owner_id" name="owner_id"
                                            class="   form-control{{ $errors->has('owner')  ? ' is-invalid' : '' }}">
                                        <option value="">انتخاب مالک</option>
                                        @foreach(\App\Models\User::select('id','name','family','username','phone')->where('role','ag')->get() as $a)
                                            <option value="{{$a->id}}"
                                                    {{ $agency->owner_id==$a->id? ' selected ':''}} >{{($a->name || $a->family) ?($a->name .' '. $a->family) : ($a->username? $a->username: $a->phone)}}</option>

                                        @endforeach
                                    </select>

                                    <button class="btn btn-secondary rounded" type="button"
                                            id="name-addon"
                                            onclick=" submitWithFiles(event,{'name':document.getElementById('name').value})">

                                        ویرایش
                                    </button>
                                </div>
                                <div class=" text-danger text-start small  col-12   " role="alert">
                                    <strong id="err-owner_id"> </strong>
                                </div>

                            </div>

                            <div class="col-md-12 mx-auto   ">
                                <div class="m-2  form-outline input-group">

                                    <input id="name" type="text"
                                           class="  px-4 form-control @error('name') is-invalid @enderror"
                                           name="name"
                                           value="{{ $agency->name }}" autocomplete="name" autofocus>
                                    <label for="name"
                                           class="col-md-12    form-label  text-md-right">نام نمایندگی</label>
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
                            <div class="col-md-12 mx-auto   ">
                                <div class="m-2  form-outline input-group">

                                    <input id="email" type="text"
                                           class="  px-4 form-control @error('email') is-invalid @enderror"
                                           name="email"
                                           value="{{ $agency->email }}" autocomplete="email" autofocus>
                                    <label for="email"
                                           class="col-md-12    form-label  text-md-right">ایمیل</label>
                                    <button class="btn btn-secondary rounded" type="button"
                                            id="email-addon"
                                            onclick=" submitWithFiles(event,{'email':document.getElementById('email').value})">
                                        ایمیل
                                    </button>

                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-email"> </strong>
                                </div>
                            </div>


                            <div class="col-md-12 mx-auto     py-3">

                                <div class="m-2   form-outline input-group">
                                    <input id="phone" type="tel"
                                           class="  px-4 form-control @error('phone') is-invalid @enderror"
                                           name="phone"
                                           value="{{ $agency->phone }}"
                                           autocomplete="phone">
                                    <label for="phone"
                                           class="col-md-12  form-label text-md-right">شماره همراه</label>
                                    <button class="btn btn-secondary rounded" type="button"
                                            id="phone-addon"
                                            onclick=" submitWithFiles(event,{
                                                'phone':f2e(document.getElementById('phone').value),

                                           })">
                                        ویرایش
                                    </button>


                                </div>
                                <div class=" text-danger text-start small col-12    " role="alert">
                                    <strong id="err-phone"> </strong>
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
                                        <option value="">انتخاب استان</option>
                                        @foreach(\Illuminate\Support\Facades\DB::table('province')->get() as $p)
                                            <option value="{{$p->id}}"
                                                    {{ $agency->province_id==$p->id? ' selected ':''}} >{{$p->name}}</option>

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

                                        <option value="">انتخاب شهر</option>
                                        @foreach(\App\Models\County::where('province_id',$agency->province_id)->get() as $c)

                                            <option value="{{$c->id}}" {{ $agency->county_id==$c->id? ' selected ':''}}>{{$c->name}}</option>
                                        @endforeach


                                    </select>

                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-county_id"> </strong>
                                    </div>

                                </div>
                                <div class="col-md-12      ">
                                    <div class="m-2" id="map"></div>
                                    <div class=" text-danger text-start small     " role="alert">
                                        <strong id="err-location"> </strong>
                                    </div>
                                    <div class="m-1 my-2 form-outline">
                                <textarea id="address" rows="3"
                                          class=" my-3 px-4 form-control @error('address') is-invalid @enderror"
                                          name="address"
                                          autocomplete="address" autofocus>{{ $agency->address }}</textarea>

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
                                <div class="m-2 form-outline">
                                <textarea id="description" rows="4"
                                          class="  px-4 form-control @error('description') is-invalid @enderror"
                                          name="description"
                                          autocomplete="description" autofocus>{{ $agency->description }}</textarea>

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
                    null,
                    null,
                    "{{auth()->user()->phone}}"
                );
                let map = leaflet('{{$agency->location}}', '{{$agency->name}}', 'edit');
            });


            function remove(id) {
                document.querySelector('#loading').classList.remove('d-none');

                data = {'id': id};
                axios.post("{{route('agency.remove')}}", data, {})
                    .then((response) => {
//                        console.log(response);
                            document.querySelector('#loading').classList.add('d-none');
                            if (response.status === 200)
                                window.location = "{{url('panel/club')}}";
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

                event.preventDefault();
                data = {'id': "{{$agency->id}}", ...data};
                axios.post("{{route('agency.edit')}}", data, {
                    onUploadProgress: function (progressEvent) {
                        let percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
//                        console.log(percentCompleted);
                    }
                })

                    .then((response) => {
//                        console.log(response);
                            document.querySelector('#loading').classList.add('d-none');

                            if (response.status === 200)
                                window.location.reload();
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

@endif