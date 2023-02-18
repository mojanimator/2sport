@php
    $user=auth()->user();
    $admin= $user && ( $user->role=='go');
    if(!$admin){
    header("Location: " . URL::to('/panel'), true, 302);
    exit();
    }
@endphp


<div class="row mt-3  mx-auto ">
    <div class="col-md-6   ">
        <a href="{{url('panel/agency/create')}}" class="my-1  d-block ">
            <div class="card move-on-hover" style="background-color: #38c17221">
                <div class="card-body  p-3  blur">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <h5 class="  mb-0 text-success font-weight-bold">
                                    نمایندگی جدید
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


    <search-agencies

            admin="{{true}}"
            panel="{{true}}"
            type="{{Helper::$typesMap['agencies']}}"
            img-link="{{asset("storage/")}}"
            asset-link="{{asset("img/")}}"
            edit-link="{{route('agency.edit')}}"
            remove-link="{{route('agency.remove')}}"
            user-data="{{$admin? \App\Models\User::where('role','ag')->get(['id','name','family','username']):json_encode([])}}"
            data-link="{{route('agency.search')}}"
            url-params="{{json_encode( request()->all()) }}"

            province-data="{{\App\Models\Province::get()}}"
            county-data="{{\App\Models\County::get()}}">

    </search-agencies>

</div>