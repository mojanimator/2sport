@php
    $user=auth()->user();
    $admin= $user && ($user->role=='ad' || $user->role=='go');
    if(!$admin){
    header("Location: " . URL::to('/panel'), true, 302);
    exit();
    }
@endphp

<div class="w-100 my-2">
    <system-setting setting-data="{{\App\Models\Setting::get()}}"

                    create-link="{{route('system-setting.create')}}"
                    edit-link="{{route('system-setting.edit')}}"
                    remove-link="{{route('system-setting.remove')}}"

    ></system-setting>
</div>