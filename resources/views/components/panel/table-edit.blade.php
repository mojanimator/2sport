@php
    $table=\App\Models\Table::where('id',$param)->first();


@endphp
{{--{{json_encode( $images)}}--}}
@if(!$table)
    <div class="text-center font-weight-bold text-danger mt-5">جدول یافت نشد</div>
@else

    <div class="  my-3  mx-auto ">
        <div id="form-create" class=" mx-auto   col-md-10  ">


            <table-editor class=" "
                          tournament-data="{{ \App\Models\Table::where('tournament','!=',null)->distinct('tournament')->pluck('tournament') }}"

                          mode="edit"
                          table-data="{{$table}}"
                          category-data="{{json_encode(array_flip( Helper::$tableType))  }}"
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
