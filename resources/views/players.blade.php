@extends('layouts.app')
@section('title')
    بازیکنان
@stop
@section('content')

    <div class=" col-lg-10 mx-auto  my-1">

        <section class="     ">

            <search-players

                type="{{Helper::$typesMap['players']}}"
                img-link="{{asset("storage/")}}"
                asset-link="{{asset("img/")}}"
                data-link="{{route('player.search')}}"
                url-params="{{json_encode( request()->all()) }}"
                sport-data="{{\App\Models\Sport::get()}}"
                province-data="{{\App\Models\Province::get()}}"
                county-data="{{\App\Models\County::get()}}">

            </search-players>

        </section>
    </div>
@endsection
