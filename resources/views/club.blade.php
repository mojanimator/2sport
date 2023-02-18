@extends('layouts.app')
@section('title')
    اطلاعات مرکز ورزشی
@stop

@section('content')

    @php
        $data=\App\Models\Club::where('id',$id)->where('active',true)->with('docs')->with('province')->with('county')->first();
if ($data ){

$docs=$data->getRelation('docs') ;
$docs=$docs->where('type_id',Helper::$docsMap['club'])->all() ;
$images=[];
if($docs)
foreach ($docs as $doc) {
   $images[]=asset('storage')."/".Helper::$docsMap['club']."/$doc->id.jpg";
   }

$sports=\App\Models\Sport::get();
   $days= [
                      7 => 'هر روز'  ,
                    0 => 'شنبه' ,
                     1 => 'یک شنبه' ,
                     2 => 'دو شنبه' ,
                    3 => 'سه شنبه' ,
                   4 => 'چهار شنبه' ,
                     5 => 'پنج  شنبه' ,
                      6 => 'جمعه' ,
                ];
 $genders= [
                     0 => 'آقایان' ,
                    1 => 'بانوان' ,
                ];
}
    @endphp

    @if ( !$data  )
        <div class="text-center font-weight-bold mt-5 ">
            <div class="   text-danger ">مرکز ورزشی یافت نشد</div>
            <a href="{{url('clubs')}}" class="list-item d-block hoverable-text-primary">بازگشت</a>
        </div>

    @else
        <div class="container  position-relative ">
            <div class="position-absolute w-100 h-100 top-0 start-0 end-0 bottom-0 opacity-30"
                 style="background-image: url('{{asset('img/texture.jpg')}}'); background-repeat:repeat;background-size: cover;z-index: 0">
            </div>
            <div class="  mt-5 shadow-lg rounded-3 bg-white p-3 ">
                <div class="col-sm-10 mx-auto  bg-white p-3 my-3 position-relative overflow-hidden">

                    <div class="" style="z-index: 2 ;">
                        <div class="row mx-auto  col-md-10 col-lg-8 ">
                            <div class="row mx-auto   " style="z-index: 2; ">
                                <div
                                        style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff"
                                        class="swiper mySwiper2"
                                >
                                    <div class="swiper-wrapper">
                                        @foreach($images as $img)
                                            <a href="{{$img}}" class="swiper-slide rounded-2 " data-lity>
                                                <img src="{{$img}}" class="rounded-2 "
                                                     onError="this.onerror=null;this.src='{{asset('img/noimage.png')}}';this.parentElement.href='{{asset('img/noimage.png')}}'"/>
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                </div>
                                <div thumbsSlider="" class="swiper mySwiper py-2 my-1 bg-primary rounded-2">
                                    <div class="swiper-wrapper  px-2 ">
                                        @foreach($images as $img)
                                            <div class="swiper-slide bg-primary ">
                                                <img src="{{$img}}" class="h-100 w-100 rounded  move-on-hover"
                                                     onError="this.onerror=null;this.src='{{asset('img/noimage.png')}}';this.parentElement.href='{{asset('img/noimage.png')}}'"/>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mx-auto my-2" style="z-index: 2 ">
                                <div class="col-12 rounded p-2 text-center  bg-primary text-white ">
                                    <i class="fa fa-star  "
                                       aria-hidden="true"></i>

                                    {{$data->name  }}
                                </div>
                                <div id="map"></div>
                                <div class="  row mx-0 my-1">
                                    <span class="col  rounded-start   bg-primary text-white small px-2 py-1 ">{{!empty($data->province) ?$data->province->name:'استان (نامشخص)'}}</span>
                                    <span class="col   rounded-end  bg-secondary text-white small  px-2 py-1 ">  {{!empty($data->county) ?$data->county->name:'شهر (نامشخص)'}} </span>
                                </div>
                                @if (!empty($data->times))

                                    <div class="accordion my-2" id="accordion1">
                                        <div class="accordion-item ">
                                            <h2 class="accordion-header " id="headingOne">
                                                <button class="accordion-button collapsed bg-primary text-white"
                                                        type="button" style=":after{color: white !important}"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapseOne" aria-expanded="false"
                                                        aria-controls="collapseOne">ساعات کاری

                                                </button>
                                            </h2>
                                            <div id="collapseOne" class="accordion-collapse collapse  "
                                                 aria-labelledby="headingOne" data-bs-parent="#accordion1">
                                                <div class="accordion-body">
                                                    @foreach(json_decode($data->times) as $d)
                                                        <div class="  row mx-auto my-1">
                                                            @php($s=$sports->where('id',$d->id)->first())
                                                            <span class="mb-1 col-6 col-sm-4 rounded   bg-blue text-white small px-2 py-1 ">{{$s->name}}</span>
                                                            <span class="col-sm-2"></span>
                                                            <span class="mb-1 col-6 col-sm-2 rounded-start    bg-primary text-white small px-2 py-1 ">{{$days[$d->d] }}</span>
                                                            <span class="mb-1 col-6 col-sm-2  rounded-end   bg-secondary text-white small px-2 py-1 ">{{$genders[$d->g] }}</span>
                                                            <span class="col-sm-2"></span>
                                                            <span class="mb-1 col-6 col-sm-2  rounded-start   bg-primary text-white small px-2 py-1 ">{{ 'از ساعت' }}</span>
                                                            <span class="mb-1 col-6 col-sm-2  rounded-end   bg-secondary text-white small px-2 py-1 ">{{( $d->fh<10? ('0'. $d->fh):$d->fh ).':'.($d->fm==0?'00':$d->fm ) }}</span>
                                                            <span class="col-sm-2"></span>
                                                            <span class="mb-1 col-6 col-sm-2  rounded-start   bg-primary text-white small px-2 py-1 ">{{ 'تا ساعت' }}</span>
                                                            <span class="mb-1 col-6 col-sm-2  rounded-end   bg-secondary text-white small px-2 py-1 ">{{( $d->th<10? ('0'. $d->th):$d->th ).':'.($d->tm==0?'00':$d->tm )  }}</span>
                                                            <span class="col-sm-2"></span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>


                                    </div>


                                @endif

                                <div class="col-12 rounded p-2 text-center  bg-primary text-white ">
                                    <i class="fa fa-phone" aria-hidden="true"></i>
                                    {{$data->phone}}
                                </div>
                            </div>

                        </div>


                        <div class="row  " style="z-index: 2 ">

                            @if($data->description)
                                <div class=" mx-auto  col-md-10 col-lg-8  rounded   bg-light   my-3 p-3"
                                     style="z-index: 2 ">
                                    <span class="    text-primary small font-weight-bold  py-3 px-2 ">  {{ $data->description  }} </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('scripts')
    @if (isset($data))

        <script>
            document.addEventListener("DOMContentLoaded", function (event) {

                window.initClubGallery('.mySwiper', '.mySwiper2');


                leaflet('{{$data->location}}', '{{$data->name}}', 'view');
            });


        </script>
    @endif
@stop

@section('styles')
    <style>
        .accordion-button::after {
            background-color: white !important;
            border-radius: 100%;
            font-weight: bold;
            margin-right: auto;
        }

        .swiper {
            width: 100%;
            height: 100%;
            z-index: 2;
        }

        .swiper-slide {
            text-align: center;
            font-size: 18px;
            background: #fff;

            /* Center slide text vertically */
            display: -webkit-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            align-items: center;
        }

        .swiper-slide img {
            display: block;
            width: 100%;
            height: 100% !important;
            object-fit: fill;
        }

        .swiper {
            width: 100%;

            margin-left: auto;
            margin-right: auto;
        }

        .swiper-slide {
            background-size: cover;
            background-position: center;
            height: 100% !important;
        }

        .mySwiper2 {

            height: 18rem;
            width: 100%;
            z-index: 2;
        }

        .mySwiper {
            height: 20%;
            box-sizing: border-box;
            padding: 10px 0;
        }

        .mySwiper .swiper-slide {
            width: 25%;
            height: 100%;
            opacity: 0.9;
        }

        .mySwiper .swiper-slide-thumb-active {
            opacity: 1;
        }

        .swiper-slide img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
@stop

