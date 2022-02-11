{{--@php--}}
{{--$user=auth()->user();--}}
{{--$admin= $user && ($user->role=='bl' || $user->role=='go');--}}
{{--if(!$admin){--}}
{{--header("Location: " . URL::to('/panel'), true, 302);--}}
{{--exit();--}}
{{--}--}}
{{--@endphp--}}

<div class="  my-3  mx-auto ">
    <div id="form-create" class=" mx-auto  col-lg-10  ">


        <blog-editor class=" "
                     category-data="{{\App\Models\Category::where('type_id',Helper::$categoryType['blog'])->get()}}"
                     old-title="{{old('title')}}"
                     old-summary="{{old('summary')}}"
                     old-img="{{old('img')}}"
                     old-tags="{{old('tags')}}"
                     send-link="{{route('blog.create')}}"
        >کلیک کنید
        </blog-editor>


    </div>

</div>


@push('scripts')
    <script>


    </script>
@endpush
