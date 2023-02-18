<?php

namespace App\Models;


//use App\Notifications\MyResetPassword;
use App\Notifications\MyResetPassword;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;


//use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements /*Auditable,*/
    CanResetPassword
{

    use \Illuminate\Auth\Passwords\CanResetPassword;
    use Notifiable;
    use HasApiTokens;

    // use \OwenIt\Auditing\Auditable;

//    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'users';
    protected $fillable = [
        'id', 'name', 'family', 'is_man', 'username', 'email', 'sheba', 'cart', 'email_verified', 'phone_verified', 'phone', 'password', 'score',
        'expires_at', 'created_at', 'updated_at', 'active', 'role', 'ref_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

        'id' => 'string',
        'is_man' => 'boolean',
        'active' => 'boolean',
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',


    ];

    public static function makeRefCode()
    {
        $original = implode("", array_merge(range(0, 9), range('a', 'z')));
        function randomString($length = 5, $original)
        {
            return substr(str_shuffle($original), 0, $length);
        }

        $ref = randomString(5, $original);
        for ($i = 5; $i <= 10; $i++) {
            for ($j = 0; $j < 100; $j++) {
                if (User::where('ref_code', $ref)->exists())
                    $ref = randomString($i, $original);
                else
                    break;
            }
            if ($j < 100)
                break;
        }
        return $ref;
    }

    public function setReferral($re = null)
    {
        $ref = $re ? $re : session('ref');

        $u = User::where('ref_code', $ref)->first();
        $id = $u ? $u->id : null;
        if ($ref && $id) {
            $r = Ref::where('invited_id', $this->id)->first();
            if ($r && $r->invited_purchase_type == null) {
                $r->inviter_id = $id;
                $r->save();
            }
            if (!$r) {
                Ref::create(['inviter_id' => $id, 'invited_id' => $this->id,]);
            }
            $this->agency_id = $u->agency_id;
        }

    }

    public function refs()
    {
        $unpaid = 0;

        $ids = [['level' => 1, 'ids' => []], ['level' => 2, 'ids' => []], ['level' => 3, 'ids' => []], ['level' => 4, 'ids' => []], ['level' => 5, 'ids' => []],];
        foreach (Ref::where('inviter_id', $this->id)->get() as $item) {
            $ids[0]['ids'][] = $item->invited_id;
            if ($item->payed_at == null)
                $unpaid++;
            foreach (Ref::where('inviter_id', $item)->get() as $item2) {
                $ids[1]['ids'][] = $item2->invited_id;
                if ($item->payed_at == null)
                    $unpaid++;
                foreach (Ref::where('inviter_id', $item2)->get() as $item3) {
                    $ids[2]['ids'][] = $item3->invited_id;
                    if ($item->payed_at == null)
                        $unpaid++;
                    foreach (Ref::where('inviter_id', $item3)->get() as $item4) {
                        $ids[3]['ids'][] = $item4->invited_id;
                        if ($item->payed_at == null)
                            $unpaid++;
                        foreach (Ref::where('inviter_id', $item4)->get() as $item5) {
                            $ids[4]['ids'][] = $item5->invited_id;
                            if ($item->payed_at == null)
                                $unpaid++;

                        }
                    }
                }
            }
        }

        $c = 0;
        foreach ($ids as $item) {
            $c += count($item['ids']);
        }
        $ids['count'] = $c;


        $ids['count_unpaid'] = $unpaid;

        return $ids;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MyResetPassword($token));
    }


    public function findForPassport($phone)
    {

        return User::where('phone', $phone)->first();


    }

    public function validateForPassportPasswordGrant($smsCode)
    {
        $sms = new \SMS();
        $res = $sms->verifyActivationSMS($this->phone, $smsCode);
        if ($res)
            $sms->deleteActivationSMS($this->phone);
        return $res;
    }

    //get all created players,coaches,clubs,shops
    public
    function getOwnes()
    {

//        $provinces = Province::get(['id', 'name',])->toArray();
//        $county = County::get(['id', 'name', 'province_id'])->toArray();

        $user_id = auth()->user()->id;

        Player::where('user_id', $user_id)->get(['name']);
    }

    public
    function getProducts($min = false)
    {
        if ($min) {
            if ($this->role == 'us')
                return Player::where('user_id', $this->id)->select('id', 'name', 'family', 'province_id', 'county_id', 'sport_id')->with('profile')->with('province')->with('county')->with('sport')->get();
            if ($this->role == 'ad' || $this->role == 'go')
                return Player::select('id', 'name', 'family', 'province_id', 'county_id', 'sport_id')->with('profile')->with('province')->with('county')->with('sport')->get();
        } else {
            if ($this->role == 'us')
                return Player::where('user_id', $this->id)->with('docs')->with('province')->with('county')->with('sport')->get();
            if ($this->role == 'ad' || $this->role == 'go')
                return Player::with('docs')->with('province')->with('county')->with('sport')->get();
        }

    }

    public function isAdmin()
    {
        return $this->role == 'go' || $this->role == 'ad';
    }

    public function isAgency()
    {
        return $this->role == 'aa' || $this->role == 'ag';
    }
}
