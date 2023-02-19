@php
    $data=\App\Models\Video::where('id',$id)->where('active',true)->first();
    $timezone=new DateTimeZone('Asia/Tehran');
if ($data){

$video=asset('storage')."/".Helper::$docsMap['videos']."/$data->id.mp4";
$poster=asset('storage')."/".Helper::$docsMap['videos']."/$data->id.jpg";

//related videos
  $search=explode(" ", $data->tags )+ explode(" ", $data->title ) ;
   $query  =\App\Models\Video::query()->whereNotIn('id',[$data->id]);
  foreach ($search as $word) {
       $queryNewest = $query->where(function ($query) use ($word) {
           $query->orWhere('title', 'LIKE', '%' . $word . '%')
               ->orWhere('tags', 'LIKE', '%' . $word . '%');
       });
    }
  $relatedVideos=$query->orderByDesc('id' )->take(2)->get() ;

  //new videos
     $newVideos=\App\Models\Video::query()->whereNotIn('id',[$data->id])->orderByDesc('id' )->take(8)->get();

     function getDuration($time){
         if(!$time) return '';
         $min=intdiv($time ,60);
         $sec= $time   %60;
         return str_pad($min, 2, "0", STR_PAD_LEFT) .":".str_pad($sec, 2, "0", STR_PAD_LEFT);

     }

}
@endphp

@extends('layouts.app')
@section('title')
    {{$data?$data->title:'ویدیو یافت نشد'}}
@stop
@section('content')

    @if ( !$data)
        <div class="text-center font-weight-bold mt-5 ">
            <div class="   text-danger ">ویدیو یافت نشد</div>
            <a href="{{url('videos')}}" class="list-item d-block hoverable-text-primary">بازگشت</a>
        </div>

    @else
        <div class="container   ">
            <div class=" my-2   shadow-lg rounded-3 bg-white p-5 position-relative">
                <div class="position-absolute w-100 h-100 top-0 start-0 end-0 bottom-0  opacity-10"
                     style="background: url('{{asset('img/texture.jpg')}}'); background-repeat:repeat;background-size: cover;z-index: 0">
                </div>

                <div class="row position-relative ">

                    <div class="col-md-8 my-3">
                        @if($video)
                            <div class="  w-100 card bg-light   ">
                                <video style=" "
                                       id="my-video"
                                       class="video-js w-100    rounded-top overflow-hidden"
                                       controls
                                       preload="auto"
                                       poster="{{$poster}}"

                                >
                                    <!--<source :src="preload" type="video/mp4"/>-->
                                    <!--<source src="MY_VIDEO.webm" type="video/webm"/>-->
                                    <p class="vjs-no-js">
                                        مرورگر قادر به اجرای این ویدیو نیست
                                        <!--<a href="https://videojs.com/html5-video-support/" target="_blank"-->
                                        <!--&gt;بیشتر</a>-->


                                    </p>
                                </video>
                            </div>

                        @endif
                        <div class="">
                            <div class="  mb-2  " style="  ">
                                <div
                                    class="col-12 p-2 rounded d-flex  justify-content-around  small  bg-light rounded-bottom text-dark ">
                                    <div>
                                        @php($ago= Morilog\Jalali\Jalalian::forge ($data->created_at,$timezone)->ago())
                                        {{ (str_contains($ago,'ماه')|| str_contains($ago,'سال'))?Morilog\Jalali\Jalalian::forge($data->created_at,$timezone)->format('%A, %d %B %Y ⏰ H:i'):$ago}}
                                    </div>
                                    <div>
                                        {{getDuration($data->duration)  }}
                                    </div>
                                </div>
                                <div class="card-divider opacity-50"></div>
                                <div class="col-12 rounded font-weight-bold  text-start text-gradient-indigo ">
                                    {{$data->title  }}
                                </div>
                                <div class="row  " style="z-index: 2 ">

                                    @if($data->description)
                                        <div class=" col mx-auto rounded   bg-light   py-3  my-3"
                                             style="z-index: 2 ">
                                    <span
                                        class="    text-primary small font-weight-bold  py-3 px-2 ">    {!!  str_replace(PHP_EOL,"<br/>",$data->description) !!}   </span>
                                        </div>
                                    @endif
                                </div>

                            </div>

                            <div class="    mt-4 opacity-75   small">

                                @foreach(explode(" ", $data->tags ) as $tag)
                                    @continue($tag==null)

                                    <a href="{{ url('videos?name='.str_replace('_',' ',$tag)  ) }}"
                                       class="  mx-1 rounded  text-white bg-secondary  px-2 p-1 d-inline-block hoverable-dark ">
                                        <small class="  ">
                                            {{$tag}}
                                        </small>
                                    </a>
                                @endforeach

                            </div>
                        </div>
                    </div>

                    {{--                    related videos--}}
                    <div class="col-md-4 bg-light rounded-6 p-2">
                        <div class="text-primary font-weight-bold">ویدیو های مرتبط</div>
                        <div class="card-divider   my-1"></div>
                        <div class="row">
                            @foreach( $relatedVideos as $vid )
                                <div class="  col-sm-6 col-md-12">
                                    <a href="{{ url('video/'.$vid->id) }}"
                                       class="d-block "

                                    >
                                        <div class="  bg-transparent  w-100 ">

                                            <div class=" position-relative    ">
                                                <a href="{{ url('video/'.$vid->id) }}"
                                                   class="d-block overflow-hidden rounded-5  shadow-2-soft">
                                                    <div class="  position-absolute rounded-5   img-overlay">▶️</div>
                                                    <img class="  w-100 rounded-5   "
                                                         style="z-index: 0;height: 11rem"
                                                         onerror="this.onerror=null;this.src='{{asset('img/noimage.png')}}';this.parentElement.href='{{asset('img/noimage.png')}}'"
                                                         src="{{ asset('storage/'.Helper::$docsMap['videos'].'/'.$vid->id.'.jpg')}}"
                                                         alt="">
                                                    <div
                                                        class="row position-absolute bottom-0 start-0 end-0 opacity-80">
                                                        <div class="col">
                                            <span
                                                class="  rounded-start hoverable-dark bg-primary text-white small   px-2 ">  {{
                                                    \App\Models\Category::findOrNew($vid->category_id)->name
                                                }}
                                            </span>
                                                            <span
                                                                class="   rounded-end  bg-secondary text-white small   px-2 ">
                                                          {{getDuration($vid->duration)  }}

                                                </span>
                                                        </div>

                                                    </div>
                                                </a>
                                            </div>

                                        </div>
                                        <div
                                            class="m-card-body  px-2 d-flex  flex-column  align-self-stretch    text-center z-index-1">

                                            <div class="text-indigo   text-end max-2-line " style="font-size:13px">
                                                @php($ago= Morilog\Jalali\Jalalian::forge ($vid->created_at,$timezone)->ago())
                                                {{ (str_contains($ago,'ماه')|| str_contains($ago,'سال'))?Morilog\Jalali\Jalalian::forge($data->created_at,$timezone)->format('%A, %d %B %Y ⏰ H:i'):$ago}}
                                            </div>
                                            <div class="card-divider opacity-50"></div>
                                            <div
                                                class="text-primary small  my-1 text-start max-2-line "> {{ $vid->title }}</div>
                                        </div>
                                        <div class="m-card-footer  bg-transparent position-relative w-100 py-1">

                                        </div>

                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <section class="   my-3 py-3 px-4   row bg-primary   rounded-3 position-relative blur ">
                    {{--<div class="d-md-inline-block d-sm-none d-none    align-self-center position-absolute right-0 top-50   ">--}}
                    {{--<p class="vertical  text-white text-lg    ">جدید ترین ها</p>--}}
                    {{--</div>--}}
                    <div class="  col-md-4   ">
                        <h4 class="  text-white    ">جدید ترین ها</h4>
                    </div>
                    <data-swiper type="video" doc-type="{{Helper::$docsMap['videos']}}" class="    col-md-8  "
                                 data-link="{{route('video.search')}}"
                                 root-link="{{route('/')}}"
                                 category-data="{{\App\Models\Category::get()}}"
                                 province-data="{{json_encode([])}}"
                                 img-link="{{asset("storage/")}}"
                                 asset-link="{{asset("img/")}}"
                                 width="13rem;"
                    >


                    </data-swiper>


                </section>
            </div>

        </div>
    @endif
@stop

@section('scripts')
    @if (isset($video))

        <script>
            document.addEventListener("DOMContentLoaded", function (event) {

                window.initPlayer("{{$video}}", "{{'360px'}}");

            });


        </script>
    @endif
@stop
