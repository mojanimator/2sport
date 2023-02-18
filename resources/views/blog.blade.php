@extends('layouts.app')

@php
    $data=\App\Models\Blog::where('id',$id)->where('active',true)->first();
@endphp

@section('title')
    {{$data?$data->title :'خبر یافت نشد'}}
@stop

@section('content')
    <nav id="navbars" class="navbar navbar-expand navbar-light bg-light   "
         aria-label="news">
        <div class=" container-fluid   ">

            <div class="navbar-collapse collapse row " id="navbar2" style="">
                <ul class="navbar-nav col-md-7  justify-content-center   ">

                    <li class="nav-item small ms-2  my-1">
                        <a href="{{route('blogs.view')}}"
                           class="nav-link px-1 px-md-2 text-primary  hoverable-primary rounded font-weight-bold {{str_contains( url()->current(),'/blogs') ? 'text-white bg-primary':'text-primary'}}"
                           aria-current="page">اخبار ورزشی
                        </a>
                    </li>
                    <li class="nav-item   small ms-2 ms-sm-1 ms-md-1 my-1">
                        <a href="{{route('blogs.view',['view'=>'blog-iran'])}}"
                           class="nav-link px-1 px-md-2  hoverable-primary rounded  font-weight-bold {{str_contains( url()->current(),'blog-iran') ? 'text-white bg-primary':'text-primary'}}"
                           aria-current="page">اخبار فوتبال داخلی
                        </a>
                    </li>
                    <li class="nav-item  small ms-2 ms-sm-1 ms-md-1 my-1">
                        <a href="{{route('blogs.view',['view'=>'blog-world'])}}"
                           class="nav-link px-1 px-md-2  hoverable-primary rounded font-weight-bold {{str_contains( url()->current(),'blog-world') ? 'text-white bg-primary':'text-primary'}} "

                           aria-current="page">اخبار فوتبال خارجی
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav  col-md-5    justify-content-center justify-content-md-start">
                    <li class="nav-item  small ms-2 ms-sm-1 ms-md-1 my-1">
                        <a href="{{route('blogs.view',['view'=>'table-all'])}}"
                           class="nav-link px-1 px-md-2  hoverable-primary rounded font-weight-bold  {{str_contains( url()->current(),'/table') ? 'text-white bg-primary':'text-primary'}}"
                           aria-current="page" @click="view='table-all'">جدول لیگ ها</a>
                    </li>
                    <li class="nav-item  small ms-2 ms-sm-1 ms-md-1 my-1">
                        <a href="{{route('blogs.view',['view'=>'conductor'])}}"
                           class="nav-link px-1 px-md-2  hoverable-primary rounded font-weight-bold {{str_contains( url()->current(),'/conductor') ? 'text-white bg-primary':'text-primary'}} "
                           :class="view=='conductor'? 'text-white bg-primary':'text-primary'"
                           aria-current="page" @click="view='conductor'">کنداکتور ورزشی
                        </a>
                    </li>


                </ul>

            </div>
        </div>
    </nav>
    @php
        if ($data){
            $content=json_decode($data->content);
        }
    @endphp
    <div class="px-1 px-sm-2 px-md-3 mx-auto col-md-10 col-lg-8   my-5">
        @if (! isset($data))
            <div class="text-center font-weight-bold mt-5 ">
                <div class="   text-danger ">خبر یافت نشد</div>
                <a href="{{url()->previous()}}" class="list-item d-block hoverable-text-primary">بازگشت</a>
            </div>

        @else
            <div class=" shadow rounded p-2 my-3 text-center bg-primary text-white">{{$data->title}}</div>
            <div class="card py-4 px-4 mx-2 mx-sm-1" style="min-height: 100vh">
                <small class="text-end   mb-2 text-secondary"
                       style="font-size: .7rem">{{Morilog\Jalali\Jalalian::forge($data->published_at, new DateTimeZone('Asia/Tehran'))->format('%A, %d %B %Y ⏰ H:i')}}</small>
                @foreach($content as $section)

                    @if($section->type =='paragraph')
                        @if(isset($section->data->text))
                            <div class="small text-primary my-1 col-md-10 col-xl-8 mx-auto">
                                {{$section->data->text}}
                            </div>
                        @endif

                    @elseif($section->type =='image')
                        @if(isset($section->data->file))
                            <img class="mx-auto rounded col-md-8 col-xl-6 my-2"
                                 data-lity="{{$section->data->file->url}}"
                                 src="{{$section->data->file->url}}"
                            />

                        @endif
                    @elseif($section->type =='list')
                        @if(isset($section->data->items))
                            @foreach($section->data->items as $item)
                                <div class="  my-1 col-md-10 col-xl-8 ms-auto">
                                    <i class="fa fa-angle-left text-secondary small" aria-hidden="true"></i>
                                    <small class="mx-2  small font-weight-bold text-dark">{{$item}}</small>
                                </div>
                            @endforeach
                        @endif

                    @elseif($section->type =='table')
                        @if(isset($section->data->content))
                            <div class="table-responsive col-md-8 col-xl-6 my-2 mx-auto">
                                <table class="table table-bordered table-striped table-light  "
                                       style="white-space: nowrap;">
                                    @if($section->data->withHeadings)
                                        <thead class="">
                                        <tr>
                                            @foreach($section->data->content[0] as $h)
                                                <th class="  py-1 font-weight-bold  ">{{$h}}
                                                </th>
                                            @endforeach
                                        </tr>
                                        </thead>
                                    @endif
                                    <tbody>
                                    @foreach(array_slice($section->data->content,$section->data->withHeadings?1:0) as $row)
                                        <tr class=" ">
                                            @foreach($row as $col)
                                                <td class="py-1 {{is_numeric($col)? 'text-center':'ps-1'}}  px-1 overflow-hidden"
                                                >
                                                    <span class=" d-inline-block  {{is_numeric($col)? 'text-center':'ps-1'}} ">{{$col}}</span>

                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                        @endif
                    @elseif($section->type =='header')
                        @if(isset($section->data->text))
                            <div class="px-2 py-2  text-primary rounded   my-1 col-md-10 col-xl-8 mx-auto">
                                {{$section->data->text}}
                            </div>
                        @endif

                    @elseif($section->type =='quote')
                        @if(isset($section->data->text))
                            <div class="px-4 py-2  text-dark rounded bg-light my-1 col-md-10 col-xl-8 mx-auto">
                                @if(isset($section->data->caption))
                                    <span
                                            class="small font-weight-bold d-block mb-2">{{$section->data->caption}}</span>
                                @endif
                                <i class="fa fa-quote-right mx-2 small opacity-50" aria-hidden="true"></i>
                                <span class="text-secondary ">{{$section->data->text}}</span>
                                <i class="fa fa-quote-left mx-2 small opacity-50" aria-hidden="true"></i>
                            </div>
                        @endif
                    @endif
                @endforeach
                <div class="    me-auto  mt-4 opacity-75   small">

                    @foreach(explode(" ", $data->tags ) as $tag)
                        @continue($tag==null)

                        <a
                                {{--                                href="{{ route('blogs.view',['name'=> str_replace('_',' ',$tag)]) }}"--}}
                                class=" mb-1  ms-1 rounded  text-primary     px-1 py-0 d-inline-block  ">
                            <small class="  ">
                                {{$tag}}
                            </small>
                        </a>
                    @endforeach

                </div>
            </div>
        @endif

    </div>
@stop


@section('styles')
    <style>


    </style>
@stop

