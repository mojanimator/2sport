@extends('layouts.app')
@section('title')
    اطلاعات فروشگاه
@stop
@section('content')

    @php
        $data=\App\Models\Shop::where('id',$id)->where('active',true)->with('docs')->with('province')->with('county')->first();

if ($data){

$province=$data->getRelation('province') ;
$county=$data->getRelation('county') ;
$docs=$data->getRelation('docs') ;

$img=$docs->where('type_id',Helper::$docsMap['logo'])->first() ;
if($img)
 $img= asset('storage')."/".Helper::$docsMap['logo']."/$img->id.jpg" ;


}
    @endphp

    @if ( !$data)
        <div class="text-center font-weight-bold mt-5 ">
            <div class="   text-danger ">فروشگاه یافت نشد</div>
            <a href="{{url('coaches')}}" class="list-item d-block hoverable-text-primary">بازگشت</a>
        </div>

    @else
        <div class="container  ">
            <div class="  mt-5 shadow-lg rounded-3 bg-white p-3">
                <div class="col-sm-10 mx-auto   bg-white p-3 my-3 position-relative overflow-hidden">
                    <div class="position-absolute w-100 h-100 top-0 start-0 end-0 bottom-0 opacity-30"
                         style="background: url('{{asset('img/texture.jpg')}}'); background-repeat:repeat;background-size: cover;z-index: 0">
                    </div>
                    <div class="row  " style="z-index: 2 ;">
                        <div class="col-sm-10 col-md-8 mx-auto ">


                            <div class="col-10 col-sm-8 col-md-6  mx-auto  my-2">
                                <div class=" position-relative w-100 h-100 card bg-light  ">
                                    <a href="{{$img}}" data-lity class="d-block  rounded-3 overflow-hidden">
                                        <div class="  position-absolute  img-overlay rounded-3 ">⌕</div>
                                        <img class="  w-100 h-100 "
                                             style="z-index: 0;  object-fit: contain"
                                             onError="this.onerror=null;this.src='{{asset('img/noimage.png')}}';this.parentElement.href='{{asset('img/noimage.png')}}'"
                                             src="{{$img}}" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class=" position-relative  my-2" style="z-index: 2 ">
                                <div class="col-12 rounded p-2 text-center  bg-primary text-white ">
                                    <i class="fa fa-shopping-cart "
                                       aria-hidden="true"></i>

                                    {{$data->name  }}
                                </div>
                                <div id="map"></div>
                                <div class="  row mx-0 my-1  ">
                                    <div class="col   rounded-start  bg-primary text-white small   px-2 py-1 ">  {{ 'تعداد محصول' }} </div>
                                    <div class=" col  rounded-end   bg-secondary text-white small  px-2 py-1 ">{{e2f(\App\Models\Product::where('shop_id',$data->id)->count())}}</div>
                                </div>
                                <div class="  row mx-0 my-1">
                                    <span class="col  rounded-start   bg-primary text-white small px-2 py-1 ">{{!empty($province) ?$province->name:'استان (نامشخص)'}}</span>
                                    <span class="col   rounded-end  bg-secondary text-white small  px-2 py-1 ">  {{!empty($county) ?$county->name:'شهر (نامشخص)'}} </span>
                                </div>

                                <div class="col-12 rounded p-2 text-center  bg-primary text-white my-1">
                                    <i class="fa fa-phone" aria-hidden="true"></i>
                                    {{$data->address}}
                                </div>
                                <div class="col-12 rounded p-2 text-center  bg-primary text-white my-1">
                                    <i class="fa fa-phone" aria-hidden="true"></i>
                                    {{$data->phone}}
                                </div>
                                <a href="{{route('products.view',['shop'=> $data->id ])}}"
                                   class="hoverable-indigo col-12 d-block rounded p-2 text-center  bg-secondary text-white my-1">
                                    <i class="fa fa-shopping-basket" aria-hidden="true"></i>
                                    {{'مشاهده محصولات'}}
                                </a>
                            </div>
                            <div class="   " style="z-index: 2 ">

                                @if($data->description)
                                    <div class=" col-sm-12  rounded   bg-light   my-3"
                                         style="z-index: 2 ">
                                        <span class="    text-primary small font-weight-bold  py-3 px-2 ">  {{ $data->description  }} </span>
                                    </div>
                                @endif
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

            leaflet('{{$data->location}}', '{{$data->name}}', 'view');
        });


    </script>
@stop