@cannot('createItem',[\App\Models\User::class,\App\Models\Table::class,false ])
    @php
        header("Location: " . URL::to('/panel/tables'), true, 302);
    exit();
    @endphp
@endcannot

<div class="  my-3  mx-auto ">
    <div id="form-create" class=" mx-auto  col-lg-10  ">

        {{--        @php($cat=collect(Helper::$tableType)->map(function ($id,$name){return  ['id'=>$id,'name'=>$name,];})->values())--}}

        <table-editor class=" "
                      tournament-data="{{ \App\Models\Tournament::select('id','name')->get()}}"
                      sport-data="{{ \App\Models\Sport::select('id','name')->get()}}"

                      send-link="{{route('table.create')}}"
                      create-tournament-link="{{route('tournament.create')}}"
        >
        </table-editor>


    </div>

</div>


@push('scripts')
    <script>


    </script>
@endpush
