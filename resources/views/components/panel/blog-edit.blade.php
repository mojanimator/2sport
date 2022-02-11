@php

    $blog=\App\Models\Blog::where('id',$param)->with('docs:id,type_id,docable_id')->first();
if ($blog){
$docs=$blog->getRelation(  'docs' );
 $image=$docs->where('type_id',Helper::$docsMap['blog'])->first() ;
 if($image)
 $image=asset('storage')."/$image->type_id/$image->id.jpg";

}

@endphp
{{--{{json_encode( $images)}}--}}
@if(!$blog)
    <div class="text-center font-weight-bold text-danger mt-5">خبر یافت نشد</div>
@else

    <div class="  my-3  mx-auto ">
        <div id="form-create" class=" mx-auto col-sm-10 col-md-8  ">


            <blog-editor
                    mode="edit"
                    class=" "
                    cors-header="{{json_encode(['Access-Control-Allow-Origin'=>url(''),'Access-Control-Allow-Methods'=>'GET', 'mode'=> 'no-cors',])}}"
                    category-data="{{\App\Models\Category::where('type_id',Helper::$categoryType['blog'])->get()}}"
                    blog-data="{{$blog}}"
                    old-img="{{$image}}"
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
