@extends('layouts.app')
@section('title')
    اطلاعات محصول
@stop

@section('content')

    @php
        $data=\App\Models\Product::where('id',$id)->where('active',true)->with('docs')->with('shop')->first();
if ($data){

$shop=$data->getRelation('shop') ;
$docs=$data->getRelation('docs') ;

$docs=$docs->where('type_id',Helper::$docsMap['product'])->all() ;
$images=[];
if($docs)
foreach ($docs as $doc) {
   $images[]=asset('storage')."/".Helper::$docsMap['product']."/$doc->id.jpg";
   }

}
    @endphp

    @if ( !$data || $shop->active==false)
        <div class="text-center font-weight-bold mt-5 ">
            <div class="   text-danger ">محصول یافت نشد</div>
            <a href="{{url('products')}}" class="list-item d-block hoverable-text-primary">بازگشت</a>
        </div>

    @else
        <div class="container  ">
            <div class="  mt-5 shadow-lg rounded-3 bg-white p-3">
                <div class=" row mx-auto   bg-white p-3 my-3 position-relative overflow-hidden">
                    <div class="position-absolute w-100 h-100 top-0 start-0 end-0 bottom-0 opacity-30"
                         style="background: url('{{asset('img/texture.jpg')}}'); background-repeat:repeat;background-size: cover;z-index: 0">
                    </div>

                    <div class="col-sm-10 mx-auto " style="z-index: 2 ;">
                        <div class="row mx-auto  col-md-10 col-lg-8" style="z-index: 2; ">
                            <div
                                    style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff"
                                    class="swiper mySwiper2"
                            >
                                <div class="swiper-wrapper">
                                    @foreach($images as $img)
                                        <a href="{{$img}}" class="swiper-slide rounded-2 " data-lity>
                                            <img src="{{$img}}" class="rounded-2 " style="object-fit: cover"
                                                 onError="this.onerror=null;this.src='{{asset('img/noimage.png')}}';this.parentElement.href='{{asset('img/noimage.png')}}'"/>
                                        </a>
                                    @endforeach
                                </div>
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                            </div>
                            <div thumbsSlider="" class="swiper mySwiper py-2 my-1 bg-primary rounded-2">
                                <div class="swiper-wrapper p-2">
                                    @foreach($images as $img)
                                        <div class="swiper-slide  bg-primary">
                                            <img src="{{$img}}" class="h-100 w-100 rounded move-on-hover"
                                                 onError="this.onerror=null;this.src='{{asset('img/noimage.png')}}';this.parentElement.href='{{asset('img/noimage.png')}}'"/>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="row mx-auto  col-md-10 col-lg-8 ">

                            <div class="col-md-12 row mx-auto  my-1" style="z-index: 2 ">
                                <div class="col-2 rounded-start p-2 text-center  bg-secondary text-white ">


                                    {{' کد '.$data->id  }}
                                </div>
                                <div class="col-10 rounded-end p-2 text-center  bg-primary text-white ">
                                    <i class="fa fa-archive  "
                                       aria-hidden="true"></i>

                                    {{ $data->name  }}
                                </div>

                                <div class="  col-12  ">
                                    <div class="col-sm-6 row    my-1  ">
                                        <span class=" rounded-start col  bg-primary text-white small px-2 py-1 ">{{!empty($data->province) ?$data->province->name:'قیمت'}}</span>
                                        <span class="{{$data->discount_price !=$data->price? 'text-decoration-line-through':''}}  col    rounded-end  bg-secondary text-white small  px-2 py-1 ">  {{ e2f(number_format($data->price)) }} </span>
                                    </div>
                                    @if ($data->discount_price !=$data->price)
                                        <div class="col-sm-6 row   my-1 ">
                                            <span class="  col  rounded-start   bg-primary text-white small px-2 py-1 ">{{!empty($data->province) ?$data->province->name:'قیمت حراج'}}</span>
                                            <span class="  col  rounded-end  bg-secondary text-white small  px-2 py-1 ">  {{ e2f(number_format($data->price)) }} </span>
                                        </div>
                                    @endif
                                </div>
                                @if ($shop)

                                    <a href="{{'/shop/'.$shop->id}}"
                                       class="col-12 d-block   hoverable-purple my-1 rounded p-2 text-center  bg-primary text-white ">
                                        <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                        {{$shop? $shop->name:''}}
                                    </a>
                                    <div class="col-12 my-1 rounded p-2 text-center  bg-primary text-white ">
                                        <i class="fa fa-phone" aria-hidden="true"></i>
                                        {{ $shop->phone }}
                                    </div>
                                @endif
                                <div class="col-12  " style="z-index: 2 ">

                                    @if($data->description)
                                        <div class="  row  rounded   bg-light   my-3 p-3"
                                             style="z-index: 2 ">
                                            <span class="    text-primary small font-weight-bold  py-3 px-2 ">  {{ $data->description  }} </span>
                                        </div>
                                    @endif
                                </div>

                            </div>
                            <div class="    mx-auto  mt-2   ">
                                <small>کلید واژه ها:</small>
                                @foreach(explode("#",$data->tags) as $tag)
                                    @continue($tag==null)

                                    <a href="{{ route('products.view',['name'=> str_replace('_',' ',$tag)]) }}"
                                       class=" mb-1 ms-1 rounded text-white bg-dark hoverable-purple px-2 d-inline-block">
                                        <small class="  ">
                                            {{$tag}}
                                        </small>
                                    </a>
                                @endforeach

                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    @endif
@stop

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function (event) {

            window.initClubGallery('.mySwiper', '.mySwiper2');

        });


    </script>
@stop

@section('styles')
    <style>


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