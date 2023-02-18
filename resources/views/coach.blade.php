@extends('layouts.app')
@section('title')
    اطلاعات مربی
@stop
@section('content')

    @php
        $data=\App\Models\Coach::where('id',$id)->where('active',true)->with('docs')->with('sport')->with('province')->with('county')->first();
if ($data){

$docs=$data->getRelation('docs') ;
$profile=$docs->where('type_id',Helper::$docsMap['profile'])->first() ;

$tmp=Morilog\Jalali\Jalalian::fromDateTime($data->born_at);

}
    @endphp

    @if ( !$data  )
        <div class="text-center font-weight-bold mt-5 ">
            <div class="   text-danger ">مربی یافت نشد</div>
            <a href="{{url('coaches')}}" class="list-item d-block hoverable-text-primary">بازگشت</a>
        </div>

    @else
        <div class="container  ">
            <div class="  mt-5 shadow-lg rounded-3 bg-white p-3">
                <div class="col-sm-10 mx-auto   bg-white p-3 my-3 position-relative overflow-hidden">
                    <div class="position-absolute w-100 h-100 top-0 start-0 end-0 bottom-0 opacity-30"
                         style="background: url('{{asset('img/texture.jpg')}}'); background-repeat:repeat;background-size: cover;z-index: 0">
                    </div>
                    <div class="" style="z-index: 2 ;">
                        <div class="row  ">


                            <div class="col-md-6 my-2" style=" max-height: 16rem">
                                <div class=" position-relative w-100 h-100 card bg-light  ">
                                    @php($img=!empty($profile) ? asset('storage')."/".Helper::$docsMap['profile']."/$profile->id.jpg":asset('img/noimage.png'))
                                    <a href="{{$img}}" data-lity class="d-block  rounded-3 overflow-hidden">
                                        <div class="  position-absolute  img-overlay rounded-3 ">⌕</div>
                                        <img class="  w-100 h-100 "
                                             style="z-index: 0;  object-fit: contain"
                                             onError="this.onerror=null;this.src='{{asset('img/noimage.png')}}';this.parentElement.href='{{asset('img/noimage.png')}}'"
                                             src="{{$img}}" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6 my-2" style="z-index: 2 ">
                                <div class="col-12 rounded p-2 text-center  bg-primary text-white ">
                                    <i class="fa {{$data->is_man? 'fa-male':'fa-female'}}   "
                                       aria-hidden="true"></i>

                                    {{$data->name .' ' .$data->family}}
                                </div>
                                <div class="  row mx-0 my-1  ">
                                    <div class="col   rounded-start  bg-primary text-white small   px-2 py-1 ">  {{ 'رشته ورزش' }} </div>
                                    <div class=" col  rounded-end   bg-secondary text-white small  px-2 py-1 ">{{!empty($data->sport) ?$data->sport->name:'  (نامشخص)'}}</div>
                                </div>
                                <div class="  row mx-0 my-1">
                                    <span class="col  rounded-start   bg-primary text-white small px-2 py-1 ">{{!empty($data->province) ?$data->province->name:'استان (نامشخص)'}}</span>
                                    <span class="col   rounded-end  bg-secondary text-white small  px-2 py-1 ">  {{!empty($data->county) ?$data->county->name:'شهر (نامشخص)'}} </span>
                                </div>
                                <div class="  row mx-0 my-1">
                                    <span class="col  rounded-start   bg-primary text-white small px-2 py-1">{{ 'سن' }}</span>
                                    <span class="col   rounded-end  bg-secondary text-white small   px-2 py-1 ">  {{ Carbon\Carbon::parse($data->born_at )->age ?: 'نامشخص'}} </span>
                                </div>

                                <div class="col-12 rounded p-2 text-center  bg-primary text-white ">
                                    <i class="fa fa-phone" aria-hidden="true"></i>
                                    {{$data->phone}}
                                </div>
                            </div>

                        </div>

                        <div class="row  " style="z-index: 2 ">

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
    @endif
@stop

@section('scripts')

@stop