<?php

namespace App\Policies;

use App\Models\Blog;
use App\Models\Club;
use App\Models\Coach;
use App\Models\Player;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Shop;
use App\Models\Table;
use App\Models\User;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Auth\Access\HandlesAuthorization;

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


    public function ownItem(User $user, $item, $abort, $id = null)
    {
        if ($id) {
            switch ($item) {
                case 'player':
                    $item = Player::find($id);
                    break;
                case 'coach':
                    $item = Coach::find($id);
                    break;
                case 'club':
                    $item = Club::find($id);
                    break;
                case 'shop':
                    $item = Shop::find($id);
                    break;
                case 'product':
                    $item = Shop::find(Product::firstOrNew(['id' => $id])->shop_id);
                    break;
            }
        }

        if (!$item && $abort)
            return abort(403, 'این مورد وجود ندارد!');
        if (!$item && !$abort)
            return false;
        if ((($item && isset($item->user_id) && $item->user_id == $user->id) || $user->role == 'go'))
            return true;
        $item_is_blog_table = ($item instanceof Blog || $item instanceof Table || $item == Blog::class || $item == Table::class || $item == 'blog' || $item == 'table');
        if ($item_is_blog_table && $user->role == 'bl')
            return true;

        if (!$item_is_blog_table && !($item instanceof Setting) && $user->role == 'ad')
            return true;


        if ($abort)
            return abort(403, 'این مورد متعلق به شما نیست!');
        else return false;
    }

    public function createItem(User $user, $item, $abort)
    {

        if ($user->role == 'go')
            return true;
        $item_is_blog_table = ($item == Blog::class || $item == Table::class || $item == 'blog' || $item == 'table');
        if ($item_is_blog_table && $user->role == 'bl')
            return true;
        if (!$item_is_blog_table && $user->role == 'bl')
            return false;

        if (!$item_is_blog_table)
            return true;


        if ($abort)
            return abort(403, 'مجاز نیستید!');
        else return false;
    }

    public function editItem(User $user, $item, $abort)
    {
        if ($user->role == 'ad' || $user->role == 'go')
            return true;
        elseif ($abort)
            return abort(403, 'این مورد متعلق به شما نیست!');
        else return false;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, User $model)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, User $model)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User $user
     * @param  \App\Models\User $model
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, User $model)
    {
        //
    }
}
