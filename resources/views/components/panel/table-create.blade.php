<div class="  my-3  mx-auto ">
    <div id="form-create" class=" mx-auto  col-lg-10  ">

        {{--        @php($cat=collect(Helper::$tableType)->map(function ($id,$name){return  ['id'=>$id,'name'=>$name,];})->values())--}}

        <table-editor class=" "
                      tournament-data="{{ \App\Models\Table::where('tournament','!=',null)->distinct('tournament')->pluck('tournament') }}"
                      category-data="{{json_encode(array_flip( Helper::$tableType))  }}"
                      send-link="{{route('table.create')}}"
        >
        </table-editor>


    </div>

</div>


@push('scripts')
    <script>


    </script>
@endpush
