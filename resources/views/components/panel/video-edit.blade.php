@php
    $data=\App\Models\Video::where('id',$param)->first();


@endphp
@if(!$data)
    <div class="text-center font-weight-bold text-danger mt-5">ویدیو یافت نشد</div>
@else
    @cannot('editItem',[\App\Models\User::class,$data,false ])
        @php
            header("Location: " . URL::to('/panel/videos'), true, 302);
        exit();
        @endphp
    @endcannot
    @php

        $user=auth()->user();

    $video=asset('storage')."/".Helper::$docsMap['videos']."/$data->id.mp4";
    $poster=asset('storage')."/".Helper::$docsMap['videos']."/$data->id.jpg";


        $tmp=Morilog\Jalali\Jalalian::fromDateTime($data->created_at);
        $data->d=$tmp->getDay();
        $data->m=$tmp->getMonth();
        $data->y=$tmp->getYear();
        $data->h=$tmp->getHour();
        $data->mm=$tmp->getMinute();



    @endphp
    {{--{{json_encode( $images)}}--}}


    <div class="  my-3 ">
        <div class="row mx-auto justify-content-center">
            <div class="col-md-10 col-sm-12  ">
                <div class="card bg-light   ">
                    <div class="  card-header   text-white bg-primary  d-flex justify-content-between  ">
                        <div class="    ">
                            <label for="delete-input"
                                   class="  ">{{$data->title }}</label>
                            <button class="btn btn-sm btn-danger rounded  ms-2 font-weight-bold" type="button"
                                    id="delete-addon"
                                    onclick=" window.showDialog('confirm','از حذف اطمینان دارید؟',()=>remove,{{$data->id}})">
                                حذف
                            </button>
                        </div>

                    </div>
                    <div class="card-body  ">


                        <form id="form-create" method="POST" action="{{ route('video.edit') }}"
                              class="text-right  row">
                            @csrf

                            <div class="row flex-row-reverse">
                                <div class="col-lg-5 ">
                                    <div class="my-2">
                                        <image-uploader
                                            class="  col-10 col-lg-12  mx-auto     overflow-x-scroll"
                                            id="img"
                                            label="پوستر ویدیو"
                                            for-id="{{$data->id}}" ref="imageUploader"
                                            crop-ratio="{{Helper::$cropsRatio['videos']}}"
                                            link="{{route('video.edit')}}"
                                            required="yes"
                                            preload="{{$poster}}"
                                            height="10" mode="edit">
                                        </image-uploader>
                                        <span class=" text-danger text-center small    col-12"
                                              role="alert">
                                        <strong id="err-img"> </strong>
                                    </span>
                                    </div>
                                    <div class="my-2 position-relative">
                                        <div id="video-uploading-percent"
                                             class="position-absolute   h-100   opacity-20 bg-primary">
                                        </div>
                                        <video-uploader
                                            class="col-10 col-lg-12 mx-auto    overflow-x-scroll"
                                            id="video"
                                            label="فایل ویدیو"
                                            for-id="video"
                                            ref="videoUploader"
                                            crop-ratio="{{Helper::$cropsRatio['videos']}}"
                                            link="{{route('video.edit')}}"
                                            type="{{Helper::$docsMap['videos']}}"
                                            required="yes"
                                            preload="{{$video}}"
                                            height="10" mode="edit">

                                        </video-uploader>
                                        <span class=" text-danger text-center small    col-12"
                                              role="alert">
                                        <strong id="err-video"> </strong>
                                    </span>

                                    </div>
                                </div>


                                <div class="col-lg-7 position-relative">
                                    <div
                                        class="border   border-left position-absolute d-none d-lg-block "
                                        style="top:10%;bottom: 10%;left: 0"></div>
                                    <div class="col-10 col-lg-12 mx-auto my-2  border rounded-3">
                                        <dropdown placeholder="دسته بندی" ref-id="category"
                                                  class="  p-2 "
                                                  ref="dropdownCategory"
                                                  pre-select="{{$data->category_id}}"
                                                  newable="{{true}}"
                                                  beforeSelected="{{false}}"
                                                  create-link="{{route('category.create').'?type_id='.Helper::$categoryType['videos']}}"
                                                  data-link="{{route('category.search').'?type_id='.Helper::$categoryType['videos']}}">

                                        </dropdown>
                                        <button
                                            class="btn-secondary input-group-text rounded-3 mx-0  ms-auto"
                                            role='button'
                                            type="button"
                                            id="category-addon"
                                            onclick=" submitWithFiles(event,{'category_id':app.$refs.dropdownCategory.selected.length>0?app.$refs.dropdownCategory.selected[0].id:null})">
                                            ویرایش
                                        </button>
                                    </div>
                                    <div class="col-10 col-lg-12 mx-auto my-2  ">
                                        <div class="my-2 form-outline input-group">

                                            <input id="title" type="text"
                                                   class="  px-4 form-control @error('title') is-invalid @enderror"
                                                   name="title"
                                                   value="{{ $data->title}}" autocomplete="title"
                                                   autofocus>

                                            <label for="title"
                                                   class="col-md-12    form-label  text-md-right">عنوان</label>
                                            <button
                                                class="btn-secondary input-group-text rounded-3  mx-1   "
                                                role='button'
                                                id="title-addon"
                                                onclick=" submitWithFiles(event,{'title':document.getElementById('title').value})">
                                                ویرایش
                                            </button>
                                        </div>
                                        <div class=" text-danger text-start small     " role="alert">
                                            <strong id="err-title"> </strong>
                                        </div>
                                    </div>

                                    <div class="col-10 col-lg-12 mx-auto  my-2  ">
                                        <div class="my-2   form-outline">
                                <textarea id="description" rows="4"
                                          class=" px-4 form-control @error('description') is-invalid @enderror"
                                          name="description"
                                          autocomplete="description" autofocus>{{$data->description}}</textarea>

                                            <label for="description"
                                                   class="col-md-12 col-form-label form-label  text-md-right">
                                                توضیحات
                                            </label>
                                            <button
                                                class="btn-secondary input-group-text rounded-3 ms-auto  "
                                                role='button'
                                                id="description-addon"
                                                onclick=" submitWithFiles(event,{'description':document.getElementById('description').value})">
                                                ویرایش
                                            </button>
                                        </div>
                                        <div class=" text-danger text-start small  col-12   "
                                             role="alert">
                                            <strong id="err-description"> </strong>
                                        </div>
                                    </div>
                                    <div class="col-10 col-lg-12 mx-auto  my-2  ">
                                        <div class="my-2  border  ">
                                            <div class="m-2">
                                                <tag-editor data="{{$data->tags}}"
                                                            classes="@error('tags')'is-invalid'@enderror "
                                                            ref="tagEditor">
                                                </tag-editor>
                                            </div>
                                            <button
                                                class="btn-secondary input-group-text rounded-3  ms-auto  "
                                                role='button'
                                                id="tags-addon"
                                                onclick=" submitWithFiles(event,{'tags':document.getElementById('tags').value})">
                                                ویرایش
                                            </button>
                                        </div>
                                    </div>
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
                addSMSBtnListener(
                    "{{auth()->user()->name}}",
                    "{{auth()->user()->family}}",
                    "{{auth()->user()->phone}}"
                );

//                var myModal = new bootstrap.Modal(document.getElementById('renewModal'), {
//                    keyboard: false
//                });
//                myModal.show();
            });


            function remove(id) {
                document.querySelector('#loading').classList.remove('d-none');
//                event.preventDefault();
                data = {'id': id};
                axios.post("{{route('player.remove')}}", data, {})
                    .then((response) => {
//                        console.log(response);
                            document.querySelector('#loading').classList.add('d-none');
                            if (response.status === 200)
                                window.location = "{{url('panel/player')}}";
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
                data = {'id': "{{$data->id}}", ...data};
                axios.post("{{route('video.edit')}}", data, {
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
