@php

    $table=\App\Models\Table::where('id',$param)->with('tournament')->first();


@endphp
{{--{{json_encode( $images)}}--}}
@if(!$table)
    <div class="text-center font-weight-bold text-danger mt-5">جدول یافت نشد</div>
@else

    <div class="  my-3  mx-auto ">
        <div id="form-create" class=" mx-auto   col-md-10  ">


            <table-editor class=" "
                          tournament-data="{{ \App\Models\Tournament::get(['id','name'])}}"
                          sport-data="{{ \App\Models\Sport::get(['id','name'])}}"
                          img-link="{{ asset('storage/'.Helper::$docsMap['tournament'] )}}"
                          mode="edit"
                          table-data="{{$table}}"
                          tournament-edit-link="{{route('tournament.edit')}}"
                          send-link="{{route('table.edit')}}"
                          remove-link="{{route('table.remove')}}"
            >
            </table-editor>


        </div>

    </div>
@endif

@push('scripts')
    <script>


    </script>
@endpush
