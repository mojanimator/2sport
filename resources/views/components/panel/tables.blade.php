<div class="row mt-3 mx-auto   ">
    <div class="col-md-6   ">
        <a href="{{url('panel/table/create')}}" class="my-1  d-block ">
            <div class="card move-on-hover" style="background-color: #38c17221">
                <div class="card-body  p-3  blur">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <h5 class="  mb-0 text-success font-weight-bold">
                                    جدول جدید
                                </h5>
                                <h5 class=" text-sm text-black-50 font-weight-bolder mb-0">
                                    &nbsp

                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="  ">
                                <i class="fa fa-3x fa-plus-circle text-success m-1"
                                   aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>


    <search-tables
            admin="{{true}}"
            panel="{{true}}"
            crop-ratio="{{Helper::$cropsRatio['tournament']}}"
            img-link="{{ asset('storage/'.Helper::$docsMap['tournament'] )}}"
            tournaments-data="{{ \App\Models\Tournament::get(['id','name']) }}"
            sports-data="{{ \App\Models\Sport::get(['id','name']) }}"
            edit-link="{{route('table.edit')}}"
            remove-link="{{route('table.remove')}}"
            data-link="{{route('table.search')}}"
            url-params="{{json_encode( request()->all()) }}"
    >

    </search-tables>

</div>