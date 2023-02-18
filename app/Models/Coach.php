<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    use HasFactory;

    protected $appends = [];
//    public $timestamps = false;
    protected $table = 'coaches';
    protected $fillable = [
        'id', 'user_id', 'sport_id', 'province_id', 'in_review', 'county_id', 'name', 'family', 'born_at', 'is_man', 'phone', 'description', 'created_at', 'updated_at', 'expires_at', 'active', 'hidden',
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

    public function docs()
    {
        return $this->hasMany(Doc::class, 'docable_id')->where('docable_type', \Helper::$typesMap['coaches']);
    }

    public function alldocs()
    {
        return $this->hasMany(Doc::class, 'docable_id');
    }
}
