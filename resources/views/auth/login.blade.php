@extends('layouts.app')
@section('content')
    <div class="container position-relative " style="height: 100vh;">
        <div class="position-absolute w-100 h-100 top-0 start-0 end-0 bottom-0 opacity-30"
             style="background: url('{{asset('img/texture.jpg')}}'); background-repeat:repeat;background-size: cover;z-index: 0">
        </div>
        <div class="row  justify-content-center">
            <div class="col-md-6 col-lg-4 col-sm-8 col-ms-10 mx-auto">
                <div class="card my-5 bg-light">
                    <div class="card-header text-right text-lg text-white font-weight-bold bg-primary">ورود</div>

                    <div class="card-body ">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group  ">
                                <label for="login"
                                       class="col-12 col-form-label text-md-right">{{__('نام کاربری/ایمیل/شماره تماس')}}</label>

                                <div class="col-12">
                                    <input id="login" type="text"
                                           class="px-4 form-control{{ $errors->has('username') || $errors->has('email') ? ' is-invalid' : '' }}"
                                           name="login"
                                           {{--class="form-control @error('email') is-invalid @enderror" name="email"--}}
                                           value="{{ old('username') ?: old('email') ?: old('phone') }}"
                                           {{--autocomplete="email"--}}
                                           autofocus>

                                    {{--@error('email')--}}
                                    @if ($errors->has('username') || $errors->has('email'))
                                        <span class="invalid-feedback text-right" role="alert">
                                        {{--<strong>{{ $message }}</strong>--}}
                                            <strong>{{ $errors->first('username') ?: $errors->first('email') }}</strong>
                                    </span>
                                        {{--@enderror--}}
                                    @endif
                                </div>
                            </div>

                            <div class="form-group  ">
                                <label for="password"
                                       class="col-12 col-form-label text-md-right"> رمز عبور/کد تایید </label>

                                <div class="col-12">
                                    <input id="password" type="password"
                                           class="px-4 form-control @error('password') is-invalid @enderror"
                                           name="password"
                                           autocomplete="current-password">

                                    @error('password')
                                    <span class="invalid-feedback text-right" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group  ">
                                <div class="col-12 mt-4 ">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember"
                                               id="remember" {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            به خاطر بسپار
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group   mb-0 row">
                                <div class="col-sm-6 my-1  ">
                                    <button type="submit" class="btn btn-primary btn-block  ">
                                        ورود
                                    </button>

                                </div>
                                <div class="col-sm-6  my-1 ">
                                    <button id="phone_verify-addon1"
                                            class="btn btn-secondary btn-block  px-1 px-sm-2">
                                        دریافت کد تایید
                                    </button>

                                </div>
                            </div>
                            @if (Route::has('password.request'))
                                <a class="btn btn-link mx-auto" href="{{ route('password.request') }}">
                                    رمز عبور خود را فراموش کرده ام
                                </a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>

        document.addEventListener("DOMContentLoaded", function (event) {

            addSMSBtnListener(
                "",
                "",
                ""
            );
        });
    </script>
@endsection