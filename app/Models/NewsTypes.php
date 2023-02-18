<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsTypes extends Model
{
    use HasFactory;

    protected $appends = [];
//    public $timestamps = false;
    protected $table = 'news-types';
    protected $fillable = [
        'id', 'name',
    ];


}
