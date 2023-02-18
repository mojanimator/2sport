@extends('layouts.app')

@section('content')
    @php
        $primaryColor=  '#343265';
   $user= auth()->user();
   $param= !empty($param)?$param:'';

    @endphp
    <div class="container-fluid">
        <div class="row  bg-light  " id="body-row">
            <!-- Sidebar -->
            <div id="sidebar-container" class="      p-0   h-100 ">
                <div class="list-group bg-primary    text-start h-100 rounded-0  ">
                    <div class="list-group-item mb-1  bg-cyan   py-5 px-0 px-sm-1    text-primary font-weight-bold">
                        @if ($user->role=='ag' || $user->role=='aa')
                            <a class="text-center small text-primary my-1 d-block overflow-hidden"
                               href="/panel">نمایندگی
                                {{ \App\Models\Agency::findOrNew( $user->agency_id )->name }}</a>
                        @endif

                        <a class="text-center my-1 d-block overflow-hidden"
                           href="/panel">  {{$user->username?:$user->name}}</a>
                        <div
                            class="text-center bg-primary text-white rounded p-2 p-sm-0 mx-sm-0 font-weight-light"> {{'امتیاز: '.$user->score}}</div>
                    </div>

                    @if($user->role =='us'  )
                        <a href="#collapseEl" class="sidebar-item py-3 px-3 text-white hoverable-dark d-sm-block d-none"
                           data-bs-toggle="collapse"
                           aria-expanded="false" aria-controls="collapseEl"
                           data-bs-toggle="tooltip">
                            <div class="px-2  ">

                                <i class="fa fa-play-circle fa-2x  item-icon align-middle" aria-hidden="true"
                                   data-bs-placement="left"
                                   title="دسترسی سریع"></i>
                                <span class="item-text ps-1">دسترسی سریع</span>

                            </div>

                        </a>
                        <div class="collapse   text-light small  item-text  " id="collapseEl">
                            @foreach(\App\Models\Player::where('user_id',$user->id)->get(['id','name']) as $item)
                                <hr class="m-0  bg-dark">
                                <a class="d-block text-white py-2 ps-4 ms-3 hoverable-dark small"
                                   href="{{url("panel/player/edit",['param'=>$item->id] )}}">
                                    <x-icons src="player.svg"
                                             fill="#fff"
                                             style="max-width: 2rem; "
                                             class="px-0 m-0 d-inline-block">
                                    </x-icons>
                                    <span class="item-text d-inline ps-1">{{$item->name}}</span>
                                </a>
                            @endforeach
                            @foreach(\App\Models\Coach::where('user_id',$user->id)->get(['id','name']) as $item)
                                <hr class="m-0  bg-dark">
                                <a class="d-block text-white py-2 ps-4 ms-3 hoverable-dark small"
                                   href="{{url("panel/coach/edit",['param'=>$item->id] )}}">
                                    <x-icons src="coach.svg"
                                             fill="#fff"
                                             style="max-width: 2rem; "
                                             class="px-0 m-0 d-inline-block">
                                    </x-icons>
                                    <span class="item-text d-inline ps-1">{{$item->name}}</span>
                                </a>
                            @endforeach
                            @foreach(\App\Models\Club::where('user_id',$user->id)->get(['id','name']) as $item)
                                <hr class="m-0  bg-dark">
                                <a class="d-block text-white py-2 ps-4 ms-3 hoverable-dark small"
                                   href="{{url("panel/club/edit",['param'=>$item->id] )}}">
                                    <x-icons src="club.svg"
                                             fill="#fff"
                                             style="max-width: 2rem; "
                                             class="px-0 m-0 d-inline-block">
                                    </x-icons>
                                    <span class="item-text d-inline ps-1">{{$item->name}}</span>
                                </a>
                            @endforeach
                            @foreach(\App\Models\Shop::where('user_id',$user->id)->get(['id','name']) as $item)
                                <hr class="m-0  bg-dark">
                                <a class="d-block text-white py-2 ps-4 ms-3 hoverable-dark small"
                                   href="{{url("panel/shop/edit",['param'=>$item->id] )}}">
                                    <x-icons src="shop.svg"
                                             fill="#fff"
                                             style="max-width: 2rem; "
                                             class="px-0 m-0 d-inline-block">
                                    </x-icons>
                                    <span class="item-text d-inline ps-1">{{$item->name}}</span>
                                </a>
                            @endforeach
                        </div>
                        <hr class="m-0">
                    @endif

                    @can('createItem',[\App\Models\User::class,\App\Models\Blog::class,false])
                        <a href="/panel/blog"
                           class="sidebar-item py-2 px-0 px-sm-2 text-white hoverable-dark {{str_contains( url()->current(),'/blog') ?'bg-cyan text-primary':''}}"
                           data-bs-toggle="tooltip">
                            <div class=" align-content-end">
                          <span class=" d-inline-block  item-icon  " data-bs-placement="left" data-bs-toggle="tooltip"
                                title="خبر">
                              <x-icons src="blog.svg"
                                       fill="{{str_contains( url()->current(),'/blog') ?$primaryColor:'#fff'}}"
                                       style="max-width: 4rem"
                                       class="'w-100   px-2 pe-sm-2 text-white'">
                            </x-icons>
                          </span>

                                <span class="item-text">خبر</span>
                            </div>
                        </a>
                        <a href="/panel/table"
                           class="sidebar-item py-2 px-0 px-sm-2 text-white hoverable-dark {{str_contains( url()->current(),'/table') ?'bg-cyan text-primary':''}}"
                           data-bs-toggle="tooltip">
                            <div class=" align-content-end">
                          <span class=" d-inline-block  item-icon  " data-bs-placement="left" data-bs-toggle="tooltip"
                                title="جدول">
                              <x-icons src="table.svg"
                                       fill="{{str_contains( url()->current(),'/table') ?$primaryColor:'#fff'}}"
                                       style="max-width: 4rem"
                                       class="'w-100   px-2 pe-sm-2 text-white'">
                            </x-icons>
                          </span>

                                <span class="item-text">جدول</span>
                            </div>
                        </a>
                        <hr class="m-0">
                        <a href="/panel/events"
                           class="sidebar-item py-2 px-0 px-sm-2 text-white hoverable-dark {{str_contains( url()->current(),'/events') ?'bg-cyan text-primary':''}}"
                           data-bs-toggle="tooltip">
                            <div class=" align-content-end">
                          <span class=" d-inline-block  item-icon  " data-bs-placement="left" data-bs-toggle="tooltip"
                                title="رویداد">
                              <x-icons src="conductor.svg"
                                       fill="{{str_contains( url()->current(),'/events') ?$primaryColor:'#fff'}}"
                                       style="max-width: 4rem"
                                       class="'w-100   px-2 pe-sm-2 text-white'">
                            </x-icons>
                          </span>

                                <span class="item-text">رویداد</span>
                            </div>
                        </a>
                        <hr class="m-0">
                        <a href="/panel/videos"
                           class="sidebar-item py-2 px-0 px-sm-2 text-white hoverable-dark {{str_contains( url()->current(),'/videos') ?'bg-cyan text-primary':''}}"
                           data-bs-toggle="tooltip">
                            <div class=" align-content-end">
                          <span class=" d-inline-block  item-icon  " data-bs-placement="left" data-bs-toggle="tooltip"
                                title="ویدیو">
                              <x-icons src="video.svg"
                                       fill="{{str_contains( url()->current(),'/videos') ?$primaryColor:'#fff'}}"
                                       style="max-width: 4rem"
                                       class="'w-100   px-2 pe-sm-2 text-white'">
                            </x-icons>
                          </span>

                                <span class="item-text">ویدیو</span>
                            </div>
                        </a>
                    @endcan
                    @can('createItem',[\App\Models\User::class,\App\Models\Player::class,false])

                        <a href="/panel/player"
                           class="sidebar-item py-2 px-0 px-sm-2 text-white hoverable-dark {{str_contains( url()->current(),'/player') ?'bg-cyan text-primary':''}}"
                           data-bs-toggle="tooltip">
                            <div class=" align-content-end">
                          <span class=" d-inline-block  item-icon  " data-bs-placement="left" data-bs-toggle="tooltip"
                                title="بازیکن">
                              <x-icons src="player.svg"
                                       fill="{{str_contains( url()->current(),'/player') ?$primaryColor:'#fff'}}"
                                       style="max-width: 4rem"
                                       class="'w-100   px-2 pe-sm-2 text-white'">
                            </x-icons>
                          </span>

                                <span class="item-text">بازیکن</span>
                            </div>
                        </a>
                        <hr class="m-0">
                        <a href="/panel/coach"
                           class="sidebar-item py-2 px-0 px-sm-2 text-white hoverable-dark {{str_contains( url()->current(),'/coach') ?'bg-cyan text-primary':''}}"
                           data-bs-toggle="tooltip">
                            <div class=" align-content-end">
                          <span class=" d-inline-block  item-icon  " data-bs-placement="left" data-bs-toggle="tooltip"
                                title="مربی">
                              <x-icons src="coach.svg"
                                       fill="{{str_contains( url()->current(),'/coach') ?$primaryColor:'#fff'}}"
                                       style="max-width: 4rem"
                                       class="'w-100   px-2 pe-sm-2 text-white'">
                            </x-icons>
                          </span>

                                <span class="item-text">مربی</span>
                            </div>
                        </a>
                        <hr class="m-0">
                        <a href="/panel/club"
                           class="sidebar-item py-2 px-0 px-sm-2 text-white hoverable-dark {{str_contains( url()->current(),'/club') ?'bg-cyan text-primary':''}}"
                           data-bs-toggle="tooltip">
                            <div class=" align-content-end">
                          <span class=" d-inline-block  item-icon  " data-bs-placement="left" data-bs-toggle="tooltip"
                                title="مرکز ورزشی">
                              <x-icons src="club.svg"
                                       fill="{{str_contains( url()->current(),'/club') ?$primaryColor:'#fff'}}"
                                       style="max-width: 4rem"
                                       class="'w-100   px-2 pe-sm-2 text-white'">
                            </x-icons>
                          </span>

                                <span class="item-text">مرکز ورزشی</span>
                            </div>
                        </a>
                        <hr class="m-0">
                        <a href="/panel/shop"
                           class="sidebar-item py-2 px-0 px-sm-2 text-white hoverable-dark {{str_contains( url()->current(),'/shop') ?'bg-cyan text-primary':''}}"
                           data-bs-toggle="tooltip">
                            <div class=" align-content-end">
                          <span class=" d-inline-block  item-icon  " data-bs-placement="left" data-bs-toggle="tooltip"
                                title="فروشگاه ورزشی">
                              <x-icons src="shop.svg"
                                       fill="{{str_contains( url()->current(),'/shop') ?$primaryColor:'#fff'}}"
                                       style="max-width: 4rem"
                                       class="'w-100   px-2 pe-sm-2'">
                            </x-icons>
                          </span>

                                <span class="item-text">فروشگاه ورزشی</span>
                            </div>
                        </a>
                        <hr class="m-0">
                        <a href="/panel/product"
                           class="sidebar-item py-2 px-0 px-sm-2 text-white hoverable-dark {{str_contains( url()->current(),'/product') ?'bg-cyan text-primary':''}}"
                           data-bs-toggle="tooltip">
                            <div class=" align-content-end">
                          <span class=" d-inline-block  item-icon  " data-bs-placement="left" data-bs-toggle="tooltip"
                                title="محصولات">
                              <x-icons src="product.svg"
                                       fill="{{str_contains( url()->current(),'/product') ?$primaryColor:'#fff'}}"
                                       style="max-width: 4rem"
                                       class="'w-100   px-2 pe-sm-2'">
                            </x-icons>
                          </span>

                                <span class="item-text">محصول</span>
                            </div>
                        </a>
                    @endcan
                    @can('createItem',[\App\Models\User::class,\App\Models\User::class,false,(object)['role'=>'us']])

                        <hr class="m-0">
                        <a href="{{url('panel/users')}}"
                           class="sidebar-item py-2 px-0 px-sm-3 text-white hoverable-dark {{str_contains( url()->current(),'/users') ?'bg-cyan text-primary':''}}"
                           data-bs-toggle="tooltip">
                            <div class="">
                          <span class=" item-icon align-middle  " data-bs-placement="left"
                                data-bs-toggle="tooltip"
                                title="کاربران">
                             <i class="fa fa-users fa-2x  item-icon  " aria-hidden="true"
                             ></i>
                          </span>

                                <span class="item-text  ms-3">کاربران</span>
                            </div>
                        </a>
                    @endcan  @can('createItem',[\App\Models\User::class,\App\Models\Agency::class,false,(object)['role'=>'us']])

                        <hr class="m-0">
                        <a href="{{url('panel/agencies')}}"
                           class="sidebar-item py-2 px-0 px-sm-3 text-white hoverable-dark {{str_contains( url()->current(),'/agencies') ?'bg-cyan text-primary':''}}"
                           data-bs-toggle="tooltip">
                            <div class="">
                          <span class=" item-icon align-middle  " data-bs-placement="left"
                                data-bs-toggle="tooltip"
                                title="نمایندگی ها">
                             <i class="fa fa-house-user fa-2x  item-icon  " aria-hidden="true"
                             ></i>
                          </span>

                                <span class="item-text  ms-3">نمایندگی ها</span>
                            </div>
                        </a>
                    @endcan
                    <hr class="m-0">
                    <a href="{{url('panel/referral')}}"
                       class="sidebar-item py-2 px-0 px-sm-3 text-white hoverable-dark {{str_contains( url()->current(),'/referral') ?'bg-cyan text-primary':''}}"
                       data-bs-toggle="tooltip">
                        <div class="">
                          <span class=" item-icon align-middle ms-sm-2" data-bs-placement="left"
                                data-bs-toggle="tooltip"
                                title="بازاریابی">
                             <i class="fa fa-dollar-sign fa-2x  item-icon  " aria-hidden="true"
                             ></i>
                          </span>

                            <span class="item-text  ms-4">بازاریابی</span>
                        </div>
                    </a>

                    <hr class="m-0">
                    <a href="{{url('panel')}}"
                       class="sidebar-item py-2 px-0 px-sm-2 text-white hoverable-dark {{str_contains( url()->current(),'panel') && !str_contains( url()->current(),'panel/')   ?'bg-cyan text-primary':''}}"
                       data-bs-toggle="tooltip">
                        <div class="">
                          <span class=" item-icon align-middle ms-sm-2" data-bs-placement="left"
                                data-bs-toggle="tooltip"
                                title="تنظیمات">
                             <i class="fa fa-cog fa-2x  item-icon  " aria-hidden="true"
                             ></i>
                          </span>

                            <span class="item-text  ms-3">تنظیمات</span>
                        </div>
                    </a>

                </div>
            </div>


            <!-- MAIN -->
            <main class="col      p-0 position-relative  ">
                <nav id="navbars01" class="navbar    navbar-dark bg-primary   col   "
                     aria-label=" navbar ">

                    <div class="container  align-items-baseline">
                        <button class="navbar-toggler text-cyan  d-sm-none  "
                                data-bs-target="#navbars01" aria-controls="navbar1" aria-expanded="false"
                                aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon text-cyan  "></span>
                        </button>
                        <div class="navbar-header">
                            <a class="navbar-brand font-weight-bold" href="{{route('/')}}">
                                <img src="{{asset('img/icon.png')}}" height="32" alt="">
                                {{ config('app.name', '') }}
                            </a>
                        </div>
                        @auth
                            <ul class="nav   navbar-dark align-self-center">
                                {{--<li class="nav-item  ">--}}
                                {{--<a class="nav-link  hoverable-text-cyan text-white" aria-current="page" href="#">Home</a>--}}
                                {{--</li>--}}

                                <li class="dropdown nav-item   ">
                                    <a href="#" class="dropdown-toggle hoverable-text-cyan text-white"
                                       data-bs-toggle="dropdown"
                                       role="button"
                                       aria-haspopup="true" aria-expanded="true">
                                        <i class="fa fa-user-circle" aria-hidden="true"></i>
                                        {{$user->name?:$user->username}}

                                        <span
                                            class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu end-0 bg-cyan    ">
                                        <li class="list-group-item hoverable-primary hoverable-text-dark p-0">
                                            <a class="d-block text-primary p-2"
                                               href="{{url('panel')}}">پنل</a>
                                        </li>
                                        <li class="list-group-item hoverable-primary hoverable-text-dark p-0">
                                            <a class="d-block text-primary p-2"
                                               href="{{url('panel/setting')}}">تنظیمات</a>
                                        </li>

                                        <li role="separator" class="divider"></li>
                                        {{--<li class="dropdown-header">Nav header</li>--}}
                                        <li class="list-group-item hoverable-primary hoverable-text-dark p-0"
                                            onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                                        >
                                            <a class="d-block text-danger p-2" href="">خروج</a>
                                        </li>

                                    </ul>
                                    <form id="logout-form" action="{{ route('logout') }}"
                                          method="POST"
                                          class="d-none">
                                        @csrf
                                    </form>
                                </li>

                            </ul>
                        @endauth
                        {{--<form>--}}
                        {{--<input class="form-control" type="text" placeholder="Search" aria-label="Search">--}}
                        {{--</form>--}}


                    </div>
                </nav>


                {{--loading--}}
                <div class="    m-2  position-absolute w-100 " style="z-index: 10;">
                    <div id="loading" class="spinner-border position-fixed  text-danger d-none
                 bottom-0  "
                         role="status">
                        <span class="sr-only"></span>
                    </div>
                    <span id="percent"
                          class=" small font-weight-bold position-fixed bottom-0 px-2 py-1   text-danger">  </span>
                </div>

                <div class="container-fluid    px-0 px-sm-2   my-2">


                    @if(str_contains( url()->current(),'/table/create'))
                        <x-panel.table-create>
                        </x-panel.table-create>
                    @elseif(str_contains( url()->current(),'/table/edit'))
                        <x-panel.table-edit param="{{$param}}">

                        </x-panel.table-edit>
                    @elseif(str_contains( url()->current(),'/table'))
                        <x-panel.tables>

                        </x-panel.tables>
                    @elseif(str_contains( url()->current(),'/blog/create'))
                        <x-panel.blog-create>

                        </x-panel.blog-create>
                    @elseif(str_contains( url()->current(),'/blog/edit'))
                        <x-panel.blog-edit param="{{$param}}">

                        </x-panel.blog-edit>
                    @elseif(str_contains( url()->current(),'/blog'))
                        <x-panel.blogs>

                        </x-panel.blogs>
                    @elseif(str_contains( url()->current(),'/player/create'))
                        <x-panel.player-create>

                        </x-panel.player-create>
                    @elseif(str_contains( url()->current(),'/player/edit'))
                        <x-panel.player-edit param="{{$param}}">

                        </x-panel.player-edit>
                    @elseif(str_contains( url()->current(),'/player'))
                        <x-panel.players>

                        </x-panel.players>
                    @elseif(str_contains( url()->current(),'/coach/create'))
                        <x-panel.coach-create>

                        </x-panel.coach-create>
                    @elseif(str_contains( url()->current(),'/coach/edit'))
                        <x-panel.coach-edit param="{{$param}}">

                        </x-panel.coach-edit>
                    @elseif(str_contains( url()->current(),'/coach'))
                        <x-panel.coaches>

                        </x-panel.coaches>

                    @elseif(str_contains( url()->current(),'/club/create'))
                        <x-panel.club-create>

                        </x-panel.club-create>
                    @elseif(str_contains( url()->current(),'/club/edit'))
                        <x-panel.club-edit param="{{$param}}">

                        </x-panel.club-edit>
                    @elseif(str_contains( url()->current(),'/club'))
                        <x-panel.clubs>

                        </x-panel.clubs>
                    @elseif(str_contains( url()->current(),'/shop/create'))
                        <x-panel.shop-create>

                        </x-panel.shop-create>
                    @elseif(str_contains( url()->current(),'/shop/edit'))
                        <x-panel.shop-edit param="{{$param}}">

                        </x-panel.shop-edit>
                    @elseif(str_contains( url()->current(),'/shop'))
                        <x-panel.shops>

                        </x-panel.shops>

                    @elseif(str_contains( url()->current(),'/product/create'))
                        <x-panel.product-create>

                        </x-panel.product-create>
                    @elseif(str_contains( url()->current(),'/product/edit'))
                        <x-panel.product-edit param="{{$param}}">

                        </x-panel.product-edit>
                    @elseif(str_contains( url()->current(),'/product'))
                        <x-panel.products>

                        </x-panel.products>

                    @elseif(str_contains( url()->current(),'/setting'))
                        <x-panel.setting>

                        </x-panel.setting>
                    @elseif(str_contains( url()->current(),'/referral'))
                        <x-panel.referral>

                        </x-panel.referral>
                    @elseif(str_contains( url()->current(),'/system-setting'))
                        <x-panel.system-setting>

                        </x-panel.system-setting>
                    @elseif(str_contains( url()->current(),'/system-logs'))
                        <x-panel.system-logs>

                        </x-panel.system-logs>
                    @elseif(str_contains( url()->current(),'/coupons'))
                        <x-panel.coupons>

                        </x-panel.coupons>
                    @elseif(str_contains( url()->current(),'/users'))
                        <x-panel.users>

                        </x-panel.users>
                    @elseif(str_contains( url()->current(),'/user/create'))
                        <x-panel.user-create>

                        </x-panel.user-create>
                    @elseif(str_contains( url()->current(),'/user/edit'))
                        <x-panel.user-edit param="{{$param}}">

                        </x-panel.user-edit>

                    @elseif(str_contains( url()->current(),'/event/create'))
                        <x-panel.event-create>

                        </x-panel.event-create>
                    @elseif(str_contains( url()->current(),'/event/edit'))
                        <x-panel.event-edit param="{{$param}}">

                        </x-panel.event-edit>
                    @elseif(str_contains( url()->current(),'/events'))
                        <x-panel.events>

                        </x-panel.events>

                    @elseif(str_contains( url()->current(),'/agencies'))
                        <x-panel.agencies>

                        </x-panel.agencies>
                    @elseif(str_contains( url()->current(),'/agency/create'))
                        <x-panel.agency-create>

                        </x-panel.agency-create>
                    @elseif(str_contains( url()->current(),'/agency/edit'))
                        <x-panel.agency-edit param="{{$param}}">

                        </x-panel.agency-edit>

                    @elseif(str_contains( url()->current(),'/video/create'))
                        <x-panel.video-create>

                        </x-panel.video-create>
                    @elseif(str_contains( url()->current(),'/video/edit'))
                        <x-panel.video-edit param="{{$param}}">

                        </x-panel.video-edit>
                    @elseif(str_contains( url()->current(),'/videos'))
                        <x-panel.videos>

                        </x-panel.videos>
                    @else
                        <x-panel.main>

                        </x-panel.main>
                    @endif
                </div>
            </main><!-- Main Col END -->
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function (event) {
            let toggle = document.querySelector(".navbar-toggler");
            let sidebar = document.querySelector("#sidebar-container");

            toggle.onclick = function () {
                sidebar.classList.toggle("sidebar-expanded");

            };

//use my info in panel create

            // add image cropper to
        });
    </script>

@stop
@stack('scripts')
