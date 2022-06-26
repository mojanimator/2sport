<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title> @yield('title',   config("app.name", "Laravel")   )</title>

    <!-- Scripts -->
{{--<script src="{{ mix('js/app.js').'?id='.rand(1000,9000) }}" defer></script>--}}


<!-- Fonts -->
    {{--<link rel="dns-prefetch" href="//fonts.gstatic.com">--}}
    {{--<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">--}}

    <style>
        @font-face {
            font-family: FontAwesome;
            src: url({{asset('fonts/fontawesome/fontawesome-webfont.eot')}});

            src: url({{asset('fonts/fontawesome/fontawesome-webfont.ttf')}}) format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: Tanha;
            src: url({{asset('fonts/Tanha/Tanha-FD.eot')}});
            src: url('{{asset('fonts/Tanha/Tanha-FD.eot')}}?#iefix') format('embedded-opentype'),
            url({{asset('fonts/Tanha/Tanha-FD.woff2')}}) format('woff2'),
            url({{asset('fonts/Tanha/Tanha-FD.woff')}}) format('woff'),
            url({{asset('fonts/Tanha/Tanha-FD.ttf')}}) format('truetype'),
            url('{{asset('fonts/Tanha/Tanha-FD.svg')}}#Tanha-FD') format('svg');

            font-weight: normal;
            font-style: normal;

            font-display: swap;
        }
    </style>
    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    @yield('styles')

</head>
<body>
<div id="app">

    @if (!Route::is('/') && !str_contains( url()->current(),'/panel') )


        <nav id="navbars00" class="navbar navbar-expand-sm navbar-dark bg-primary " aria-label="Eighth navbar example">
            <div class="container-fluid px-md-0">
                <a class="navbar-brand me-0 me-md-2 font-weight-bold" href="{{route('/')}}">
                    <img src="{{asset('img/icon.png')}}" height="64" alt="">
                    {{ config('app.name', '') }}
                </a>
                <button class="navbar-toggler  collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbar1" aria-controls="navbar1" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="navbar-collapse collapse" id="navbar1" style="">
                    <ul class="navbar-nav w-100    mb-lg-0">
                        <li class="nav-item small ms-1  my-1">
                            <a class="nav-link px-1 px-md-2  hoverable-cyan rounded {{str_contains( url()->current(),'/players')? 'text-primary bg-cyan':'text-white'}} "
                               aria-current="page" href="/players">بازیکن</a>
                        </li>
                        <li class="nav-item   small ms-1 ms-sm-0 ms-md-1 my-1">
                            <a class="nav-link px-1 px-md-2  hoverable-cyan rounded {{str_contains( url()->current(),'/coaches')? 'text-primary bg-cyan':'text-white'}} "
                               aria-current="page" href="/coaches">مربی</a>
                        </li>
                        <li class="nav-item  small ms-1 ms-sm-0 ms-md-1 my-1">
                            <a class="nav-link px-1 px-md-2  hoverable-cyan rounded {{str_contains( url()->current(),'/clubs')? 'text-primary bg-cyan':'text-white'}} "
                               aria-current="page" href="/clubs">مرکز ورزشی</a>
                        </li>
                        <li class="nav-item  small ms-1 ms-sm-0 ms-md-1 my-1">
                            <a class="nav-link px-1 px-md-2  hoverable-cyan rounded {{str_contains( url()->current(),'/shops')? 'text-primary bg-cyan':'text-white'}} "
                               aria-current="page" href="/shops">فروشگاه ورزشی</a>
                        </li>
                        <li class="nav-item  small ms-1 ms-sm-0 ms-md-1 my-1">
                            <a class="nav-link px-1 px-md-2  hoverable-cyan rounded {{str_contains( url()->current(),'/blogs')? 'text-primary bg-cyan':'text-white'}} "
                               aria-current="page" href="/blogs">خبر ورزشی</a>
                        </li>


                        @guest

                            <li class="nav-item   end-0 pe-1 align-self-center align-self-center my-1   ms-sm-auto ">
                                <div class="    ">
                                    <!-- Authentication Links -->

                                    <div class=" btn-group     ">
                                        <a class="btn  bg-secondary my-0 text-white px-sm-2 px-md-3  hoverable-dark "
                                           type="button"

                                           href=" {{ url('login') }} ">
                                            ورود
                                        </a>
                                        <a class="btn bg-blue my-0 text-white hoverable-dark px-sm-2 px-md-3 "
                                           type="button"

                                           href=" {{ url('register') }} ">
                                            ثبت نام
                                        </a>

                                    </div>
                                </div>
                            </li>
                        @endguest
                        @auth

                            <li class="dropdown position-absolute end-0  nav-item    align-self-center my-1 ms-sm-auto ">
                                <a href="#" class="dropdown-toggle nav-link  text-white hoverable-text-cyan"
                                   data-bs-toggle="dropdown"
                                   role="button" id="navbarDropdown"
                                   aria-haspopup="true" aria-expanded="false">

                                    {{auth()->user()->name?:auth()->user()->username}}
                                    {{--<i class="fa fa-user-circle " aria-hidden="true"></i>--}}

                                </a>
                                <ul class=" dropdown-menu end-0 bg-cyan    " aria-labelledby="navbarDropdown">
                                    <li class="list-group-item hoverable-primary hoverable-text-dark p-0">
                                        <a class="p-2 d-block text-primary" href="{{url('panel')}}">پنل
                                            کاربری</a>
                                    </li>

                                    <li role="separator" class="divider"></li>
                                    {{--<li class="dropdown-header">Nav header</li>--}}
                                    <li class="list-group-item hoverable-primary hoverable-text-dark p-0"
                                        onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                                    >
                                        <a class="d-block text-danger p-2" href="#">خروج</a>
                                    </li>

                                </ul>
                                <form id="logout-form" action="{{ route('logout') }}"
                                      method="POST"
                                      class="d-none">
                                    @csrf
                                </form>
                            </li>


                        @endauth
                    </ul>
                    {{--<form>--}}
                    {{--<input class="form-control" type="text" placeholder="Search" aria-label="Search">--}}
                    {{--</form>--}}
                </div>
            </div>
        </nav
        >
    @endif

    <main class="py-0  " style="min-height: 100vh !important ">


        @include('partials.alert', [ ])
        @yield('content')
    </main>
</div>

@if ( !str_contains( url()->current(),'/panel') )
    <footer class="footer pt-3 mt-1 bg-primary  ">
        {{--<hr class="horizontal dark mb-5">--}}
        <div class=" mx-3  ">
            <div class=" row text-center col-10 mx-auto">
                <div class="col-sm-4     ">
                    <div>
                        <h6 class="text-white text-primary font-weight-bolder">ارتباط با ما</h6>
                    </div>
                    <div class="  ">

                        <ul class="   nav    mx-auto  ">
                            <li class="nav-item ">
                                <a class="nav-link pe-1 text-white" href="https://www.instagram.com/varta.shop/"
                                   target="_blank"
                                   data-bs-toggle="tooltip" data-bs-placement="bottom" title="اینستاگرام">
                                    <i class="fab fa-instagram text-lg opacity-8 text-white"></i>
                                </a>
                            </li>
                            @php( $adminPhone = str_replace('09', '9', \Helper::$admin_phone))
                            <li class="nav-item ">
                                <a class="nav-link pe-1 text-white" href="https://wa.me/98{{$adminPhone}}"
                                   target="_blank"
                                   data-bs-toggle="tooltip" data-bs-placement="bottom" title="واتساپ">
                                    <i class="fab fa-whatsapp text-lg opacity-8 text-white"></i>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pe-1 text-white" href="https://t.me/develowper" target="_blank"
                                   data-bs-toggle="tooltip" data-bs-placement="bottom" title="تلگرام">
                                    <i class="fab fa-telegram text-lg opacity-8 text-white"></i>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link pe-1 text-white  "
                                   href="https://www.youtube.com/channel/UCzwQ6GnoNQG1PwpqZhkIogA"
                                   target="_blank" data-bs-toggle="tooltip" data-bs-placement="bottom" title="یوتیوب">
                                    <i class="fab fa-youtube text-lg opacity-8 text-white "></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-sm-4      ">
                    <div>
                        <h6 class="text-gradient text-white   font-weight-bolder">دسترسی سریع</h6>
                        <ul class="flex-row  nav  pr-0  small">
                            <li class="nav-item hoverable-text-teal">
                                <a class="nav-link text-white" href="{{route('players.view')}}" target="blank">
                                    بازیکن
                                </a>
                            </li>
                            <li class="nav-item hoverable-text-teal">
                                <a class="nav-link text-white" href="{{route('coaches.view')}}" target="blank">
                                    مربی
                                </a>
                            </li>
                            <li class="nav-item hoverable-text-teal">
                                <a class="nav-link text-white" href="{{route('clubs.view')}}" target="blank">
                                    مرکز ورزشی
                                </a>
                            </li>
                            <li class="nav-item hoverable-text-teal">
                                <a class="nav-link text-white" href="{{route('shops.view')}}" target="blank">
                                    فروشگاه ورزشی
                                </a>
                            </li>
                            <li class="nav-item hoverable-text-teal">
                                <a class="nav-link text-white" href="{{route('blogs.view')}}" target="blank">
                                    خبر ورزشی
                                </a>
                            </li>
                            <li class="nav-item hoverable-text-teal">
                                <a class="nav-link text-white" href="{{route('blogs.view',['view'=>'conductor'])}}"
                                   target="blank">
                                    کنداکتور
                                </a>
                            </li>

                        </ul>
                    </div>
                </div>
                <div class="col-sm-4  text-center ">
                    <a href="{{route('/')}}">
                        <img src="{{asset('img/icon.png')}}" alt=""
                             class="rounded-lg    move-on-hover w-100   " style="max-width: 200px">
                    </a>
                </div>
                <hr class="horizontal   ">
            </div>
            <a href="https://zil.ink/varta"
               class="start-0 hoverable-cyan text-light text-center bg-dark end-0 small bg-transparent position-absolute">
                <span class="">طراحی با </span>
                <span><i class="fa fa-heart text-danger" aria-hidden="true"></i></span>
                <span>توسط</span>
                <span> ورتا </span>
            </a>
        </div>
    </footer>
@endif
<script src="{{ mix('js/app.js')/*.'?id='.rand(1000,9000)*/}}" defer></script>
@yield('scripts')
</body>
</html>
