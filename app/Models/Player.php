<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use Symfony\Component\VarDumper\Caster\ImgStub;

class Player extends Model
{

    protected $appends = [];
//    public $timestamps = false;
    protected $table = 'players';
    protected $fillable = [
        'id', 'user_id', 'province_id','in_review', 'county_id', 'sport_id', 'name', 'family', 'height', 'weight', 'born_at', 'is_man', 'phone', 'description', 'created_at', 'updated_at', 'expires_at', 'active', 'hidden',
    ];
    protected $casts = [
        'born_at' => 'timestamp',
        'expires_at' => 'timestamp',
        'is_man' => 'boolean',
        'active' => 'boolean',
        'hidden' => 'boolean',
        'in_review' => 'boolean',
        'user_id' => 'string',
        'province_id' => 'string',
        'county_id' => 'string',
        'sport_id' => 'string',
        'height' => 'string',
        'weight' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function county()
    {
        return $this->belongsTo(County::class);
    }

    public function sport()
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * Get all of the player's docs.
     */
    public function docs()
    {
        return $this->hasMany(Doc::class, 'docable_id')->where('docable_type', \Helper::$typesMap['players']);
    }

    public function alldocs()
    {
        return $this->hasMany(Doc::class, 'docable_id');
    }


    public function profile()
    {
        return $this->hasMany(Doc::class, 'docable_id')->where('docable_type', \Helper::$typesMap['players'])->where('type_id', \Helper::$docsMap['profile']);

    }


}
