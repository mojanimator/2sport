@extends('layouts.app')
@section('title')
    محصولات
@stop
@section('content')

    <div class="col-lg-10 mx-auto   my-1">
        <section class="     ">

            <search-products
                    type="{{Helper::$typesMap['products']}}"
                    img-link="{{asset("storage/")}}"
                    asset-link="{{asset("img/")}}"
                    data-link="{{route('product.search')}}"
                    url-params="{{json_encode( request()->all()) }}"
                    shop-data="{{\App\Models\Shop::where('active',true)->select('id','name')->get()}}"
                    {{--province-data="{{\App\Models\Province::get()}}"--}}
                    {{--county-data="{{\App\Models\County::get()}}"--}}
            >

            </search-products>

        </section>
    </div>
@endsection
