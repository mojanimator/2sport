<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{

//    protected $appends = ['image'];
    public $timestamps = true;
    protected $table = 'shops';
    protected $fillable = [
        'id', 'user_id', 'province_id', 'in_review', 'county_id', 'groups', 'name', 'location', 'address', 'phone', 'description', 'created_at', 'updated_at', 'expires_at', 'active', 'hidden',
    ];
    protected $casts = [
        //'id' => 'string',
        'expires_at' => 'timestamp',
        'active' => 'boolean',
        'hidden' => 'boolean',
        'in_review' => 'boolean',
        'user_id' => 'string',
        'province_id' => 'string',
        'county_id' => 'string',
        'sport_id' => 'string',

    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function county()
    {
        return $this->belongsTo(County::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    public function docs()
    {
        return $this->hasMany(Doc::class, 'docable_id')->where('docable_type', \Helper::$typesMap['shops']);
    }

    public function alldocs()
    {
        return $this->hasMany(Doc::class, 'docable_id');
    }

}
