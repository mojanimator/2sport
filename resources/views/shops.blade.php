@extends('layouts.app')
@section('title')
    فروشگاه ها
@stop
@section('content')

    <div class="col-lg-10 mx-auto   my-1">
        <section class="     ">

            <search-shops
                    type="{{Helper::$typesMap['shops']}}"
                    img-link="{{asset("storage/")}}"
                    asset-link="{{asset("img/")}}"
                    data-link="{{route('shop.search')}}"
                    url-params="{{json_encode( request()->all()) }}"
                    shop-data="{{\App\Models\Shop::select('id','name')->get()}}"
                    province-data="{{\App\Models\Province::get()}}"
                    sport-data="{{\App\Models\Sport::get()}}"
                    county-data="{{\App\Models\County::get()}}"
            >

            </search-shops>

        </section>
    </div>
@endsection
