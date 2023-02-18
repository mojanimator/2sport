@extends('layouts.app')
@section('title')
    قوانین و حریم خصوصی
@stop


@section('content')

    @php
        function beautify($str){
$filters=[  'شماره شبا','باشگاه ورزشی','شماره تماس','دبل اسپورت','فروشگاه','ایمیل','شماره کارت','تماس با ما'];


foreach ($filters as $item) {
  if (str_contains($str,$item))
    $str= str_replace( $item,"<strong>$item</strong>",$str);

  }
        return $str;
        }

            $txt=[
           "دبل اسپورت حریم شخصی کاربران را محترم شمرده و همانند حریم شخصی خود از آن حفاظت می نماید"
        ,
        "تمامی اطلاعات عمومی کاربران به علاوه شماره تماس، آدرس فروشگاه یا باشگاه ورزشی صرفا جهت نمایش و معرفی شما درون سایت و اپلیکیشن استفاده می شود"
,
"ثبت اطلاعاتی مانند شماره کارت یا شماره شبا کاملا اختیاری است و تنها به منظور واریز کارمزد بازاریابی دریافت شده و نزد ما محفوظ می ماند."
,
"شماره تماس و ایمیل جهت ورود کاربران استفاده می شود. در صورت مشاهده مغایرت یا گزارش سواستفاده، دبل اسپورت می تواند جهت احراز هویت یا غیر فعال سازی حساب کاربری اقدام کند."
         ,
         "در صورت وجود هر گونه مشکل یا داشتن سوال یا ارسال انتقادات و پیشنهادات، از لینک های تماس با ما که در سایت و اپلیکیشن وجود دارند استفاده نمایید."
            ];
    @endphp
    <div class="container position-relative  ">
        <div class="position-absolute w-100 h-100 top-0 start-0 end-0 bottom-0 opacity-30"
             style="background: url('{{asset('img/texture.jpg')}}'); background-repeat:repeat;background-size: cover;z-index: 0;height: 100vh">
        </div>
        <div class="row  justify-content-center">
            <div class="col-md-10 col-lg-6 col-sm-10 col-ms-10 mx-auto">
                <div class="card my-5 bg-light">
                    <div class="card-header text-center text-lg text-white font-weight-bold bg-primary">
                        حریم خصوصی و قوانین

                    </div>

                    <div class="card-body text-primary">
                        <div class="alert    bg-opacity-25 d-flex flex-column  p-2 py-4">
                            <div class="d-flex align-items-center">
                                <x-icons src="info"
                                         fill="#fff"
                                         style=" "
                                         class="m-1 p-2 ">
                                </x-icons>
                                <div>{!!  beautify($txt[0]) !!}</div>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center">
                                <x-icons src="info"
                                         fill="#fff"
                                         style=" "
                                         class="m-1 p-2 ">
                                </x-icons>
                                <div>{!! beautify($txt[1]) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex align-items-center">
                                <x-icons src="info"
                                         fill="#fff"
                                         style=" "
                                         class="m-1 p-2 ">
                                </x-icons>
                                <div>

                                    {!! beautify($txt[2]) !!}
                                </div>
                            </div>

                            <div class="text-danger rounded-3 border-danger border d-flex align-items-center p-2 my-4">
                                <x-icons src="warning"
                                         fill="#fff"
                                         style=" "
                                         class="m-1 p-2 ">
                                </x-icons>
                                <div>{!! beautify($txt[3]) !!}</div>
                            </div>

                            <div class="d-flex align-items-center">
                                <x-icons src="info"
                                         fill="#fff"
                                         style=" "
                                         class="m-1 p-2 ">
                                </x-icons>
                                <div>{!! beautify($txt[4]) !!}     </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
