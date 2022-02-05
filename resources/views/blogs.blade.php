@extends('layouts.app')

@section('content')
    <div class="container col-lg-10 ">
        {{--news and tables--}}
        <section class="row no-gutters mx-auto  px-4       mt-1 mt-md-3 ">


            <div class="col-md-6 col-sm-8  px-1 mx-auto my-1">
                @php
                    $blogs=\App\Models\Blog::select('id','title','summary','updated_at')->with('docs')->latest()->take(5)->get();
                @endphp
                <div class="  bg-gradient-dark-transparent rounded-3 p-0 overflow-hidden  position-relative"
                     style="height: 18rem;">
                    <div class=" bg-gradient-primary  p-2 overflow-hidden position-absolute rounded-bottom    text-white"
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
                                    <img src="{{asset('storage').$blog->type_id.'/'. (($img=$blog->getRelation('docs')->first())?$img->type_id.'/'. $img->id:'').'.jpg'}}"
                                         class=" d-block mx-auto  h-100  "
                                         alt="{{$blog->title}}"
                                         style=" object-fit:contain ;object-position: 0 0;">
                                    <div class="carousel-caption p-0   start-0 end-0  top-50 bottom-0 start-0 end-0  d-md-block   "
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
                $table=\App\Models\Table::where('type_id',Helper::$tableType['کنداکتور'])->first();
if($table){
            $data=json_decode($table->content) ;
            $img=$data->img;
            $data=$data->table;
            if($data)
            {  $conductors= $data->body;


            }
            }

            @endphp
            @if($table)
                <div class="col-md-6 col-sm-8 px-1 my-1 mx-auto">
                    <div class=" bg-gradient-dark-transparent rounded-3    overflow-hidden  position-relative"
                         style="height: 18rem;">
                        <small class="text-white position-absolute end-0 m-2 small"
                               style="font-size: .7rem;z-index: 1">{{Morilog\Jalali\Jalalian::forge($table->updated_at, new DateTimeZone('Asia/Tehran'))->format('%A, %d %B %Y')}}</small>
                        <div class=" bg-gradient-primary  p-2 overflow-hidden position-absolute rounded-bottom    text-white"
                             style="z-index: 1"
                        >
                            پخش زنده ورزشی
                        </div>
                        <img src="{{ $img }}"
                             class="  position-absolute w-100  h-100  "
                             alt=" "
                             style=" object-fit:cover ;object-position: 0 0; z-index: 0">
                        <div id="carouselConductor" class=" carousel slide  h-100"
                             data-mdb-ride="carousel" data-mdb-interval="5000">

                            <div class="carousel-indicators">
                                @foreach($conductors as  $idx=>$conductor)

                                    <button type="button" data-mdb-target="#carouselConductor"
                                            data-mdb-slide-to="{{$idx}}"
                                            class="{{$idx==0?  'active':''}}"
                                            aria-current="true" aria-label="{{$idx}} "></button>

                                @endforeach
                            </div>
                            <div class="carousel-inner  h-100 w-100 ">

                                @foreach($conductors as  $idx=>$conductor)

                                    <div class="  carousel-item h-100 w-100 {{$idx==0?  'active':''}}">

                                        <a href="/table/{{$table->id}}/{{str_replace(' ','-',$table->title)}}"
                                           class="carousel-caption border-3  m-3    d-block start-0 end-0   bottom-0 start-0 end-0  d-md-block   "
                                        >
                                            <div class="rounded-3 overflow-hidden  ">
                                                @foreach($conductor as $idx=>$program)
                                                    <div class="bg-gradient-info text-white py-1   m-0">{{$program->value}}</div>

                                                @endforeach
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
        {{--blog and table list navbar--}}
        <blogs
                table-link="{{route('table.search')}}"
                blog-link="{{route('blog.search')}}"
                url-params="{{json_encode( request()->all())}}"
                category-data="{{\App\Models\Category::where('type_id',Helper::$categoryType['blog'])->get()}}"></blogs>
    </div>
@endsection
