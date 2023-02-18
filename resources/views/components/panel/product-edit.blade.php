@php
    $product = \App\Models\Product::where('id', $param)->with('docs:id,type_id,docable_id')->first();

@endphp
@if(!$product)
    <div class="text-center font-weight-bold text-danger mt-5">محصول یافت نشد</div>
@else
    @cannot('editItem',[\App\Models\User::class,$product,false ])
        @php
            header("Location: " . URL::to('/panel/products'), true, 302);
        exit();
        @endphp
    @endcannot
    {{--{{json_encode( $images)}}--}}

    @php

        $user=auth()->user();

        $shops =    \App\Models\Shop::select('id','name')->where('user_id', \App\Models\Shop::findOrNew($product->shop_id)->user_id)->get();
        $docs=$product->getRelation(   'docs' );

        $images=[];
        foreach ($docs->where('type_id',Helper::$docsMap['product'])->all( ) as $item) {
            $images[]=$item;
        }

    @endphp
    {{--if (Illuminate\Support\Facades\Gate::allows('editItem',  [\App\Models\User::class, $shop, false]  )){--}}




    <div class="  my-3 ">
        <div class="row mx-auto justify-content-center">
            <div class="col-md-10 col-sm-12  ">
                <div class="card bg-light">
                    <h5 class="card-header text-center text-white bg-primary">
                        <div class=" input-group my-2">
                            <label for="addres-input"
                                   class="  ">{{$product->name }}</label>
                            <button class="btn btn-danger rounded ms-auto font-weight-bold" type="button"
                                    id="addres-addon"
                                    onclick=" window.showDialog('confirm','از حذف اطمینان دارید؟',()=>remove,{{$product->id}})">
                                حذف
                            </button>
                        </div>
                    </h5>

                    <div class="card-body  ">


                        <form id="form-create" method="POST" action="{{ route('product.create') }}"
                              class="text-right  row">
                            @csrf


                            <div class="row">
                                @foreach ($images as $i=>$image)

                                    <image-uploader
                                            key="{{$i}}"
                                            class="  col-sm-12 col-md-6  mx-auto my-1 overflow-auto"
                                            id="img{{$i}}"
                                            label="تصویر محصول"
                                            for-id="{{$image->id}}" ref="imageUploader-img{{$i}}"
                                            crop-ratio="{{1}}"
                                            link="{{route('product.edit')}}"
                                            type="{{Helper::$docsMap['product']}}"
                                            preload="{{asset('storage')."/$image->type_id/$image->id.jpg"}}"
                                            height="10" mode="edit">

                                    </image-uploader>

                                @endforeach
                                @for ($i = count($images); $i < Helper::$club_image_limit; $i++)

                                    <image-uploader
                                            class="  col-sm-12 col-md-6  mx-auto my-1 overflow-auto"
                                            id="img{{$i}}"
                                            label="تصویر محصول"
                                            for-id="{{$product->id}}" ref="imageUploader-img{{$i}}"
                                            crop-ratio="{{1.2}}"
                                            link="{{route('product.edit')}}"
                                            type="{{Helper::$docsMap['product']}}"
                                            preload=""
                                            height="10" mode="edit">

                                    </image-uploader>

                                @endfor


                            </div>

                            <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-images"> </strong>
                                    </span>
                            <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-images.0"> </strong>
                                    </span>
                            <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-images.1"> </strong>
                                    </span>
                            <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-images.2"> </strong>
                                    </span>


                            <div class="col-md-12 mx-auto   ">
                                <div class="m-2  form-outline input-group">

                                    <input id="name" type="text"
                                           class="  px-4 form-control @error('name') is-invalid @enderror"
                                           name="name"
                                           value="{{ $product->name }}" autocomplete="name" autofocus>
                                    <label for="name"
                                           class="col-md-12    form-label  text-md-right">نام محصول</label>
                                    <button class="btn btn-secondary rounded" type="button"
                                            id="name-addon"
                                            onclick=" submitWithFiles(event,{'name':document.getElementById('name').value})">

                                        ویرایش
                                    </button>

                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-name"> </strong>
                                </div>
                            </div>
                            <div class="col-md-12  mx-auto  ">
                                <small class="mx-3 mt-3"> فروشگاه</small>
                                <div class=" m-2 mt-0  input-group   ">
                                    {{--<label for="province-input"--}}
                                    {{--class="col-12 col-form-label text-right">استان</label>--}}
                                    <select id="shop_id" name="shop_id"
                                            class="   form-control{{ $errors->has('shop_id')  ? ' is-invalid' : '' }}">
                                        <option value="">انتخاب فروشگاه</option>
                                        @foreach($shops as $shop)
                                            <option value="{{$shop->id}}"
                                                    {{ $product->shop_id==$shop->id? ' selected ':''}} >{{$shop->name }}</option>

                                        @endforeach
                                    </select>

                                    <button class="btn btn-secondary rounded" type="button"
                                            id="shop_id-addon"
                                            onclick=" submitWithFiles(event,{'shop_id':document.getElementById('shop_id').value})">

                                        ویرایش
                                    </button>
                                </div>
                                <div class=" text-danger text-start small  col-12   " role="alert">
                                    <strong id="err-owner_id"> </strong>
                                </div>

                            </div>
                            <div class="  mx-0 px-0 row  border border-primary rounded-3 my-2 py-2 position-relative">
                                <div class=" mb-2   w-100  text-end">
                                    <button class="btn btn-secondary rounded    " type="button"
                                            id="price-addon"
                                            onclick=" submitWithFiles(event,{

                                                    'price':f2e(document.getElementById('price').value),
                                                    'discount_price':f2e(document.getElementById('discount_price').value),
                                                    'count':f2e(document.getElementById('count').value),
                                                    'group_id':f2e(document.getElementById('group_id').value),
                                                })">

                                        ویرایش
                                    </button>
                                </div>
                                <div class="my-1     col-sm-6">
                                    <div class="form-outline">
                                        <input id="price" type="number"
                                               class="px-4 form-control @error('price') is-invalid @enderror"
                                               value="{{ $product->price}}"
                                               name="price"
                                               autocomplete="price">
                                        <label for="price"
                                               class="col-md-12  form-label text-md-right">قیمت (تومان)</label>

                                    </div>
                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-price"> </strong>
                                    </div>
                                </div>
                                <div class="my-1     col-sm-6">
                                    <div class="form-outline">
                                        <input id="discount_price" type="number"
                                               class="px-4 form-control @error('discount_price') is-invalid @enderror"
                                               value="{{ $product->discount_price}}"
                                               name="discount_price"
                                               autocomplete="discount_price">
                                        <label for="discount_price"
                                               class="col-md-12  form-label text-md-right">قیمت با تخفیف
                                            (تومان)</label>

                                    </div>
                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-discount_price"> </strong>
                                    </div>
                                </div>
                                <div class="my-1     col-sm-6">
                                    <div class="form-outline">
                                        <input id="count" type="number"
                                               class="px-4 form-control @error('count') is-invalid @enderror"
                                               value="{{ $product->count}}"
                                               name="count"
                                               autocomplete="count">
                                        <label for="count"
                                               class="col-md-12  form-label text-md-right">تعداد موجود</label>

                                    </div>
                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-count"> </strong>
                                    </div>
                                </div>
                                <div class="col-sm-6 my-1">
                                    <div class="input-group px-0 ">
                                        <select id="group_id" name="group_id"
                                                class="px-4 form-control{{ $errors->has('group_id')  ? ' is-invalid' : '' }}">
                                            <option value="0">دسته بندی</option>
                                            @foreach(\App\Models\Sport::get() as $s)
                                                <option value="{{$s->id}}"
                                                        {{ $product->group_id==$s->id? ' selected ':''}} >{{$s->name}}</option>

                                            @endforeach
                                        </select>

                                    </div>
                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-group_id"> </strong>
                                    </div>

                                </div>

                            </div>


                            <div class="col-md-12 mx-auto    ">
                                <div class="my-2 form-outline">
                                <textarea id="tags" rows="4"
                                          class="  px-4 form-control @error('tags') is-invalid @enderror"
                                          name="tags"
                                          autocomplete="description" autofocus>{{ $product->tags }}</textarea>

                                    <label for="tags"
                                           class="col-md-12 col-form-label form-label  text-md-right">
                                        هشتگ ها (هر کلمه در یک خط جداگانه)
                                    </label>
                                    <div class=" input-group my-1">

                                        <button class="btn btn-secondary rounded ms-auto" type="button"
                                                id="tags-addon"
                                                onclick=" submitWithFiles(event,{
                                            'tags':document.getElementById('tags').value,
                                           })">ویرایش
                                        </button>
                                    </div>
                                </div>
                                <div class=" text-danger text-start small  col-12   " role="alert">
                                    <strong id="err-tags"> </strong>
                                </div>
                            </div>
                            <div class="row mx-auto  px-0   ">
                                <div class="my-2 form-outline">
                                <textarea id="description" rows="4"
                                          class="  px-4 form-control @error('description') is-invalid @enderror"
                                          name="description"
                                          autocomplete="description" autofocus>{{ $product->description }}</textarea>

                                    <label for="description"
                                           class="col-md-12 col-form-label form-label  text-md-right">
                                        توضیحات
                                    </label>
                                    <div class=" input-group my-1">

                                        <button class="btn btn-secondary rounded ms-auto" type="button"
                                                id="times-addon"
                                                onclick=" submitWithFiles(event,{
                                            'description':document.getElementById('description').value,
                                           })">ویرایش
                                        </button>
                                    </div>
                                </div>
                                <div class=" text-danger text-start small  col-12   " role="alert">
                                    <strong id="err-description"> </strong>
                                </div>
                            </div>


                        </form>
                    </div>
                    <div class="card-footer text-center text-white bg-primary"></div>
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script>

            function remove(id) {
                document.querySelector('#loading').classList.remove('d-none');

                data = {'id': id};
                axios.post("{{route('product.remove')}}", data, {})
                    .then((response) => {
//                        console.log(response);
                            document.querySelector('#loading').classList.add('d-none');
                            if (response.status === 200)
                                window.location = "{{url('panel/product')}}";
                        }
                    ).catch((error) => {
                    document.querySelector('#loading').classList.add('d-none');
                    let errors = '';
                    if (error.response && error.response.status === 422)
                        for (let idx in error.response.data.errors)
                            errors += error.response.data.errors[idx] + '<br>';
                    else {
                        errors = error;
                    }
                    window.showToast('danger', errors);
                });
            }

            function submitWithFiles(event, data) {
                document.querySelector('#loading').classList.remove('d-none');
                validInputs();
                event.preventDefault();
                data = {'id': "{{$product->id}}", ...data};
                axios.post("{{route('product.edit')}}", data, {
                    onUploadProgress: function (progressEvent) {
                        let percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
//                        console.log(percentCompleted);
                    }
                })

                    .then((response) => {
//                        console.log(response);
                            document.querySelector('#loading').classList.add('d-none');

                            if (response.status === 200) {
                                if (response.data && response.data.status === 'success' && response.data.msg)
                                    window.showToast('success', response.data.msg);
                            }
                                {{--window.location = '{{url('panel/club')}}'--}}

                        }
                    ).catch((error) => {
                    document.querySelector('#loading').classList.add('d-none');
//                console.log(error.response.data.errors);

                    invalidInputs(error.response.data.errors);
                    let errors = '';
                    if (error.response && error.response.status === 422)
                        for (let idx in error.response.data.errors)
                            errors += error.response.data.errors[idx] + '<br>';
                    else {
                        errors = error;
                    }
                    window.showToast('danger', errors);
//                    if (error.response && error.response.status === 422)
//                        for (let idx in error.response.data.errors)
//                            this.errors += '' + error.response.data.errors[idx] + '<br>';
//                    else {
//                        this.errors = error;
//                    }
//                    window.showDialog('danger', this.errors, onclick = null);
                });
            }


            //        });
        </script>
    @endpush

@endif