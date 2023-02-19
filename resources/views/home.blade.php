@extends('layouts.app')


@php
    if ($r=request()->ref ) {
    session(['ref'=>$r]) ;
    }
@endphp
@section('content')
    <header class="position-relative  bg-primary  ">

        <div class="position-absolute  w-100 h-100 opacity-30  "
             style="background-image: url({{asset('img/texture.jpg')}});  "></div>

        <nav class="navbar navbar-expand navbar-dark shadow-0  " aria-label="classjo navbar">
            <div class="container-fluid">
                <a class="navbar-brand font-weight-bold" href="{{route('/')}}">
                    <img src="{{asset('img/icon.png')}}" height="64" alt="">
                    {{ config('app.name', '') }}
                </a>
                <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbar1" aria-controls="navbar1" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="navbar-collapse container collapse" id="navbar1" style="">
                    <ul class="navbar-nav w-100  mb-2 mb-lg-0">
                        {{--<li class="nav-item  small ms-1 ms-sm-0 ms-md-1 my-1">--}}
                        {{--<a class="nav-link px-1 px-md-2  hoverable-cyan rounded {{str_contains( url()->current(),'/contact-us')? 'text-primary bg-cyan':'text-white'}} "--}}
                        {{--aria-current="page" href="/contact-us">تماس با ما</a>--}}
                        {{--</li>--}}
                        {{--<li class="nav-item  small ms-1 ms-sm-0 ms-md-1 my-1">--}}
                        {{--<a class="nav-link px-1 px-md-2  hoverable-cyan rounded {{str_contains( url()->current(),'/blogs')? 'text-primary bg-cyan':'text-white'}} "--}}
                        {{--aria-current="page" href="/blogs">اخبار ورزشی</a>--}}
                        {{--</li>--}}
                        @guest
                            <li class="nav-item align-self-center  me-2   ms-auto  ">
                                <div class="   ">
                                    <!-- Authentication Links -->

                                    <div class=" btn-group    ">
                                        <a class="btn  bg-secondary my-0 text-white   hoverable-dark "
                                           type="button"

                                           href=" {{ url('login') }} ">
                                            ورود
                                        </a>
                                        <a class="btn bg-blue my-0 text-white hoverable-dark " type="button"

                                           href=" {{ url('register') }} ">
                                            ثبت نام
                                        </a>

                                    </div>
                                </div>
                            </li>
                        @endguest
                        @auth

                            <li class="dropdown  nav-item    align-self-center ms-auto me-2 ">
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

        <div class=" position-relative  pb-4 text-center    ">
            <h1 class="text-white"> {{ config('app.name', '') }}</h1>
            <h2 class="text-white">فرصتی برای دیده شدن</h2>
        </div>
        <div class="      ">
            <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                 viewBox="0 24 150 40" preserveAspectRatio="none" shape-rendering="auto">
                <defs>
                    <path id="gentle-wave"
                          d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z"></path>
                </defs>
                <g class="moving-waves">
                    <use xlink:href="#gentle-wave" x="48" y="-1" fill="rgba(255,255,255,0.40"></use>
                    <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.35)"></use>
                    <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.25)"></use>
                    <use xlink:href="#gentle-wave" x="48" y="8" fill="rgba(255,255,255,0.20)"></use>
                    <use xlink:href="#gentle-wave" x="48" y="13" fill="rgba(255,255,255,0.15)"></use>
                    <use xlink:href="#gentle-wave" x="48" y="16" fill="rgba(255,255,255,0.95"></use>
                </g>
            </svg>
        </div>


    </header>
    <section class="  row mx-auto   justify-content-center " style="margin-top: -7rem;">
        <div class="col-10  ">
            <div class="card shadow-3   blur">

                <div class="card-body        ">

                    <div class="row  text-center align-content-center  text-primary ">
                        <a class="col-ms-6 col-sm-4 col-md-3 col-xl-2 move-on-hover   "
                           href="{{route('players.view')}}">
                            <x-icons src="player.svg" fill="#343265" style="max-width: 10rem;"
                                     class="'w-100  px-4     px-lg-5 text-white'">

                            </x-icons>

                            <h5 class="font-weight-bold">بازیکن</h5>

                        </a>

                        <a class="col-ms-6 col-sm-4 col-md-3 col-xl-2 move-on-hover " href="{{route('coaches.view')}}">
                            <x-icons src="coach.svg" fill="#343265" style="max-width: 10rem"
                                     class="'w-100  px-4    px-lg-5 text-white'">

                            </x-icons>
                            <h5 class="font-weight-bold">مربی</h5>
                        </a>

                        <a class="col-ms-6 col-sm-4 col-md-3 col-xl-2 move-on-hover  "
                           href="{{route('clubs.view')}}">
                            <x-icons src="club.svg" fill="#343265" style="max-width: 10rem"
                                     class="'w-100  px-4    px-lg-5 text-white'">

                            </x-icons>
                            <h5 class="font-weight-bold">مرکز ورزشی</h5>

                        </a>
                        <a class="col-ms-6 col-sm-4 col-md-3 col-xl-2 move-on-hover  "
                           href="{{route('shops.view')}}">
                            <x-icons src="shop.svg" fill="#343265" style="max-width: 10rem"
                                     class="'w-100  px-4    px-lg-5 text-white'">

                            </x-icons>
                            <h5 class="font-weight-bold">فروشگاه ورزشی</h5>
                        </a>
                        <div class="d-none d-md-block d-xl-none col-ms-6 col-sm-4 col-md-2 col-lg-1 col-xl-0"></div>
                        <a class="col-ms-6 col-sm-4 col-md-3 col-xl-2 move-on-hover " href="{{route('videos.view')}}">
                            <x-icons src="video.svg" fill="#343265" style="max-width: 10rem"
                                     class="'w-100  px-4   px-lg-5 text-white'">

                            </x-icons>
                            <h5 class="font-weight-bold">ویدیو</h5>
                        </a>
                        <a class="col-ms-6 col-sm-4 col-md-3 col-xl-2 move-on-hover "
                           href="{{route('blogs.view')}}">
                            <x-icons src="blog.svg" fill="#343265" style="max-width: 10rem"
                                     class="'w-100  px-4   px-lg-5 text-white'">

                            </x-icons>
                            <h5 class="font-weight-bold">خبر ورزشی</h5>
                        </a>
                        <a class="col-ms-6 col-sm-4 col-md-3 col-xl-2 move-on-hover  "
                           href="{{route('blogs.view',['view'=>'table-all'])}}">
                            <x-icons src="table.svg" fill="#343265" style="max-width: 10rem"
                                     class="'w-100  px-4    px-lg-5 text-white'">

                            </x-icons>
                            <h5 class="font-weight-bold">جدول لیگ ها</h5>
                        </a>


                    </div>


                </div>
            </div>
        </div>

    </section>

    <div class="container col-lg-10 ">
        {{--news and tables--}}
        <section class="row no-gutters mx-auto  px-4       mt-1 mt-md-3 ">


            <div class="col-md-6  px-1 mx-auto my-1">
                @php
                    $blogs=\App\Models\Blog::select('id','title','summary','updated_at')->with('docs')->latest()->take(5)->get();
                @endphp
                <div class="  bg-gradient-dark-transparent rounded-3 p-0 overflow-hidden  position-relative"
                     style="height: 18rem;">
                    <div
                        class=" bg-gradient-primary  p-2 overflow-hidden position-absolute rounded-bottom    text-white"
                        style="z-index: 2"
                    >
                        اخبار ورزشی
                    </div>
                    <div id="carouselBlogs" class=" carousel slide  h-100"
                         data-mdb-ride="carousel" data-mdb-interval="8000">

                        <div class="carousel-indicators">
                            @foreach($blogs as  $idx=>$blog)

                                <button type="button" data-mdb-target="#carouselBlogs" data-mdb-slide-to="{{$idx}}"
                                        class="{{$idx==0?  'active':''}}"
                                        aria-current="true" aria-label="{{$blog->title}}"></button>

                            @endforeach
                        </div>
                        <div class="carousel-inner  h-100 w-100 ">

                            @foreach($blogs as  $idx=>$blog)

                                <div class="z-index-1 carousel-item h-100 w-100 {{$idx==0?  'active':''}}">
                                    <small class="text-white position-absolute end-0 m-2 small"
                                           style="font-size: .7rem">{{Morilog\Jalali\Jalalian::forge($blog->updated_at, new DateTimeZone('Asia/Tehran'))->format('%A, %d %B %Y ⏰ H:i')}}</small>
                                    <img
                                        src="{{asset('storage').$blog->type_id.'/'. (($img=$blog->getRelation('docs')->first())?$img->type_id.'/'. $img->id:'').'.jpg'}}"
                                        class=" d-block mx-auto  h-100  "
                                        alt="{{$blog->title}}"
                                        style=" object-fit:contain ;object-position: 0 0;">
                                    <div
                                        class="carousel-caption p-0   start-0 end-0  top-50 bottom-0 start-0 end-0  d-md-block   "
                                    >
                                        <a type="button"
                                           class="btn move-on-hover bg-gradient-secondary  w-auto text-white "

                                           href="/blog/{{$blog->id}}/{{str_replace(' ','-',$blog->title)}}"><p
                                                class=" ">{{$blog->title}}</p>
                                        </a>

                                        <a type="button"
                                           class="btn small bg-gradient-dark-transparent mt-2 text-start  text-white w-100 "
                                           href="/blog/{{$blog->id}}/{{str_replace(' ','-',$blog->title)}}">
                                            <p>{{$blog->summary}}</p>
                                        </a>

                                    </div>


                                </div>
                            @endforeach

                        </div>
                        <button
                            class="carousel-control-prev"
                            type="button"
                            data-mdb-target="#carouselBlogs"
                            data-mdb-slide="prev"
                        >
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button
                            class="carousel-control-next"
                            type="button"
                            data-mdb-target="#carouselBlogs"
                            data-mdb-slide="next"
                        >
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
            @php
                $events=(new \App\Http\Controllers\EventController )->search(new  \Illuminate\Http\Request(['group'=>true,]));
                if( $events-> getData() ){
 $today=$events->getData()->today;
  $days=$events->getData()->days;

 $days= get_object_vars($days );
 }

            @endphp
            @if($days && is_array($days) && count($days)>0)
                <div class="col-md-6 px-1 my-1">
                    <div class=" bg-gradient-dark-transparent rounded-3    overflow-hidden  position-relative"
                         style="height: 18rem;">
                        <small class="text-white position-absolute end-0 m-2 small"
                               style="font-size: .7rem;z-index: 1"></small>


                        @php  $indx=0 @endphp
                        <div id="carouselConductor" class=" carousel slide   h-100  w-100  "
                             data-mdb-ride="carousel" data-mdb-interval="8000">
                            <div class="carousel-indicators">
                                @foreach($days as  $idx=>$day)

                                    <button type="button" data-mdb-target="#carouselConductor"
                                            data-mdb-slide-to="{{$indx++}}"
                                            class="{{$idx==$today?  'active':''}}"
                                            aria-current="true" aria-label="{{$indx}} "></button>

                                @endforeach
                            </div>
                            <div class="carousel-inner h-100 w-100     ">

                                @foreach($days as  $idx=>$eventGroups)

                                    <div
                                        class="  carousel-item  overflow-y-auto h-100 w-100 position-relative  {{$idx==$today?  'active':''}}">
                                        <div
                                            class=" bg-gradient-primary  p-2 overflow-hidden position-absolute rounded-bottom    text-white"
                                            style="z-index: 1"
                                        >{{' کنداکتور '.$idx  }}

                                        </div>
                                        <a href="{{url('blogs').'?view=conductor'}}">
                                            <div
                                                class="  carousel-caption   border-3   mt-3 h-100  overflow-y-auto    ">
                                                <div class="rounded-3 h-100   w-100 ">

                                                    @foreach($eventGroups as $title=>$events)

                                                        <div class="card my-1   w-100  ">
                                                            <div
                                                                class="text-white card-header bg-indigo p-1 ">{{$title}}</div>
                                                            @foreach($events as $idx=>$event)
                                                                @if($idx!=0)
                                                                    <hr class="border border-top border-info p-0 m-0">
                                                                @endif

                                                                <div class="text-primary d-flex flex-column ">
                                                                    <div
                                                                        class="text-indigo justify-content-around d-flex ">
                                                                        <div> {{$event->team1}}</div>
                                                                        <div
                                                                            class="d-flex justify-content-center font-weight-bold text-purple ">
                                                                            @if($event->score1 && $event->score2)
                                                                                <div> {{$event->score1}}</div>
                                                                                <div>:</div>
                                                                                <div> {{$event->score2}}</div>
                                                                                {{--@elseif($event->status==null)--}}
                                                                                {{--<div class="small"> {{Morilog\Jalali\Jalalian::forge($event->time, new DateTimeZone('Asia/Tehran'))->format('⏰ H:i')}}</div>--}}

                                                                            @endif
                                                                        </div>
                                                                        <div>{{$event->team2}}</div>
                                                                    </div>
                                                                    @if($event->status!=null)
                                                                        <div class=" smaller">{{$event->status}}</div>
                                                                    @else
                                                                        <div
                                                                            class="small"> {{Morilog\Jalali\Jalalian::forge($event->time, new DateTimeZone('Asia/Tehran'))->format('⏰ H:i')}}</div>

                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </a>

                                    </div>
                                @endforeach

                            </div>
                            <button
                                class="carousel-control-prev"
                                type="button"
                                data-mdb-target="#carouselConductor"
                                data-mdb-slide="prev"
                            >
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button
                                class="carousel-control-next"
                                type="button"
                                data-mdb-target="#carouselConductor"
                                data-mdb-slide="next"
                            >
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>

        </section>
        {{--carousel gallery--}}
        <section class="row px-5     mt-1 mt-md-3 ">
            @php
                $imgs=\App\Models\Doc::where('type_id',Helper::$docsMap['club'])->with('docable')->inRandomOrder()->take(5)->get();
            $clubs=[];
             foreach($imgs as  $idx=>$img){
             $d= $imgs[$idx]->getRelation('docable');
             if(!$d || !$img) continue;
             $clubs[]= $d ;
             $clubs[$idx]['doc']=  $img ;
             }
            $imgs=\App\Models\Doc::where('type_id',Helper::$docsMap['profile'])->where('docable_type',Helper::$typesMap['coaches'])->with('docable')->inRandomOrder()->take(5)->get();
                $coaches=[];
              foreach($imgs as  $idx=>$img){
                $d= $imgs[$idx]->getRelation('docable');

             if(!$d || !$img) continue;
             $coaches[]= $d ;
             $coaches[$idx]['doc']=  $img ;
             }
            $imgs=\App\Models\Doc::where('type_id',Helper::$docsMap['profile'])->where('docable_type',Helper::$typesMap['players'])->with('docable')->inRandomOrder()->take(5)->get();
                $players=[];
              foreach($imgs as  $idx=>$img){
                $d= $imgs[$idx]->getRelation('docable');
             if(!$d || !$img) continue;
             $players[]= $d ;
             $players[$idx]['doc']=  $img ;
             }


            @endphp

            <div class="col-md-6 bg-gradient-dark-transparent rounded-3  p-0  overflow-hidden" style="height: 18rem;">
                <div
                    class=" bg-gradient-primary  p-2 overflow-hidden position-absolute rounded-bottom rounded-start   text-white"
                    style="z-index: 2"
                >
                    مراکز ورزشی
                </div>
                <div id="carouselClubs" class=" carousel slide  h-100"
                     data-mdb-ride="carousel">

                    <div class="carousel-indicators">
                        @foreach($clubs as  $idx=>$club)
                            @continue(  is_array($clubs[$idx]))
                            <button type="button" data-mdb-target="#carouselClubs" data-mdb-slide-to="{{$idx}}"
                                    class="{{$idx==0?  'active':''}}"
                                    aria-current="true" aria-label="{{$clubs[$idx]->name}}"></button>

                        @endforeach
                    </div>
                    <div class="carousel-inner  h-100 w-100 ">

                        @foreach($clubs as  $idx=>$club)
                            @php($doc=$clubs[$idx]['doc'])
                            @continue(!$doc ||  is_array($clubs[$idx]))
                            @php($img=asset("storage/$doc->type_id/$doc->id.jpg") )
                            <div class="z-index-1 carousel-item h-100 w-100 {{$idx==0?  'active':''}}">
                                <img src="{{$img}}" class=" d-block  w-100 h-100  "
                                     alt="{{$clubs[$idx]->name}}"
                                     style=" object-fit:cover ;object-position: 0 0;">
                                <div class="carousel-caption   d-md-block  start-0 end-0">
                                    <h5 class="bg-gradient-dark-transparent text-white  w-100 py-3  ">{{$clubs[$idx]->name}}</h5>
                                    <div class="  left-0 right-0 bg-gradient-faded-dark w-100    rounded-lg p-4">
                                        {{--<p class="small">{{$docables[$idx]->name}}</p>--}}
                                    </div>
                                    <a type="button" class="btn move-on-hover bg-secondary w-auto text-white btn-lg"
                                       href="/club/{{$clubs[$idx]->id}}">مشاهده جزییات

                                    </a>
                                </div>


                            </div>
                        @endforeach

                    </div>
                    <button
                        class="carousel-control-prev"
                        type="button"
                        data-mdb-target="#carouselClubs"
                        data-mdb-slide="prev"
                    >
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button
                        class="carousel-control-next"
                        type="button"
                        data-mdb-target="#carouselClubs"
                        data-mdb-slide="next"
                    >
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>

            <div class="col-md-3 col-sm-6  py-sm-1 ps-md-2  px-sm-1 p-3 py-md-0 px-0  " style="height: 18rem;">
                <div class=" bg-primary rounded-3 overflow-hidden h-100 position-relative">
                    <div class=" bg-primary p-2   position-absolute rounded-bottom    text-white"
                         style="z-index: 2">
                        مربیان
                    </div>
                    <div id="carouselCoaches" class=" carousel slide carousel-fade   h-100"
                         data-mdb-ride="carousel">

                        <div class="carousel-indicators">
                            @foreach($coaches as  $idx=>$coach)
                                @if (!is_array($coach))
                                    <button type="button" data-mdb-target="#carouselCoaches"
                                            data-mdb-slide-to="{{$idx}}"
                                            class="{{$idx==0?  'active':''}}"
                                            aria-current="true" aria-label="{{$coaches[$idx]->name}}"></button>
                                @endif
                            @endforeach
                        </div>
                        <div class="carousel-inner  h-100 w-100 ">

                            @foreach($coaches as  $idx=>$coach)

                                @php($doc=$coaches[$idx]['doc'])
                                @continue(!$doc || is_array($coaches[$idx]))
                                @php($img=asset("storage/$doc->type_id/$doc->id.jpg") )
                                <div class="z-index-1 carousel-item h-100 w-100 {{$idx==0?  'active':''}}">
                                    <img src="{{$img}}" class=" d-block  w-100 h-100 rounded-3"
                                         alt="{{$coaches[$idx]->name}}"
                                         style=" object-fit:fill;object-position: 0 0;">
                                    <div class="carousel-caption   d-md-block start-0 end-0 ">
                                        <h5 class="bg-gradient-dark-transparent text-white  w-100 py-3 ">{{$coaches[$idx]->name.' '. $coaches[$idx]->family}}</h5>
                                        <div class="  left-0 right-0 bg-gradient-faded-dark w-100    rounded-lg p-4">
                                            {{--<p class="small">{{$docables[$idx]->name}}</p>--}}
                                        </div>
                                        <a type="button"
                                           class="btn move-on-hover bg-secondary w-auto text-white btn-lg"
                                           href="/coach/{{$coaches[$idx]->id}}">مشاهده جزییات

                                        </a>
                                    </div>


                                </div>

                            @endforeach

                        </div>
                        <button
                            class="carousel-control-prev"
                            type="button"
                            data-mdb-target="#carouselCoaches"
                            data-mdb-slide="prev"
                        >
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button
                            class="carousel-control-next"
                            type="button"
                            data-mdb-target="#carouselCoaches"
                            data-mdb-slide="next"
                        >
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6  py-sm-1 ps-md-2   px-sm-1 p-3 py-md-0 px-0  " style="height: 18rem;">
                <div class=" bg-primary rounded-3 h-100 position-relative overflow-hidden">
                    <div class=" bg-primary p-2  position-absolute rounded-bottom    text-white"
                         style="z-index: 2">
                        بازیکنان
                    </div>
                    <div id="carouselPlayers" class=" carousel slide carousel-fade   h-100"
                         data-mdb-ride="carousel">

                        <div class="carousel-indicators">
                            @foreach($players as  $idx=>$player)

                                @if (!is_array($player))

                                    <button type="button" data-mdb-target="#carouselPlayers"
                                            data-mdb-slide-to="{{$idx}}"
                                            class="{{$idx==0?  'active':''}}"
                                            aria-current="true" aria-label="{{$player->name}}"></button>
                                @endif
                            @endforeach
                        </div>
                        <div class="carousel-inner  h-100 w-100 ">

                            @foreach($players as  $idx=>$player)

                                @php($doc=$players[$idx]['doc'])
                                @continue(!$doc || is_array($players[$idx]))
                                @php($img=asset("storage/$doc->type_id/$doc->id.jpg") )
                                <div class="z-index-1 carousel-item h-100 w-100 {{$idx==0?  'active':''}}">
                                    <img src="{{$img}}" class=" d-block  w-100 h-100 rounded-3"
                                         alt="{{$players[$idx]->name}}"
                                         style="  object-position: 0 0;">
                                    <div class="carousel-caption   d-md-block start-0 end-0 ">
                                        <h5 class="bg-gradient-dark-transparent text-white  w-100 py-3 ">{{$players[$idx]->name.' '. $players[$idx]->family}}</h5>
                                        <div class="  left-0 right-0 bg-gradient-faded-dark w-100    rounded-lg p-4">
                                            {{--<p class="small">{{$docables[$idx]->name}}</p>--}}
                                        </div>
                                        <a type="button"
                                           class="btn move-on-hover bg-secondary w-auto text-white btn-lg"
                                           href="/player/{{$players[$idx]->id}}">مشاهده جزییات

                                        </a>
                                    </div>


                                </div>

                            @endforeach

                        </div>
                        <button
                            class="carousel-control-prev"
                            type="button"
                            data-mdb-target="#carouselPlayers"
                            data-mdb-slide="prev"
                        >
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button
                            class="carousel-control-next"
                            type="button"
                            data-mdb-target="#carouselPlayers"
                            data-mdb-slide="next"
                        >
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>


        <section class=" mx-4 my-3 py-4 pe-4 ps-5  row blur   rounded-3 position-relative  ">
            {{--<div class="d-md-inline-block d-sm-none d-none    align-self-center position-absolute right-0 top-50   ">--}}
            {{--<p class="vertical  text-white text-lg    ">جدید ترین ها</p>--}}
            {{--</div>--}}
            <div class="  col-md-4 py-3 ">
                <h4 class="  text-white    ">جدید ترین ها</h4>
            </div>
            <data-swiper class="    col-md-8  " data-link="{{route('latest')}}"
                         root-link="{{route('/')}}"
                         province-data="{{\App\Models\Province::with('counties')->get()}}"
                         img-link="{{asset("storage/")}}"
                         asset-link="{{asset("img/")}}"
                         width="13rem;"
            >


            </data-swiper>


        </section>
    </div>

@endsection

