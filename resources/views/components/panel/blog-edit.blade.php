@php
    $blog=\App\Models\Blog::where('id',$param)->with('docs:id,type_id,docable_id')->first();

@endphp
@if(!$blog)
    <div class="text-center font-weight-bold text-danger mt-5">خبر یافت نشد</div>
@else
    @cannot('editItem',[\App\Models\User::class,$blog,false ])
        @php
            header("Location: " . URL::to('/panel/blogs'), true, 302);
        exit();
        @endphp
    @endcannot
    {{--{{json_encode( $images)}}--}}

    @php

        $docs=$blog->getRelation(  'docs' );
     $image=$docs->where('type_id',Helper::$docsMap['blog'])->first() ;
     if($image)
     $image=asset('storage')."/$image->type_id/$image->id.jpg";

    @endphp



    <div class="  my-3  mx-auto ">
        <div id="form-create" class=" mx-auto col-sm-10 col-md-8  ">


            <blog-editor
                mode="edit"
                class=" "
                cors-header="{{json_encode(['Access-Control-Allow-Origin'=>url(''),'Access-Control-Allow-Methods'=>'GET', 'mode'=> 'no-cors',])}}"
                category-data="{{\App\Models\Category::where('type_id',Helper::$categoryType['blog'])->get()}}"
                blog-data="{{$blog}}"
                old-img="{{$image}}"
                old-tags="{{$blog->tags}}"
                remove-link="{{route('blog.remove')}}"
                send-link="{{route('blog.edit')}}"
            >کلیک کنید
            </blog-editor>


        </div>

    </div>
@endif

@push('scripts')
    <script>


    </script>
@endpush
