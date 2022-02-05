@extends('layouts.app')
@section('title')
    مراکز ورزشی
@stop
@section('content')

    <div class="col-lg-10 mx-auto   my-1">
        <section class="     ">

            <search-clubs
                    type="{{Helper::$typesMap['clubs']}}"
                    img-link="{{asset("storage/")}}"
                    asset-link="{{asset("img/")}}"
                    data-link="{{route('club.search')}}"
                    url-params="{{json_encode( request()->all()) }}"
                    sport-data="{{\App\Models\Sport::get()}}"
                    province-data="{{\App\Models\Province::get()}}"
                    county-data="{{\App\Models\County::get()}}">

            </search-clubs>

        </section>
    </div>
@endsection
@section('scripts')

    <script>


    </script>
@stop