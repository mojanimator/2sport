@cannot('createItem',[\App\Models\User::class,\App\Models\User::class,false ])
    @php
        header("Location: " . URL::to('/panel/users'), true, 302);
    exit();
    @endphp
@endcannot
{{--{{json_encode( $images)}}--}}

<div class="  my-3 ">
    <div class="row mx-auto justify-content-center">
        <div class="col-md-10 col-sm-12  ">
            <div class="card bg-light">
                <div class="card-header text-center text-white bg-primary">
                    <div class="   my-2 text-center">

                        <h5> ثبت کاربر</h5>

                    </div>
                </div>

                <div class="card-body  ">


                    <form id="form-create" method="POST" action=" "
                          class="text-right  row">
                        @csrf

                        <div class="col-md-10  mx-auto  ">
                            <div class=" mb-2 form-outline input-group  ">

                                <input id="name" type="text"
                                       class="  px-4 form-control @error('name') is-invalid @enderror"
                                       name="name"
                                       value="{{ old('name') }}" autocomplete="name" autofocus>
                                <label for="name"
                                       class="col-md-12    form-label  text-md-right">نام </label>

                            </div>
                            <div class=" text-danger text-start small     " role="alert">
                                <strong id="err-name"> </strong>
                            </div>
                        </div>

                        <div class="col-md-10  mx-auto  ">
                            <div class=" my-2 form-outline input-group  ">

                                <input id="family" type="text"
                                       class="  px-4 form-control @error('family') is-invalid @enderror"
                                       name="family"
                                       value="{{ old('family') }}" autocomplete="family" autofocus>
                                <label for="family"
                                       class="col-md-12    form-label  text-md-right">نام خانوادگی </label>

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
                                       value="{{ old('username') }}" autocomplete="username" autofocus>
                                <label for="username"
                                       class="col-md-12    form-label  text-md-right">نام کاربری </label>


                            </div>
                            <div class=" text-danger text-start small     " role="alert">
                                <strong id="err-username"> </strong>
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

                        <div class="col-md-10 my-2 mx-auto   border border-primary rounded-3    py-3">

                            <div class="my-2 mb-4  form-outline input-group">
                                <input id="phone" type="tel"
                                       class="  px-4 form-control @error('phone') is-invalid @enderror"
                                       name="phone"
                                       value="{{ old('phone') }}"
                                       autocomplete="phone">
                                <label for="phone"
                                       class="col-md-12  form-label text-md-right">شماره همراه</label>


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

                        <div class="col-md-10 my-2 mx-auto   border border-primary rounded-3    py-3 my-2">
                            <div class="  mx-auto  ">
                                <div class=" my-2 form-outline input-group  ">

                                    <input id="sheba" type="text"
                                           class="  px-4 form-control @error('sheba') is-invalid @enderror"
                                           name="sheba"
                                           value="{{ old('sheba') }}" autocomplete="sheba" autofocus>
                                    <label for="sheba"
                                           class="col-md-12    form-label  text-md-right">شماره شبا (بدون
                                        IR)</label>


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
                                           value="{{ old('cart') }}" autocomplete="cart" autofocus>
                                    <label for="cart"
                                           class="col-md-12    form-label  text-md-right">شماره کارت </label>


                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-cart"> </strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10 mx-auto   p-2  my-2   border border-primary rounded-3">

                            <div class="  my-2  shadow-0 p-2   ">
                                @foreach(Helper::$roles as $role=>$rname)
                                    @can('createItem',[\App\Models\User::class,\App\Models\User::class,false,(object)['role'=>$role]])
                                        <span class="d-inline-block">
                                        <input type="radio" class="form-check-input  " name="role" value="{{$role}}"
                                               id="{{"option-$role"}}"
                                               autocomplete="off" {{$role=='us'?  'checked':''}}/>
                                        <label class="form-check-label me-4" for="{{"option-$role"}}">{{$rname}}</label>
                                        </span>
                                    @endcan
                                @endforeach

                            </div>
                            <div class=" text-danger text-start small     " role="alert">
                                <strong id="err-role"> </strong>
                            </div>
                        </div>
                        <div class="form-group  p-0 my-1">
                            <div class="col-md-12  mt-2">
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
                    errors = error;
                }
                window.showToast('danger', errors);
            });
        }

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
            axios.post("{{route('user.create')}}", fd, {})

                .then((response) => {
//                        console.log(response);
                        document.querySelector('#loading').classList.add('d-none');

                        if (response.status == 200) {
//                                if (response.data && response.data.res)
//                                    window.showToast('success', response.data.res);
//                                else
//                                    window.location.reload();
                            window.location = '{{url('panel/user/edit' )}}/' + response.data.id
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

