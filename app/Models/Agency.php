<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;


    protected $table = 'agencies';
    protected $fillable = [
        'id', 'name', 'email', 'owner_id', 'province_id', 'county_id', 'address', 'description', 'email_verified', 'phone_verified', 'phone', 'created_at', 'updated_at', 'active'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

        'id' => 'string',
        'active' => 'boolean',
        'email_verified' => 'boolean',
        'phone_verified' => 'boolean',


    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function county()
    {
        return $this->belongsTo(County::class);
    }

}
