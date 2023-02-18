<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use PHPUnit\TextUI\Help;
use Symfony\Component\VarDumper\Caster\ImgStub;

class Product extends Model
{
    protected $appends = ['salePercent'];
//    public $timestamps = false;
    protected $table = 'products';
    protected $fillable = [
        'id', 'shop_id', 'group_id',
        'props', 'name', 'description', 'count', 'tags', 'sold', 'price', 'discount_price', 'created_at', 'active', 'hidden',
    ];
    protected $casts = [

        'active' => 'boolean',
        'hidden' => 'boolean',
        'in_review' => 'boolean',
        'sold' => 'string',
        'price' => 'string',
        'count' => 'string',
        'group_id' => 'string',
        'shop_id' => 'integer',
        'discount_price' => 'string',
        'province_id' => 'string',
        'county_id' => 'string',

    ];
//    protected static function booted()
//    {
//        static::addGlobalScope('active', function (Builder $builder) {
//            $builder->where('active', true);
//        });
//    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }


    public function images()
    {
        return Doc::where('docable_id', $this->id)->where('docable_type', \Helper::$typesMap['products'])->get()->map(function ($data) {
            return asset("storage/$data->type_id/$data->id.jpg");

        });
    }


    public function getSalePercentAttribute()
    {

        if (!$this->discount_price || !$this->price)
            return '0%';
        return ceil(($this->price - $this->discount_price) * 100 / ($this->price)) . "%";
    }

//    public function getSlugAttribute()
//    {
//
//
//        return str_slug($this->name, $language = 'fa');
//    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }

    public function docs()
    {
        return $this->hasMany(Doc::class, 'docable_id')->where('docable_type', \Helper::$typesMap['products']);
    }

    public function alldocs()
    {
        return $this->hasMany(Doc::class, 'docable_id');
    }


}
