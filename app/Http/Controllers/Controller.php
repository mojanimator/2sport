<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    protected function latest(Request $request)
    {
        $paginate = $request->paginate ?: 30;
        $page = $request->page ?: 2;
        $cols = 'id, province_id, county_id,created_at, ';
        $p1 = \App\Models\Player::selectRaw($cols . 'CONCAT(name,\' \', family) as name, "pl" as type');
        $p2 = \App\Models\Coach::selectRaw($cols . 'CONCAT(name,\' \', family) as name, "co" as type');
        $p3 = \App\Models\Club::selectRaw($cols . 'name, "cl" as type');
        $p4 = \App\Models\Shop::selectRaw($cols . 'name, "sh" as type');
        $p5 = \App\Models\Product::selectRaw('id,price as province_id,discount_price as county_id,created_at,name, "pr" as type');


        $res = $p5->union($p4)->union($p1)->union($p2)->union($p3)
            ->with('alldocs')
//            ->orderByDesc('created_at')

            ->inRandomOrder()
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
