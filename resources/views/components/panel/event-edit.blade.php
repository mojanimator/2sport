@php
    $event=\App\Models\Event::find($param);

@endphp
@if(!$event)
    <div class="text-center font-weight-bold text-danger mt-5">رویداد یافت نشد</div>
@else
    @cannot('editItem',[\App\Models\User::class,$event,false ])
        @php
            header("Location: " . URL::to('/panel/events'), true, 302);
        exit();
        @endphp
    @endcannot
    {{--{{json_encode( $images)}}--}}


    @php

        $date=Morilog\Jalali\Jalalian::fromDateTime($event->time,new DateTimeZone('Asia/Tehran'));
    @endphp

    <div class="  my-3 ">
        <div class="row mx-auto justify-content-center">
            <div class="col-md-10 mx-auto  ">
                <div class="card bg-light">
                    <div class="  card-header   text-white bg-primary  d-flex justify-content-around ">
                        <label for="delete-input"
                               class="  ">{{$event->title }}</label>
                        <div class="    ">
                            <button class="btn btn-sm btn-danger rounded  ms-2 font-weight-bold" type="button"
                                    id="delete-addon"
                                    onclick=" window.showDialog('confirm','از حذف اطمینان دارید؟',()=>remove,{{$event->id}})">
                                حذف
                            </button>
                        </div>
                    </div>
                    <div class="card-body  ">


                        <form id="form-create" method="POST" action="{{ route('event.edit') }}"
                              class="text-right  row">
                            @csrf


                            <span class=" text-danger text-center small row  col-12" role="alert">
                                        <strong id="err-img"> </strong>
                                    </span>

                            <div class="col-md-10 mx-auto   ">
                                <div class="m-2 form-outline">

                                    <input id="title" type="text"
                                           class="  px-4 form-control @error('title') is-invalid @enderror"
                                           name="title"
                                           value="{{ $event->title }}" autocomplete="title" autofocus>
                                    <label for="title"
                                           class="col-md-12    form-label  text-md-right">عنوان رویداد</label>
                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-title"> </strong>
                                </div>
                            </div>


                            <div class="col-md-11 mx-auto  row   ">
                                <div class="col-sm-6      ">
                                    <div class="m-2 form-outline">
                                        <input id="team1" type="text"
                                               class="  px-4 form-control @error('team1') is-invalid @enderror"
                                               name="team1"
                                               value="{{ $event->team1 }}" autocomplete="team1" autofocus>
                                        <label for="team1"
                                               class="col-md-12 col-form-label form-label  text-md-right">
                                            آیتم (تیم) اول
                                        </label>

                                    </div>
                                    <div class=" text-danger text-start small     " role="alert">
                                        <strong id="err-team1"> </strong>
                                    </div>
                                </div>

                                <div class="col-sm-6      ">
                                    <div class="m-2 form-outline">
                                        <input id="score1" type="number"
                                               class="  px-4 form-control @error('score1') is-invalid @enderror"
                                               name="score1"
                                               value="{{ $event->score1  }}" autocomplete="score1" autofocus>
                                        <label for="score1"
                                               class="col-md-12 col-form-label form-label  text-md-right">
                                            امتیاز اول
                                        </label>

                                    </div>
                                    <div class=" text-danger text-start small     " role="alert">
                                        <strong id="err-score1"> </strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-11 mx-auto  row   ">
                                <div class="col-sm-6      ">
                                    <div class="m-2 form-outline">
                                        <input id="team2" type="text"
                                               class="  px-4 form-control @error('team2') is-invalid @enderror"
                                               name="team2"
                                               value="{{ $event->team2  }}" autocomplete="team2" autofocus>
                                        <label for="team2"
                                               class="col-md-12 col-form-label form-label  text-md-right">
                                            آیتم (تیم) دوم
                                        </label>

                                    </div>
                                    <div class=" text-danger text-start small     " role="alert">
                                        <strong id="err-team2"> </strong>
                                    </div>
                                </div>
                                <div class="col-sm-6      ">
                                    <div class="m-2 form-outline">
                                        <input id="score2" type="number"
                                               class="  px-4 form-control @error('score2') is-invalid @enderror"
                                               name="score2"
                                               value="{{ $event->score2 }}" autocomplete="score2" autofocus>
                                        <label for="score2"
                                               class="col-md-12 col-form-label form-label  text-md-right">
                                            امتیاز دوم
                                        </label>

                                    </div>
                                    <div class=" text-danger text-start small     " role="alert">
                                        <strong id="err-score2"> </strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-10 mx-auto row    ">
                                <label class="  font-weight-bold me-4">{{'وضعیت'}}</label>
                                <div class="  my-2  shadow-0 p-0">

                                    <input type="radio" class="form-check-input  " name="status"
                                           value="{{null}}"
                                           id="{{'status-0'}}"
                                           autocomplete="off" {{$event->status==null?'checked':''}}/>
                                    <label class="form-check-label me-4" for="{{'status-0'}}">{{'خالی'}}</label>
                                    @foreach(Helper::$eventStatus as $idx=>$status)
                                        <input type="radio" class="form-check-input  " name="status"
                                               value="{{$status}}"
                                               id="{{'status-'.($idx+1)}}"
                                               autocomplete="off" {{$event->status==$status?'checked':''}}/>
                                        <label class="form-check-label me-3 small"
                                               for="{{'status-'.($idx+1)}}">{{$status}}</label>
                                    @endforeach

                                </div>
                            </div>
                            <div class="col-md-10 mx-auto     ">
                                <div class="  row   ms-auto    ">
                                    <div class="my-2 mx-1  form-outline col-2   ">
                                        <input id="mm" type="number"
                                               class="  px-1 form-control @error('mm') is-invalid @enderror"
                                               value="{{  $date->getMinute() }}"
                                               name="mm"
                                               autocomplete="mm">
                                        <label for="mm"
                                               class="col-md-12  form-label text-md-right">دقیقه</label>


                                    </div>
                                    <div class="my-2 mx-1  form-outline col-2     ">
                                        <input id="hh" type="number"
                                               class="  px-1 form-control @error('hh') is-invalid @enderror"
                                               value="{{$date->getHour()}}"
                                               name="hh"
                                               autocomplete="hh">
                                        <label for="hh"
                                               class="col-md-12  form-label text-md-right">ساعت</label>


                                    </div>


                                    <div class="my-2 mx-1  form-outline    col-2">
                                        <input id="d" type="number"
                                               class="  px-1 form-control @error('d') is-invalid @enderror"
                                               value="{{$date->getDay()}}"
                                               name="d"
                                               autocomplete="d">
                                        <label for="d"
                                               class="col-md-12  form-label text-md-right">روز</label>


                                    </div>

                                    <div class="my-2 mx-1  form-outline col-2 ">
                                        <input id="m" type="number"
                                               class="  px-1 form-control @error('m') is-invalid @enderror"
                                               value="{{$date->getMonth()}}"
                                               name="m"
                                               autocomplete="m">
                                        <label for="m"
                                               class="col-md-12  form-label text-md-right">ماه </label>


                                    </div>

                                    <div class="my-2 mx-1  form-outline  col-2">
                                        <input id="y" type="number"
                                               class="  px-1 form-control @error('y') is-invalid @enderror"
                                               value="{{  $date->getYear() }}"
                                               name="y"
                                               autocomplete="y">
                                        <label for="y"
                                               class="col-md-12  form-label text-md-right">سال </label>


                                    </div>

                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-mm"> </strong>
                                    </div>
                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-hh"> </strong>
                                    </div>
                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-d"> </strong>
                                    </div>
                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-m"> </strong>
                                    </div>
                                    <div class=" text-danger text-start small  col-12   " role="alert">
                                        <strong id="err-y"> </strong>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-10   mx-auto my-2 ">

                                {{--<label for="sport-input"--}}
                                {{--class="col-12 col-form-label text-right">رشته ورزشی</label>--}}
                                <select id="sport" name="sport"
                                        class="px-4 form-control{{ $errors->has('sport')  ? ' is-invalid' : '' }}">
                                    <option value="{{null}}">انتخاب رشته ورزشی</option>
                                    @foreach(\App\Models\Sport::get() as $s)
                                        <option value="{{$s->id}}"
                                                {{ $event->sport_id==$s->id? ' selected ':''}} >{{$s->name}}</option>

                                    @endforeach
                                </select>

                                <div class=" text-danger text-start small  col-12   " role="alert">
                                    <strong id="err-sport"> </strong>
                                </div>

                            </div>

                            <div class="col-md-10 mx-auto     ">
                                <div class="m-2 form-outline">
                                    <input id="source" type="text"
                                           class="  px-4 form-control @error('source') is-invalid @enderror"
                                           name="source"
                                           value="{{ $event->source }}" autocomplete="source" autofocus>
                                    <label for="source"
                                           class="col-md-12 col-form-label form-label  text-md-right">
                                        منبع پخش (اختیاری)
                                    </label>

                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-source"> </strong>
                                </div>
                            </div>
                            <div class="col-md-10 mx-auto     ">
                                <div class="m-2 form-outline">
                                    <input id="link" type="text"
                                           class="  px-4 form-control @error('link') is-invalid @enderror"
                                           name="link"
                                           value="{{ $event->link}}" autocomplete="link" autofocus>
                                    <label for="link"
                                           class="col-md-12 col-form-label form-label  text-md-right">
                                        لینک منبع پخش (اختیاری)
                                    </label>

                                </div>
                                <div class=" text-danger text-start small     " role="alert">
                                    <strong id="err-team2"> </strong>
                                </div>
                            </div>

                            <div class="col-md-10 mx-auto    ">
                                <div class="m-2 form-outline">
                                <textarea id="details" rows="4"
                                          class="  px-4 form-control @error('details') is-invalid @enderror"
                                          name="details"
                                          autocomplete="details" autofocus>{{ $event->details }}</textarea>

                                    <label for="details"
                                           class="col-md-12 col-form-label form-label  text-md-right">
                                        جزییات (اختیاری)
                                    </label>

                                </div>
                                <div class=" text-danger text-start small  col-12   " role="alert">
                                    <strong id="err-details"> </strong>
                                </div>
                            </div>
                            <div class="form-group   mb-0">
                                <div class="col-md-12  mt-2">
                                    <button onclick=" submitWithFiles(event)" type="button"
                                            class="btn hoverable-indigo btn-blue text-white btn-block font-weight-bold py-3">
                                        ویرایش
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


            function remove(id) {
                document.querySelector('#loading').classList.remove('d-none');

                data = {'id': id};
                axios.post("{{route('event.remove')}}", data, {})
                    .then((response) => {
//                        console.log(response);
                            document.querySelector('#loading').classList.add('d-none');
                            if (response.status === 200)
                                window.location = "{{url('panel/event')}}";
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

            function submitWithFiles(event) {
                document.querySelector('#loading').classList.remove('d-none');
                event.preventDefault();

                let fd = new FormData();
                let data = document.querySelector('#form-create').querySelectorAll('input, textarea, select');
                for (let i in data) {
                    if (data[i].type === 'radio' && data[i].checked === false)
                        continue;
                    else
                        fd.append(data[i].name, data[i].value);

                }

                fd.append('id', "{{$event->id}}");


                axios.post("{{route('event.edit')}}", fd, {
                    onUploadProgress: function (progressEvent) {
                        let percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
//                        console.log(percentCompleted);
                    }
                })

                    .then((response) => {
//                        console.log(response);
                            document.querySelector('#loading').classList.add('d-none');

                            if (response.status === 200)
                                window.location.reload();
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