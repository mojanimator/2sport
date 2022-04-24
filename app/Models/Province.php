<?php

namespace App\Models;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{

    protected $fillable = [
        'name',
    ];
    protected $table = 'province';
    protected $casts = [

        'id' => 'string',
    ];

    public function counties()
    {
        return $this->hasMany(County::class, 'province_id');
    }


}
