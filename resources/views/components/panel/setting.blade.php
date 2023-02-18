@php
    $user=auth()->user();
@endphp
{{--{{json_encode( $images)}}--}}
@if(!$user)
    <div class="text-center font-weight-bold text-danger mt-5">کاربر یافت نشد</div>
@else



    <div class="  my-3 ">
        <div class="row mx-auto justify-content-center">
            <div class="col-md-10 col-sm-12  ">
                <div class="card bg-light">
                    <h5 class="card-header text-center text-white bg-primary">
                        <div class=" input-group my-2">
                            <label for="addres-input"
                                   class="  ">تنظیمات کاربری</label>
                            {{--<button class="btn btn-danger rounded ms-auto font-weight-bold " type="button"--}}
                            {{--id="addres-addon"--}}
                            {{--onclick=" remove(event)">حذف--}}
                            {{--</button>--}}
                        </div>
                    </h5>

                    <div class="card-body  ">


                        <form id="form-create" method="POST" action=" "
                              class="text-right  row">
                            @csrf

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
                                           value="{{ $user->username }}" autocomplete="username">
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
                document.querySelector('#username').focus();
                addSMSBtnListener(
                    "{{auth()->user()->name}}",
                    "{{auth()->user()->family}}",
                    "{{auth()->user()->phone}}"
                );
            });


            function submitWithFiles(event, data) {
                document.querySelector('#loading').classList.remove('d-none');
                event.preventDefault();
                data = {'id': "{{$user->id}}", ...data};
                axios.post("{{route('user.edit')}}", data, {})

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