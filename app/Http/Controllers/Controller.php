<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use App\Models\County;
use App\Models\Province;
use App\Models\Shop;
use Helper;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function dataEdited($data, $logType, $msg)
    {
        if (isset($data)) {
            if (auth()->user()->role != 'ad' && auth()->user()->role != 'go' && !str_contains($logType, 'user')) {
                $data->active = false;
            }
            \Telegram::log(\Helper::$TELEGRAM_GROUP_ID, $logType, $data);
            $data->save();
            return redirect()->back()->with('success-alert', $msg);
        }
    }

    protected function settings(Request $request)
    {
        return response()->json([

            'days' => [0 => 'شنبه', 1 => 'یکشنبه', 2 => 'دوشنبه', 3 => 'سه شنبه', 4 => 'چهارشنبه', 5 => 'پنجشنبه', 6 => 'جمعه', 7 => 'هر روز',],
            'provinces' => Province::select('id', 'name')->get(),
            'shops' => Shop::select('id', 'name')->get(),
            'counties' => County::select('id', 'name', 'province_id')->get(),
            'sports' => Sport::select('id', 'name')->get(),
            'app_version' => Helper::$APP_VERSION,
            'app_link' => 'test',
            'doc_types' => Helper::$docsMap,
        ], 200);
    }

    protected function latest(Request $request)
    {
        $paginate = $request->paginate ?: 12;
        $page = $request->page ?: 1;
        $cols = 'id, province_id, county_id,created_at, ';
        $p1 = \App\Models\Player::selectRaw($cols . 'CONCAT(name,\' \', family) as name, "pl" as type')->where('active', true)->where('hidden', false);
        $p2 = \App\Models\Coach::selectRaw($cols . 'CONCAT(name,\' \', family) as name, "co" as type')->where('active', true)->where('hidden', false);
        $p3 = \App\Models\Club::selectRaw($cols . 'name, "cl" as type')->where('active', true)->where('hidden', false);
        $p4 = \App\Models\Shop::selectRaw($cols . 'name, "sh" as type')->where('active', true)->where('hidden', false);
        $p5 = \App\Models\Product::selectRaw('id,price as province_id,discount_price as county_id,created_at,name, "pr" as type')->where('active', true)->where('hidden', false);


        $res = $p5->union($p4)->union($p1)->union($p2)->union($p3)
            ->with('alldocs')
            ->orderByDesc('created_at')
//            ->inRandomOrder()
            ->paginate($paginate, ['*'], 'page', $page);

//        $res->setCollection($res->getCollection()->map(function ($row) {
//
//            $docs = $row->docs->filter(function ($item) use ($row) {
//
//                return $item->docable_type == $row->type;
//            })->values();
//
//            return [
//                'id' => $row->id,
//                'name' => $row->name,
//                'type' => $row->type,
//                'province_id' => $row->province_id,
//                'county_id' => $row->county_id,
//                'docs' => $docs
//            ];
//        }));
        return $res;
    }
}
