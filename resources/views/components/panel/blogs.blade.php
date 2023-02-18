@php
    $user=auth()->user();
    $admin= $user && ($user->role=='bl' || $user->role=='go');


@endphp
@cannot('createItem',[\App\Models\User::class,\App\Models\Blog::class,false ])
    @php
        header("Location: " . URL::to('/panel'), true, 302);
    exit();
    @endphp
@endcannot

<div class="row mt-3 mx-auto  ">
    <div class="col-md-6   ">
        <a href="{{url('panel/blog/create')}}" class="my-1  d-block ">
            <div class="card move-on-hover" style="background-color: #38c17221">
                <div class="card-body  p-3  blur">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <h5 class="  mb-0 text-success font-weight-bold">
                                    خبر جدید
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


    <search-blogs
        admin="{{true}}"
        type="{{Helper::$typesMap['blogs']}}"
        img-link="{{asset("storage/")}}"
        asset-link="{{asset("img/")}}"
        edit-link="{{route('blog.edit')}}"
        remove-link="{{route('blog.remove')}}"
        user-data="{{$admin? \App\Models\User::get(['id','name','family','username']):json_encode([])}}"
        data-link="{{route('blog.search')}}"
        url-params="{{json_encode( request()->all()) }}"
        category-data="{{\App\Models\Category::where('type_id',Helper::$categoryType['blog'])->get()}}"
    >

    </search-blogs>

</div>
