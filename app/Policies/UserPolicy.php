<?php

namespace App\Policies;

use App\Models\Agency;
use App\Models\Blog;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Event;
use App\Models\Player;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\Table;
use App\Models\Tournament;
use App\Models\User;
use App\Models\Video;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Auth\Access\HandlesAuthorization;
use PhpParser\Node\Expr\Instanceof_;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {

        if ($user->role == 'go') {
            return true;
        }
    }


    public function ownShop(User $user, $shop_id, $abort)
    {
        if ($user->role == 'go' || $user->role == 'ad')
            return true;
        if (\App\Models\Shop::where('user_id', $user->id)->exists() /*|| \App\Models\Rule::where('user_id', $user->id)->where('shop_id', $shop_id)->exists()*/)
            return true;
        if ($abort)
            return abort(403, 'این فروشگاه متعلق به شما نیست!');
        else return false;
    }

    public function ownProduct(User $user, $product_id, $abort)
    {
        if ($user->role == 'go' || $user->role == 'ad')
            return true;

        $shop_id = Product::findOrNew($product_id)->shop_id;

        if (\App\Models\Shop::where('user_id', $user->id)->exists() /*|| \App\Models\Rule::where('user_id', $user->id)->where('shop_id', $shop_id)->exists()*/)
            return true;
        if ($abort)
            return abort(403, 'این محصول متعلق به شما نیست!');
        else return false;
    }


    public function editItem(User $user, $item, $abort, $id = null)
    {

        if ($user->role == 'go') {
            return true;
        }

        switch (true) {
            case  $item instanceof User :
                if ($user->id == $item->id)
                    return true;
                if ($user->role == 'ad' && in_array($item->role, ['us', 'bl', 'aa',]))
                    return true;
                if ($user->role == 'ag' && in_array($item->role, ['aa', 'us']) && $user->agency_id == $item->agency_id)
                    return true;
                if ($user->role == 'aa' && in_array($item->role, ['us']) && $user->agency_id == $item->agency_id)
                    return true;
                break;
            case $item instanceof Player:
            case $item instanceof Coach:
            case $item instanceof Club:
            case $item instanceof Shop:
                if ($item && isset($item->user_id) && $item->user_id == $user->id)
                    return true;
                $u = $item->user;
                if ($user->role == 'ad' || (in_array($user->role, ['ag', 'aa']) && $u && $user->agency_id == $u->agency_id))
                    return true;
                break;
            case $item instanceof Product:
                $shop = Shop::findOrNew(Product::firstOrNew(['id' => $item->id])->shop_id);
                if ($shop && isset($shop->user_id) && $shop->user_id == $user->id)
                    return true;
                $u = $shop->user;
                if ($user->role == 'ad' || (in_array($user->role, ['ag', 'aa']) && $u && $user->agency_id == $u->agency_id))
                    return true;
                break;
            case $item instanceof Video:
            case $item instanceof Blog:
            case $item instanceof Table:
            case $item instanceof Event:
            case $item instanceof Tournament:
                if (in_array($user->role, ['ad', 'bl']))
                    return true;
                break;
            case $item instanceof Setting:
                if (in_array($user->role, ['ad']))
                    return true;
                break;
        }


        if ($abort)
            return abort(403, 'درخواست غیر مجاز است');
        else return false;
    }

    public function createItem(User $user, $item, $abort, $option = null)
    {


        if ($user->role == 'go')
            return true;

        switch ($item) {
            case User::class  :
                if ($user->active && $user->role == 'ad' && isset($option->role) && in_array($option->role, ['us', 'bl', 'aa']))
                    return true;
                if ($user->active && $user->role == 'ag' && isset($option->role) && in_array($option->role, ['us', 'aa']))
                    return true;
                if ($user->active && $user->role == 'aa' && isset($option->role) && in_array($option->role, ['us']))
                    return true;
                break;
            case Video::class:
            case Blog::class:
            case Table::class:
            case  Event::class:
            case Tournament::class:
                if ($user->active && in_array($user->role, ['bl', 'ad']))
                    return true;
                break;
            case Player::class:
            case Coach::class:
            case Club::class:
            case Shop::class:
            case Product::class:
                if ($user->active && in_array($user->role, ['us', 'ad', 'aa', 'ag']))
                    return true;
                break;
            case Setting::class:
                if ($user->active && in_array($user->role, ['ad']))
                    return true;
                break;


        }


        if ($abort)
            return abort(403, 'مجاز نیستید!');
        else return false;
    }


    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }
}
