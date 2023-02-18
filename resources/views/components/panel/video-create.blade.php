@cannot('createItem',[\App\Models\User::class,\App\Models\Video::class,false ])
    @php
        header("Location: " . URL::to('/panel'), true, 302);
    exit();
    @endphp
@endcannot

<div class="  my-3 container">
    <div class="row mx-auto justify-content-center">
        <div class="col-md-10 col-12 mx-auto  ">
            <div class="card bg-light">
                <h5 class="card-header text-center text-white bg-primary">ثبت ویدیو</h5>

                <div class="card-body  ">


                    <form id="form-create" method="POST" action="{{ route('video.create') }}" class="text-right  row">
                        @csrf

                        <div class="row flex-row-reverse">
                            <div class="col-lg-5 ">
                                <div class="my-2">
                                    <image-uploader
                                        class="  col-10 col-lg-12  mx-auto     overflow-x-scroll" id="img"
                                        label="پوستر ویدیو"
                                        for-id="img" ref="imageUploader"
                                        crop-ratio="{{Helper::$cropsRatio['videos']}}"
                                        link="null"
                                        preload="null"
                                        height="10" mode="create">
                                    </image-uploader>
                                    <span class=" text-danger text-center small    col-12" role="alert">
                                        <strong id="err-img"> </strong>
                                    </span>
                                </div>
                                <div class="my-2 position-relative">
                                    <div id="video-uploading-percent"
                                         class="position-absolute   h-100   opacity-20 bg-primary">
                                    </div>
                                    <video-uploader
                                        class="col-10 col-lg-12 mx-auto    overflow-x-scroll" id="video"
                                        label="فایل ویدیو"
                                        for-id="video" ref="videoUploader"
                                        crop-ratio="{{Helper::$cropsRatio['videos']}}"
                                        link=""
                                        height="10" mode="create">

                                    </video-uploader>
                                    <span class=" text-danger text-center small    col-12" role="alert">
                                        <strong id="err-video"> </strong>
                                    </span>

                                </div>
                            </div>


                            <div class="col-lg-7 position-relative">
                                <div class="border   border-left position-absolute d-none d-lg-block "
                                     style="top:10%;bottom: 10%;left: 0"></div>
                                <div class="col-10 col-lg-12 mx-auto my-2  ">
                                    <dropdown placeholder="دسته بندی" ref-id="category"

                                              class="  p-2 "
                                              ref="dropdownCategory"
                                              newable="{{true}}"
                                              beforeSelected="{{false}}"
                                              create-link="{{route('category.create').'?type_id='.Helper::$categoryType['videos']}}"
                                              data-link="{{route('category.search').'?type_id='.Helper::$categoryType['videos']}}">

                                    </dropdown>
                                </div>
                                <div class="col-10 col-lg-12 mx-auto my-2  ">
                                    <div class="m-2 form-outline">

                                        <input id="title" type="text"
                                               class="  px-4 form-control @error('title') is-invalid @enderror"
                                               name="title"
                                               value="{{ old('title') }}" autocomplete="title" autofocus>
                                        <label for="title"
                                               class="col-md-12    form-label  text-md-right">عنوان</label>
                                    </div>
                                    <div class=" text-danger text-start small     " role="alert">
                                        <strong id="err-title"> </strong>
                                    </div>
                                </div>

                                <div class="col-10 col-lg-12 mx-auto  my-2  ">
                                    <div class="m-2   form-outline">
                                <textarea id="description" rows="4"
                                          class=" px-4 form-control @error('description') is-invalid @enderror"
                                          name="description"
                                          autocomplete="description" autofocus>{{ old('description') }}</textarea>

                                        <label for="description"
                                               class="col-md-12 col-form-label form-label  text-md-right">
                                            توضیحات
                                        </label>

                                    </div>
                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-description"> </strong>
                                    </div>
                                </div>
                                <div class="col-10 col-lg-12 mx-auto  my-2  ">
                                    <div class="m-2   ">
                                        <tag-editor data="{{old('tags')}}" classes="@error('tags')'is-invalid'@enderror"
                                                    ref="tagEditor">
                                        </tag-editor>
                                    </div>
                                </div>
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
                if (data[i].id === 'img') {
                    let tmp = app.$refs.imageUploader.getCroppedData();
                    if (tmp != null)
                        fd.append(data[i].name, tmp);
                } else if (data[i].id === 'video-file' && extra['upload_pending'] != true) {
                    if (data[i].files[0] !== undefined)
                        fd.append('video', data[i].files[0]);
                } else
                    fd.append(data[i].name, data[i].value);
            }
            if (app.$refs.dropdownCategory.selected.length > 0)
                fd.append('category_id', app.$refs.dropdownCategory.selected[0].id);

            fd.append('duration', app.$refs.videoUploader.getDuration());
            for (let i in extra)
                fd.append(i, extra[i]);

            axios.post("{{route('video.create')}}", fd, {
                onUploadProgress: function (progressEvent) {
                    var percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    let percentEl = document.querySelector('#percent');
                    let videoPercentEl = document.querySelector('#video-uploading-percent');
                    if (percentCompleted > 0) {
                        percentEl.innerHTML = percentCompleted;
                        videoPercentEl.style.width = percentCompleted + '%';
                        if (percentCompleted >= 100)
                            videoPercentEl.style.width = '0';
                    }
                }
            })

                .then((response) => {
                        // console.log(response);
                        document.querySelector('#loading').classList.add('d-none');

                        if (response.status == 200)
                            if (response.data.resume == true)
                                submitWithFiles(event);
                            else
                                window.location = "{{url('panel/videos')}}";

                    }
                ).catch((error) => {
                document.querySelector('#loading').classList.add('d-none');
                console.log(error.response.data.errors);
                let errors = '';
                invalidInputs(error.response.data.errors);
                if (error.response && error.response.status === 422)
                    for (let idx in error.response.data.errors)
                        errors += '' + error.response.data.errors[idx] + '<br>';
                else {
                    errors = error;
                }
                window.showToast('danger', errors);
            });
        }


        //        });
    </script>
@endpush
