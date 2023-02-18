@extends('layouts.app')
@section('title')
    رسید پرداخت
@stop
@section('content')
    @if($payment)
        @php
            $split=isset($payment->pay_for)? explode('_',$payment->pay_for ):[];
            $type=count($split)>0? (isset(Helper::$labelsMap[$split[0].'s'])?Helper::$labelsMap[$split[0].'s']:(isset(Helper::$labelsMap[$split[0].'es'])?Helper::$labelsMap[$split[0].'es']:'نامشخص')):'نامشخص';
            $month=count($split)>1?$split[1]:0;
            $title=$type&& $month? "$type $month "."ماه":'نامشخص';
            $url= secure_url("panel/$type/edit/$payment->id");
        $status=isset($payment->code)&& $payment->code==0?'success':'danger';
        $message=isset($payment->code) && isset(NextPay::MESSAGES[$payment->code])? NextPay::MESSAGES[$payment->code]:'پرداخت انجام نشد';

            $time = \Morilog\Jalali\Jalalian::fromDateTime($payment->updated_at)->format('%A, %d %B %Y ⏰ H:i');
            $payment->time=$time;
     /*   dd($payment->getAttributes());*/
        @endphp
        <div class="container position-relative ">
            <div class="position-absolute w-100 h-100 top-0 start-0 end-0 bottom-0 opacity-30"
                 style="background: url('{{asset('img/texture.jpg')}}'); background-repeat:repeat;background-size: cover;z-index: 0">
            </div>
            <div class="row  justify-content-center">
                <div class="col-md-6 col-lg-4 col-sm-8 col-ms-10 mx-auto">
                    <div class="card my-5 bg-light">
                        <div class="card-header text-center text-lg text-white font-weight-bold bg-{{$status}}"> {{$message}}

                        </div>

                        <div class="card-body text-primary">
                            <form method="POST">


                                <div class="form-group  ">
                                    <span class="col-12 col-form-label text-md-right">{{__('تاریخ پرداخت')}}</span>

                                    <div class="col-12">
                                        <div class="text-center font-weight-bold">{{ $time}}</div>
                                    </div>

                                    <hr class="my-1"/>
                                </div>
                                <div class="form-group  ">
                                    <span class="col-12 col-form-label text-md-right">{{__('نوع محصول')}}</span>

                                    <div class="col-12">
                                        <div class="text-center font-weight-bold">{{ $title}}</div>
                                    </div>

                                    <hr class="my-1"/>
                                </div>

                                <div class="form-group  ">
                                    <span class="col-12 col-form-label text-md-right">{{__('مبلغ')}}</span>

                                    <div class="col-12">
                                        <div class="text-center font-weight-bold">{{ $payment->amount.' تومان '}}</div>
                                    </div>

                                    <hr class="my-1"/>
                                </div>
                                <div class="form-group  ">
                                    <span class="col-12 col-form-label text-md-right">{{__('کد سفارش')}}</span>

                                    <div class="col-12">
                                        <div class="text-center font-weight-bold">{{ $payment->order_id }}</div>
                                    </div>

                                    <hr class="my-1"/>
                                </div>
                                <div class="form-group  ">
                                    <span class="col-12 col-form-label text-md-right">{{__('کد رهگیری')}}</span>

                                    <div class="col-12">
                                        <div class="text-center font-weight-bold">{{ $payment->Shaparak_Ref_Id }}</div>
                                    </div>

                                    <hr class="my-1"/>
                                </div>


                                <div class="form-group    row">
                                    <div class="col-sm-12 my-2  ">
                                        <a href="{{$url}}" type="submit"
                                           class="btn btn-primary btn-block py-3    font-weight-bold ">
                                            بازگشت به برنامه
                                        </a>

                                    </div>

                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
