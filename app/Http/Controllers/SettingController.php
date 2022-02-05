<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    protected function create(Request $request)
    {
        $request->validate([

            'name' => 'required|max:255',
            'key' => 'required|max:100|regex:/^[A-Za-z]+[A-Za-z0-9_]{1,100}$/|unique:settings,key',
            'value' => 'required|max:100',
        ], [
            'name.required' => 'نام فارسی ضروری است',
            'name.max' => 'طول نام فارسی حداکثر 255 باشد',
            'key.required' => 'شناسه ضروری است',
            'key.max' => 'حداکثر طول کلید 100 باشد',
            'key.regex' => 'کلید حداقل دو حرف و با حروف انگلیسی شروع شود و می تواند شامل عدد و _  باشد',
            'key.unique' => 'کلید تکراری است',

            'value.required' => 'مقدار ضروری است',
            'value.max' => 'حداکثر طول مقدار 100 باشد',

        ]);
        $this->authorize('ownItem', [User::class, new Setting(), true]);
        Setting::create([
            'name' => $request->name,
            'key' => $request->key,
            'value' => $request->value
        ]);
        return Setting::get();
    }

    protected function edit(Request $request)
    {
        $request->validate([
            'id' => 'required|' . Rule::in(Setting::pluck('id')),
            'value' => 'required|max:100',
        ], [
            'id.required' => 'شناسه ضروری است',
            'id.in' => 'شناسه نامعتبر است',
            'value.required' => 'مقدار ضروری است',
            'value.max' => 'حداکثر طول مقدار 100 باشد',

        ]);
        $setting = Setting::where('id', $request->id)->first();
        $this->authorize('ownItem', [User::class, $setting, true]);
        $setting->value = $request->value;
        $setting->save();
        return Setting::all();
    }

    protected function remove(Request $request)
    {
        $request->validate([
            'id' => 'required|' . Rule::in(Setting::pluck('id')),
        ], [
            'id.required' => 'شناسه ضروری است',
            'id.in' => 'شناسه نامعتبر است',

        ]);

        $this->authorize('ownItem', [User::class, new Setting(), true]);
        Setting::where('id', $request->id)->delete();
        return Setting::all();
    }
}
