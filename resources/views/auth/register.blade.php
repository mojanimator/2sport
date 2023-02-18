@php

    if(auth()->user()){
    header("Location: " . URL::to('/'), true, 302);
            exit();
    }
@endphp

@extends('layouts.app')

@section('content')
    <div class="container my-3 ">
        <div class="row w-100 mx-auto justify-content-center">
            {{--register options--}}
            <div class="col-md-8  ">
                <div class="btn-group w-100 my-2 mb-3">
                    <a href="/register"
                       class="btn px-1 {{  url()->current()==url('/register') ? 'btn-primary':'btn-outline-primary' }}">کاربر</a>
                    <a href="/register-player"
                       class="btn px-1 {{ url()->current()==url('/register-player') ? 'btn-primary':'btn-outline-primary' }}">بازیکن</a>
                    <a href="/register-coach"
                       class="btn px-1 {{ url()->current()==url('/register-coach') ? 'btn-primary':'btn-outline-primary' }}">مربی</a>
                    <a href="/register-club"
                       class="btn px-1 {{ url()->current()==url('/register-club') ? 'btn-primary':'btn-outline-primary' }}">مرکز
                        ورزشی</a>
                    <a href="/register-shop"
                       class="btn px-1 {{ url()->current()==url('/register-shop') ? 'btn-primary':'btn-outline-primary' }}">فروشگاه
                        ورزشی</a>
                </div>
            </div>

            <div class="col-md-8 col-sm-10 mx-auto  ">
                <div class="card bg-light">
                    <h5 class="card-header text-center text-white bg-primary">ثبت نام کاربر</h5>

                    <div class="card-body  ">
                        <form method="POST" action="{{ route('register') }}" class="text-right  row">
                            @csrf
                            <div class="col-md-10 mx-auto   d-none">
                                <div class="m-2 form-outline">

                                    <input id="name" type="text"
                                           class="my-3 px-4 form-control @error('name') is-invalid @enderror"
                                           name="name"
                                           value="{{ old('name') }}" autocomplete="name" autofocus>
                                    <label for="name"
                                           class="col-md-12    form-label  text-md-right">نام</label>

                                    @error('name')
                                    <div class="invalid-feedback   " role="alert">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                    @enderror

                                </div>
                            </div>

                            <div class="col-md-10 mx-auto   d-none ">
                                <div class="m-2 form-outline">
                                    <input id="family" type="text"
                                           class="my-3 px-4 form-control @error('family') is-invalid @enderror"
                                           name="family"
                                           value="{{ old('family') }}" autocomplete="family" autofocus>
                                    <label for="family"
                                           class="col-md-12 col-form-label form-label  text-md-right">
                                        نام خانوادگی
                                    </label>

                                    @error('family')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror

                                </div>
                            </div>

                            <div class="col-md-10 mx-auto   ">
                                <div class="m-2 form-outline">

                                    <input id="username" type="text"
                                           class="my-3 px-4 form-control @error('username') is-invalid @enderror"
                                           value="{{ old('username') }}"
                                           name="username"
                                           autocomplete="username">
                                    <label for="username"
                                           class="col-md-12  form-label text-md-right">نام کاربری</label>

                                    @error('username')
                                    <span class="invalid-feedback small" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror

                                </div>
                            </div>

                            <div class="col-md-10 mx-auto   d-none">
                                <div class="m-2 form-outline">


                                    <input id="email" type="email"
                                           class="my-3 px-4 form-control @error('email') is-invalid @enderror"
                                           name="email"
                                           value="{{ old('email') }}" autocomplete="email">
                                    <label for="email"
                                           class="col-md-12  form-label text-md-right">ایمیل</label>
                                    @error('email')
                                    <span class="invalid-feedback small" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-10 mx-auto   ">
                                <div class="m-2 my-3 form-outline input-group">
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

                                    @error('phone')
                                    <div class="invalid-feedback  small" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class=" col-md-10 mx-auto   ">
                                <div class=" form-outline m-2   ">

                                    <input id="phone_verify" type="text"
                                           class="my-3 px-4 form-control @error('phone_verify') is-invalid @enderror"
                                           name="phone_verify">
                                    <label for="phone_verify"
                                           class="col-md-12  form-label text-md-right">کد تایید</label>
                                    @error('phone_verify')
                                    <div class="invalid-feedback  small" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                    @enderror
                                </div>

                            </div>


                            <div class="col-md-10 mx-auto  d-none ">
                                <div class="m-2 form-outline  ">

                                    <input id="password" type="password"
                                           class="my-3 px-4 form-control @error('password') is-invalid @enderror"
                                           name="password"

                                           autocomplete="new-password">
                                    <label for="password"
                                           class="col-md-12  form-label text-md-right">رمز ورود</label>
                                    @error('password')
                                    <div class="invalid-feedback  small" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                    @enderror

                                </div>
                            </div>
                            <div class="col-md-10 mx-auto   d-none">
                                <div class="m-2 form-outline  ">

                                    <input id="password-confirm" type="password"
                                           class="my-3 px-4 form-control @error('password') is-invalid @enderror"
                                           name="password_confirmation"

                                           autocomplete="new-password">
                                    <label for="password"
                                           class="col-md-12  form-label text-md-right">تایید رمز ورود</label>
                                    @error('password')
                                    <div class="invalid-feedback  small" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </div>
                                    @enderror

                                </div>
                            </div>


                            <div class="form-group   mb-0">
                                <div class="col-md-12  mt-2">
                                    <button type="submit" class="btn btn-success btn-block font-weight-bold py-3">
                                        ثبت نام
                                    </button>
                                </div>
                            </div>

                            {{--<div class="form-group row">--}}
                            {{--<label class="col-md-3 col-form-label text-md-right"> </label>--}}
                            {{--<div class="col-md-6">--}}
                            {{--{!! htmlFormSnippet--}}
                            {{--([--}}
                            {{--"theme" => "light",--}}
                            {{--"size" => "normal",--}}
                            {{--"tabindex" => "3",--}}
                            {{--])--}}
                            {{--!!}--}}
                            {{--</div>--}}
                            {{--@error('g-recaptcha-response')--}}
                            {{--<span class="  text-center text-danger @error('g-recaptcha-response') is-invalid @enderror "--}}
                            {{--role="alert">--}}
                            {{--<strong>{{ $message }}</strong>--}}
                            {{--</span>--}}
                            {{--@enderror--}}
                            {{--</div>--}}


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


            addSMSBtnListener(
                "",
                "",
                ""
            );
        });
    </script>
@endsection