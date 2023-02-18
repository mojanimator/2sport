@cannot('createItem',[\App\Models\User::class,\App\Models\Product::class,false ])
    @php
        header("Location: " . URL::to('/panel/products'), true, 302);
    exit();
    @endphp
@endcannot

@php
    $user=auth()->user();
    $shops=[];
    if($user->role=='go' || $user->role=='ad')
    $shops=\App\Models\Shop::where('active',true)->get(['id','name']);
    if($user->role=='us')
    $shops=\App\Models\Shop::where('user_id',$user->id)->where('active',true)->get(['id','name']);
@endphp

<div class="  my-3 ">
    <div class="row mx-auto justify-content-center">
        <div class="col-md-10 col-sm-12  ">
            <div class="card bg-light">
                <h5 class="card-header text-center text-white bg-primary">ثبت محصول</h5>

                <div class="card-body  ">


                    <form id="form-create" method="POST" action="{{ route('product.create') }}" class="text-right  row">
                        @csrf


                        <div class="row mx-auto">
                            @for ($i = 0; $i < Helper::$product_image_limit; $i++)

                                <image-uploader key="{{$i}}" style=" min-width:{{1*10}}rem;min-height:{{10}}rem"
                                                class="  col-sm-12 col-md-6  mx-auto my-1 overflow-auto" id="img{{$i}}"
                                                label="تصویر محصول"
                                                for-id="img{{$i}}" ref="imageUploader-img{{$i}}"
                                                crop-ratio="{{Helper::$cropsRatio['product']}}"
                                                link="null"
                                                preload="null"
                                                height="10" mode="create">

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


                        <div class="row col-md-10 mx-auto    ">
                            <div class="col-md-8 my-2 form-outline">

                                <input id="name" type="text"
                                       class="  px-4 form-control @error('name') is-invalid @enderror"
                                       name="name"
                                       value="{{ old('name') }}" autocomplete="name" autofocus>
                                <label for="name"
                                       class="col-md-12    form-label  text-md-right">نام محصول</label>
                            </div>
                            <div class="col-md-4 my-2 form-outline">

                                <input id="count" type="text"
                                       class="  px-4 form-control @error('count') is-invalid @enderror"
                                       name="count"
                                       value="{{ old('count') }}" autocomplete="count" autofocus>
                                <label for="count"
                                       class="col-md-12    form-label  text-md-right"> تعداد موجود</label>
                            </div>
                            <div class=" text-danger text-start small     " role="alert">
                                <strong id="err-name"> </strong>
                            </div>
                            <div class=" text-danger text-start small     " role="alert">
                                <strong id="err-count"> </strong>
                            </div>
                        </div>
                        <div class="row col-md-10 mx-auto px-0">
                            <div class="col-md-6    ">
                                <div class="my-2 form-outline">

                                    <input id="price" type="text"
                                           class="  px-4 form-control @error('price') is-invalid @enderror"
                                           name="price"
                                           value="{{ old('price') }}" autocomplete="price" autofocus>
                                    <label for="price"
                                           class="col-md-12    form-label  text-md-right">قیمت (تومان)</label>
                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-price"> </strong>
                                </div>
                            </div>
                            <div class="col-md-6    ">
                                <div class="my-2 form-outline">

                                    <input id="discount_price" type="text"
                                           class="  px-4 form-control @error('discount_price') is-invalid @enderror"
                                           name="discount_price"
                                           value="{{ old('discount_price') }}" autocomplete="discount_price" autofocus>
                                    <label for="discount_price"
                                           class="col-md-12    form-label  text-md-right">قیمت با تخفیف (تومان)</label>
                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-discount_price"> </strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-10 row mx-auto my-2 ">

                            {{--<label for="sport-input"--}}
                            {{--class="col-12 col-form-label text-right">رشته ورزشی</label>--}}
                            <select id="group_id" name="group_id"
                                    class="px-4 form-control{{ $errors->has('group_id')  ? ' is-invalid' : '' }}">
                                <option value="0">دسته بندی</option>
                                @foreach(\App\Models\Sport::get() as $s)
                                    <option value="{{$s->id}}"
                                            {{ old('sport_id')==$s->id? ' selected ':''}} >{{$s->name}}</option>

                                @endforeach
                            </select>

                            <div class=" text-danger text-start small  col-12   " role="alert">
                                <strong id="err-group_id"> </strong>
                            </div>

                        </div>
                        <div class="col-md-10 row mx-auto my-2 px-0">
                            <div class="col-sm-12 my-1 my-sm-0 ">
                                {{--<label for="province-input"--}}
                                {{--class="col-12 col-form-label text-right">استان</label>--}}
                                <select id="shop" name="shop"
                                        class="px-4 form-control{{ $errors->has('shop')  ? ' is-invalid' : '' }}">
                                    <option value="0">انتخاب فروشگاه</option>
                                    @foreach($shops as $s)
                                        <option value="{{$s->id}}"
                                                {{ old('shop')==$s->id? ' selected ':''}} >{{$s->name}}</option>

                                    @endforeach
                                </select>

                                <div class=" text-danger text-start small  col-12   " role="alert">
                                    <strong id="err-shop"> </strong>
                                </div>

                            </div>

                        </div>

                        <div class="col-md-10 mx-auto    ">
                            <div class="my-2 form-outline">
                                <textarea id="description" rows="4"
                                          class="  px-4 form-control @error('description') is-invalid @enderror"
                                          name="description"
                                          autocomplete="description" autofocus>{{ old('description') }}</textarea>

                                <label for="description"
                                       class="col-md-12 col-form-label form-label  text-md-right">
                                    توضیحات محصول (جنس، رنگ ...)
                                </label>

                            </div>
                            <div class=" text-danger text-start small  col-12   " role="alert">
                                <strong id="err-description"> </strong>
                            </div>
                        </div>

                        <div class="col-md-10 mx-auto    ">
                            <div class="my-2 form-outline">
                                <textarea id="tags" rows="4"
                                          class="  px-4 form-control @error('tags') is-invalid @enderror"
                                          name="tags"
                                          placeholder="{{implode("\n",['ست گرمکن','لباس','نایک'])}}"
                                          autocomplete="tags" autofocus></textarea>

                                <label for="tags"
                                       class="col-md-12 col-form-label form-label  text-md-right">
                                    هشتگ ها (هر کلمه در یک خط جداگانه)
                                </label>

                            </div>
                            <div class=" text-danger text-start small  col-12   " role="alert">
                                <strong id="err-tags"> </strong>
                            </div>
                        </div>

                        <div class="form-group   mb-0">
                            <div class="col-md-12  mt-2">
                                <button onclick=" submitWithFiles(event,{'upload_pending': true})" type="button"
                                        class="btn btn-success btn-block font-weight-bold py-3">
                                    ثبت
                                </button>
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
        document.addEventListener("DOMContentLoaded", function (event) {


        });

        function submitWithFiles(event, extra = {}) {
            document.querySelector('#loading').classList.remove('d-none');
            validInputs();

            event.preventDefault();
            let fd = new FormData();
            let data = document.querySelector('#form-create').querySelectorAll('input, textarea, select');
            for (let i in data) {

                if (data[i].id && data[i].id.includes('img') && !data[i].id.includes('file') && extra['upload_pending'] !== true) {

                    let res = app.$refs['imageUploader-' + data[i].id].getCroppedData();
                    if (res)
                        fd.append('images[]', res);

                }
                else if (['count', 'price', 'discount_price'].includes(data[i].name)) {
                    fd.append(data[i].name, f2e(data[i].value));
                }
                else
                    fd.append(data[i].name, data[i].value);
            }
            for (let i in extra)
                fd.append(i, extra[i]);

            axios.post("{{route('product.create')}}", fd, {
                onUploadProgress: function (progressEvent) {
                    let percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    console.log(percentCompleted);
                }
            })

                .then((response) => {
//                        console.log(response);
                        document.querySelector('#loading').classList.add('d-none');

                        if (response.status == 200)
                            if (response.data.resume == true)
                                submitWithFiles(event);
                            else
                                window.location = '{{url('panel/product')}}'


                    }
                ).catch((error) => {
                document.querySelector('#loading').classList.add('d-none');
//                console.log(error.response.data.errors);

                invalidInputs(error.response.data.errors);
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