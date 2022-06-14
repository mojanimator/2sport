@extends('layouts.app')

@php
    $data=\App\Models\Table::whereId($id)->with('tournament')->first();

@endphp

@section('title')
    {{$data?optional($data->tournament)->name?:$data->title:'جدول یافت نشد'}}
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
        $img=asset('storage/'.Helper::$docsMap['tournament'].$data->tournament_id.'.jpg');
            if ($data){
                if($data->tournament){
                    $data=\App\Models\Table::where('tournament_id',$data->tournament->id)->get()->all();
                    foreach ($data as $d) {
                        $d->content=json_decode($d->content);
                        $d->img=$img;
                        $d->header=$d->content->table->header;
                        $d->body=$d->content->table->body;


                       }

                    }
                 else{
                    $content=json_decode($data->content);
                    $header=$content->table->header;
                    $body=$content->table->body;
                    $tags=$content->tags;
            }
            }
    @endphp
    <div class="px-1 px-sm-2 px-md-3 mx-auto col-md-11 col-lg-10   my-5">
        @if (! isset($data))
            <div class="text-center font-weight-bold mt-5 ">
                <div class="   text-danger ">جدول یافت نشد</div>
                <a href="{{url('blogs').'?view=table-all'}}" class="list-item d-block hoverable-text-primary">بازگشت</a>
            </div>

        @elseif( is_array($data))
            <div class=" shadow rounded p-2 my-3 text-center bg-primary text-white">{{$d->tournament->name}}</div>

            @foreach($data as $d)
                <div class="card shadow-primary small">

                    <div class="card-header bg-primary text-white">{{$d->title}}</div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-light  "
                               style="white-space: nowrap;">
                            <thead class="table-primary text-primary">
                            <tr>
                                @foreach($d->header as $h)
                                    @if(str_contains($h, 'تیم') || str_contains($h, 'نام'))
                                        <th class="py-2 px-3 text-center   font-weight-bold    "
                                            style="min-width: 130px">{{$h}}
                                        </th>
                                    @else
                                        <th class="   px-3  py-2 font-weight-bold   ">{{$h}}
                                        </th>
                                    @endif
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($d->body as $row)
                                <tr class=" ">
                                    @foreach($row as $col)
                                        <td class=" py-1    overflow-hidden"
                                            style="font-size: 11px">
                                            @if($col->type=='img')
                                                <img src="{{$col->value}}" alt="" style="height: 3rem;">
                                            @else
                                                <div class=" text-center ">{{$col->value}}</div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @else
            <div class="card shadow-primary small">

                <div class="card-header bg-primary text-white">{{$data->title}}</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-light  "
                           style="white-space: nowrap;table-layout: auto  ">
                        <thead class="table-primary text-primary">
                        <tr>
                            @foreach($header as $h)
                                @if(str_contains($h, 'تیم') || str_contains($h, 'نام'))
                                    <th class="py-2 px-3 text-center   font-weight-bold    "
                                        style="min-width: 130px">{{$h}}
                                    </th>
                                @else
                                    <th class="   px-3  py-2  text-center  font-weight-bold   ">{{$h}}
                                    </th>
                                @endif
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($body as $row)
                            <tr class=" ">
                                @foreach($row as $col)
                                    <td class="   py-1  overflow-hidden"
                                        style="font-size: 11px">
                                        @if($col->type=='img')
                                            <img src="{{$col->value}}" alt="" style="height: 3rem;">
                                        @else
                                            <div class=" text-center py-1   ">{{$col->value}}</div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @endif
    </div>
@stop


@section('styles')
    <style>


        .accordion-button::after {
            /*color: white !important;*/
            /*background-color: white;*/
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e") !important;
        }
    </style>
@stop

