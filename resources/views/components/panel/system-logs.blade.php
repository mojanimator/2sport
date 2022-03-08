@php
    $user=auth()->user();
    $admin= $user && ($user->role=='ad' || $user->role=='go');
    if(!$admin){
    header("Location: " . URL::to('/panel'), true, 302);
    exit();
    }
@endphp

<div class="w-100 my-2">
    <system-logs
            province-data="{{\App\Models\Province::get()}}"
            county-data="{{\App\Models\County::get()}}"

            log-link="{{route('system-logs.search')}}"

    ></system-logs>
</div>