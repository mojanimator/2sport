@extends('layouts.app')
@section('title')
    بازیکنان
@stop
@section('content')

    <div class=" col-lg-10 mx-auto  my-1 ">

        <section class="     ">

            <search-videos

                type="{{Helper::$categoryType['videos']}}"
                img-link="{{asset("storage/")}}"
                asset-link="{{asset("img/")}}"
                edit-link="{{route('video.edit')}}"
                remove-link="{{route('video.remove')}}"
                user-data="{{json_encode([])}}"
                data-link="{{route('video.search')}}"
                url-params="{{json_encode( request()->all()) }}"
                category-data="{{\App\Models\Category::where('type_id',Helper::$categoryType['videos'])->get()}}"
            ></search-videos>

        </section>
    </div>
@endsection
