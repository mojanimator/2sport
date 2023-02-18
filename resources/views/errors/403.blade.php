@extends('layouts.app')
@section('title')
    خطای دسترسی
@stop

@section('content')


    <div class="alert alert-danger mt-5 m-4">
        <strong>خطای دسترسی!</strong> <br>
        {{ /*$exception->getMessage()*/ 'مجاز نیستید' }} <strong>
            <br>
            <a
                    href="@if(auth()->user()){{route('panel.view')}}
                    @else{{route('login')}}@endif"
                    class="text-danger d-block text-center">
                بازگشت <i class="fa fa-backward text-dark-red"></i></a></strong>
    </div>
@stop