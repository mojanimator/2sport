<?php

namespace App\Http\Controllers;

use App\Models\Ref;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RefController extends Controller
{
    public function search(Request $request = null)
    {


        $user = auth()->user() ?: auth('api')->user();
        $admin = ($user->role == 'ad' || $user->role == 'go') && !str_contains(request()->url(), '/api/'); //send all users for admin just in site
        $refs = Ref::get();
        $settings = Setting::get();
        $ref1_percent = (($settings->where('key', 'ref_1')->first() ?: new Setting())->value ?: 0) / 100;
        $ref2_percent = (($settings->where('key', 'ref_2')->first() ?: new Setting())->value ?: 0) / 100;
        $ref3_percent = (($settings->where('key', 'ref_3')->first() ?: new Setting())->value ?: 0) / 100;
        $ref4_percent = (($settings->where('key', 'ref_4')->first() ?: new Setting())->value ?: 0) / 100;
        $ref5_percent = (($settings->where('key', 'ref_5')->first() ?: new Setting())->value ?: 0) / 100;
        $users = User::select('id', 'name', 'family', 'username')->whereIntegerInRaw('id', $admin ? $refs->unique('inviter_id')->pluck('inviter_id') : [$user->id])->get();


        foreach ($users as $user) {
            $bestan_count = 0;
            $bestan_sum = 0;
            $levels = [
                1 => ['paid' => ['count' => 0, 'sum' => 0], 'unpaid' => ['count' => 0, 'sum' => 0]],
                2 => ['paid' => ['count' => 0, 'sum' => 0], 'unpaid' => ['count' => 0, 'sum' => 0]],
                3 => ['paid' => ['count' => 0, 'sum' => 0], 'unpaid' => ['count' => 0, 'sum' => 0]],
                4 => ['paid' => ['count' => 0, 'sum' => 0], 'unpaid' => ['count' => 0, 'sum' => 0]],
                5 => ['paid' => ['count' => 0, 'sum' => 0], 'unpaid' => ['count' => 0, 'sum' => 0]],
            ];

            foreach ($refs->where('inviter_id', $user->id)->all() as $level1) {

                if ($level1->invited_purchase_type != null && $level1->payed_1_at != null) {
                    $levels[1]['paid']['count']++;
                    $levels[1]['paid']['sum'] += $level1->payed_1;
                } elseif ($level1->invited_purchase_type != null && $level1->payed_1_at == null) {
                    $levels[1]['unpaid']['count']++;
                    $levels[1]['unpaid']['sum'] += round($ref1_percent * ($settings->where('key', \Helper::$refMap[$level1->invited_purchase_type] . '_' . $level1->invited_purchase_months . '_price')->first() ?: new Setting())->value ?: 0);
                }
                foreach ($refs->where('inviter_id', $level1->invited_id)->all() as $level2) {
                    if ($level2->invited_purchase_type != null && $level2->payed_2_at != null) {
                        $levels[2]['paid']['count']++;
                        $levels[2]['paid']['sum'] += $level2->payed_2;
                    } elseif ($level2->invited_purchase_type != null && $level2->payed_2_at == null) {
                        $levels[2]['unpaid']['count']++;
                        $levels[2]['unpaid']['sum'] += round($ref2_percent * ($settings->where('key', \Helper::$refMap[$level2->invited_purchase_type] . '_' . $level1->invited_purchase_months . '_price')->first() ?: new Setting())->value ?: 0);
                    }
                    foreach ($refs->where('inviter_id', $level2->invited_id)->all() as $level3) {
                        if ($level3->invited_purchase_type != null && $level3->payed_3_at != null) {
                            $levels[3]['paid']['count']++;
                            $levels[3]['paid']['sum'] += $level3->payed_3;
                        } elseif ($level3->invited_purchase_type != null && $level3->payed_3_at == null) {
                            $levels[3]['unpaid']['count']++;
                            $levels[3]['unpaid']['sum'] += round($ref3_percent * ($settings->where('key', \Helper::$refMap[$level3->invited_purchase_type] . '_' . $level1->invited_purchase_months . '_price')->first() ?: new Setting())->value ?: 0);
                        }
                        foreach ($refs->where('inviter_id', $level3->invited_id)->all() as $level4) {
                            if ($level4->invited_purchase_type != null && $level4->payed_4_at != null) {
                                $levels[4]['paid']['count']++;
                                $levels[4]['paid']['sum'] += $level4->payed_4;
                            } elseif ($level4->invited_purchase_type != null && $level4->payed_4_at == null) {
                                $levels[4]['unpaid']['count']++;
                                $levels[4]['unpaid']['sum'] += round($ref4_percent * ($settings->where('key', \Helper::$refMap[$level4->invited_purchase_type] . '_' . $level1->invited_purchase_months . '_price')->first() ?: new Setting())->value ?: 0);
                            }
                            foreach ($refs->where('inviter_id', $level4->invited_id)->all() as $level5) {
                                if ($level5->invited_purchase_type != null && $level5->payed_5_at != null) {
                                    $levels[5]['paid']['count']++;
                                    $levels[5]['paid']['sum'] += $level5->payed_5;
                                } elseif ($level5->invited_purchase_type != null && $level5->payed_5_at == null) {
                                    $levels[5]['unpaid']['count']++;
                                    $levels[5]['unpaid']['sum'] += round($ref5_percent * ($settings->where('key', \Helper::$refMap[$level5->invited_purchase_type] . '_' . $level1->invited_purchase_months . '_price')->first() ?: new Setting())->value ?: 0);
                                }
                            }
                        }
                    }
                }
            }

            $user['levels'] = $levels;
            foreach ($levels as $idx => $level) {
                $bestan_count += $level['unpaid']['count'];
                $bestan_sum += $levels[$idx]['unpaid']['sum'];
            }
            $user['bestan'] = ['count' => $bestan_count, 'sum' => $bestan_sum];
        }
        return $users;

    }

    protected function tasvie(Request $request)
    {
        $this->authorize('editItem', [User::class, new Ref(), true]);
        $settings = Setting::get();
        $ref1_percent = (($settings->where('key', 'ref_1')->first() ?: new Setting())->value ?: 0) / 100;
        $ref2_percent = (($settings->where('key', 'ref_2')->first() ?: new Setting())->value ?: 0) / 100;
        $ref3_percent = (($settings->where('key', 'ref_3')->first() ?: new Setting())->value ?: 0) / 100;
        $ref4_percent = (($settings->where('key', 'ref_4')->first() ?: new Setting())->value ?: 0) / 100;
        $ref5_percent = (($settings->where('key', 'ref_5')->first() ?: new Setting())->value ?: 0) / 100;

        $refs = Ref::get();
        $users = User::select('id', 'name', 'family', 'username')->whereIntegerInRaw('id', $request->ids)->get();


        foreach ($users as $user) {

            foreach ($refs->where('inviter_id', $user->id)->all() as $level1) {
                if ($level1->invited_purchase_type != null && $level1->payed_1_at == null) {
                    $level1->payed_1 = round($ref1_percent * ($settings->where('key', \Helper::$refMap[$level1->invited_purchase_type] . '_' . $level1->invited_purchase_months . '_price')->first() ?: new Setting())->value ?: 0);
                    $level1->payed_1_at = Carbon::now();
                    $level1->save();
                }
                foreach ($refs->where('inviter_id', $level1->invited_id)->all() as $level2) {
                    if ($level2->invited_purchase_type != null && $level2->payed_2_at == null) {
                        $level2->payed_2 = round($ref2_percent * ($settings->where('key', \Helper::$refMap[$level2->invited_purchase_type] . '_' . $level1->invited_purchase_months . '_price')->first() ?: new Setting())->value ?: 0);
                        $level2->payed_2_at = Carbon::now();
                        $level2->save();
                    }
                    foreach ($refs->where('inviter_id', $level2->invited_id)->all() as $level3) {
                        if ($level3->invited_purchase_type != null && $level3->payed_3_at == null) {
                            $level3->payed_3 = round($ref3_percent * ($settings->where('key', \Helper::$refMap[$level3->invited_purchase_type] . '_' . $level1->invited_purchase_months . '_price')->first() ?: new Setting())->value ?: 0);
                            $level3->payed_3_at = Carbon::now();
                            $level3->save();
                        }
                        foreach ($refs->where('inviter_id', $level3->invited_id)->all() as $level4) {
                            if ($level4->invited_purchase_type != null && $level4->payed_4_at == null) {
                                $level4->payed_4 = round($ref4_percent * ($settings->where('key', \Helper::$refMap[$level4->invited_purchase_type] . '_' . $level1->invited_purchase_months . '_price')->first() ?: new Setting())->value ?: 0);
                                $level4->payed_4_at = Carbon::now();
                                $level4->save();
                            }
                            foreach ($refs->where('inviter_id', $level4->invited_id)->all() as $level5) {
                                if ($level5->invited_purchase_type != null && $level5->payed_5_at == null) {
                                    $level5->payed_5 = round($ref5_percent * ($settings->where('key', \Helper::$refMap[$level5->invited_purchase_type] . '_' . $level1->invited_purchase_months . '_price')->first() ?: new Setting())->value ?: 0);
                                    $level5->payed_5_at = Carbon::now();
                                    $level5->save();
                                }
                            }
                        }
                    }
                }
            }


        }
        return;

    }

}
