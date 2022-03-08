@php
    $user=auth()->user();
    $admin= $user && ($user->role=='ad' || $user->role=='go');

@endphp

<div class="w-100 my-2">
    <coupons coupon-data="{{$admin?\App\Models\Coupon::get():\App\Models\Coupon::where(function ($query) {
    return $query->orWhere('expires_at','>',\Illuminate\Support\Carbon::now())->orWhereNull('expires_at' );})
    ->where(function($query) use ($user){
  return $query->orWhere('user_id',$user->id)->orWhereNull('user_id');})
  ->where(function ($query) use ($user){
   return $query->whereNotIn('id',\App\Models\Payment::where('user_id',$user->id)->whereNotNull('coupon_id')->pluck('coupon_id'));
    })->get()}}"
             admin="{{$admin}}"
             users-data="{{$admin?\App\Models\User::select('id','name','family')->get():null}}"
             create-link="{{route('coupon.create')}}"
             remove-link="{{route('coupon.remove')}}"

    ></coupons>
</div>