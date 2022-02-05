@php
    $user=auth()->user();
    $admin= $user && ($user->role=='ad' || $user->role=='go');


@endphp
<div class="w-100 my-2">
    <referral
            admin="{{$admin}}"
            ref-link="{{url('').'?ref='.base64_encode($user->id)}}"
            tasvie-link="{{route('ref.tasvie')}}"
            search-link="{{route('ref.search')}}"


    ></referral>
</div>