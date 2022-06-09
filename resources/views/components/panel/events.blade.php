@cannot('createItem',[\App\Models\User::class, \App\Models\Event::class,false])
    @php
        header("Location: " . URL::to('/panel'), true, 302);
        exit();
    @endphp

@endcannot
<div class="row mt-3 mx-auto  ">
    @can('createItem',[\App\Models\User::class, \App\Models\Event::class,false])
        <div class="col-md-6   ">
            <a href="{{url('panel/event/create')}}" class="my-1  d-block ">
                <div class="card move-on-hover" style="background-color: #38c17221">
                    <div class="card-body  p-3  blur">
                        <div class="row">
                            <div class="col-8">
                                <div class="numbers">
                                    <h5 class="  mb-0 text-success font-weight-bold">
                                        رویداد جدید
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
    @endcan
    @php
        $user=auth()->user();
        $admin=$user->role=='ad' || $user->role=='go'
    @endphp

    <search-events
            admin="{{true}}"
            asset-link="{{asset("img/")}}"
            sport-data="{{\App\Models\Sport::get()}}"
            edit-link="{{route('event.edit')}}"
            remove-link="{{route('event.remove')}}"
            data-link="{{route('event.search')}}"
            url-params="{{json_encode( request()->all()) }}"
            user-data="{{$admin? \App\Models\User::get(['id','name','family','username']):json_encode([])}}"
    >

    </search-events>

</div>