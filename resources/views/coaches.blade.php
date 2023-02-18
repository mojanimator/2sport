@extends('layouts.app')
@section('title')
    مربیان
@stop
@section('content')

    <div class="col-lg-10 mx-auto   my-1">
        <section class="     ">

            <search-coaches
                    type="{{Helper::$typesMap['coaches']}}"
                    img-link="{{asset("storage/")}}"
                    asset-link="{{asset("img/")}}"
                    data-link="{{route('coach.search')}}"
                    url-params="{{json_encode( request()->all()) }}"
                    sport-data="{{\App\Models\Sport::get()}}"
                    province-data="{{\App\Models\Province::get()}}"
                    county-data="{{\App\Models\County::get()}}">

            </search-coaches>

        </section>
    </div>
@endsection
