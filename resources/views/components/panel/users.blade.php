<div class="row mt-3  mx-auto ">
    <div class="col-md-6   ">
        <a href="{{url('panel/user/create')}}" class="my-1  d-block ">
            <div class="card move-on-hover" style="background-color: #38c17221">
                <div class="card-body  p-3  blur">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <h5 class="  mb-0 text-success font-weight-bold">
                                    کاربر جدید
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

    @php($user=auth()->user())
    @php($admin= $user && ($user->role=='ad' || $user->role=='go'))

    <search-users
            price="{{0}}"
            admin="{{$admin}}"
            panel="{{true}}"
            type=""
            img-link="{{asset("storage/")}}"
            asset-link="{{asset("img/")}}"
            edit-link="{{route('user.edit')}}"
            remove-link="{{route('user.remove')}}"
            user-data="{{$admin? \App\Models\User::get(['id','name','family','username']):json_encode([])}}"
            data-link="{{route('user.search')}}"
            url-params="{{json_encode( request()->all()) }}"
         >

    </search-users>

</div>