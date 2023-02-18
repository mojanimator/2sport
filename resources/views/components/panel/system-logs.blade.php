@php
    $user=auth()->user();
    $admin= $user && ($user->role=='ad' || $user->role=='go');

@endphp

<div class="w-100 my-2">
    <system-logs
            province-data="{{\App\Models\Province::get()}}"
            county-data="{{\App\Models\County::get()}}"
            agency-data="{{$admin?\App\Models\Agency::select('name','id','province_id','county_id')->get():\App\Models\Agency::select('name','id','province_id','county_id')->where('id',$user->agency_id)->get()}}"

            log-link="{{route('system-logs.search')}}"

    ></system-logs>
</div>