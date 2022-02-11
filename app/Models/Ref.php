<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Ref extends Model
{
    use HasFactory;

    protected $appends = [];
    public $timestamps = true;
    protected $table = 'refs';
    protected $fillable = [
        'id', 'invited_id', 'inviter_id', 'invited_purchase_type', 'invited_purchase_months', 'payed_1_at', 'payed_1', 'payed_2_at', 'payed_2', 'payed_3_at', 'payed_3', 'payed_4_at', 'payed_4', 'payed_5_at', 'payed_5', 'created_at', 'updated_at'
    ];


    public static function refs()
    {
        $user = auth()->user();
        $admin = $user->role == 'ad' || $user->role == 'go';
        $refs = Ref::get();
        $users = User::select('id', 'name', 'family', 'username')->whereIntegerInRaw('id', $admin ? $refs->pluck('inviter_id')->unique() : [$user->id])->get();


        $unpaid = 0;
        $count = 0;
        foreach ($users as $user) {

            foreach ($refs->where('inviter_id', $user->id)->all() as $level1) {
                if ($level1->invited_purchase_type != null)
                    $count++;
                if ($level1->invited_purchase_type != null && $level1->payed_1_at == null) {
                    $unpaid++;
                }
                foreach ($refs->where('inviter_id', $level1->invited_id)->all() as $level2) {
                    if ($level2->invited_purchase_type != null)
                        $count++;
                    if ($level2->invited_purchase_type != null && $level2->payed_2_at == null) {
                        $unpaid++;
                    }
                    foreach ($refs->where('inviter_id', $level2->invited_id)->all() as $level3) {
                        if ($level3->invited_purchase_type != null)
                            $count++;
                        if ($level3->invited_purchase_type != null && $level3->payed_3_at == null) {
                            $unpaid++;
                        }
                        foreach ($refs->where('inviter_id', $level3->invited_id)->all() as $level4) {
                            if ($level4->invited_purchase_type != null)
                                $count++;
                            if ($level4->invited_purchase_type != null && $level4->payed_4_at == null) {
                                $unpaid++;
                            }
                            foreach ($refs->where('inviter_id', $level4->invited_id)->all() as $level5) {
                                if ($level5->invited_purchase_type != null)
                                    $count++;
                                if ($level5->invited_purchase_type != null && $level5->payed_5_at == null) {
                                    $unpaid++;

                                }
                            }
                        }
                    }
                }
            }


        }
        return ['unpaid' => $unpaid, 'count' => $count];

    }
}
