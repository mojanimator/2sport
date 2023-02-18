<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    use HasFactory;

    protected $appends = [];
    public $timestamps = false;
    protected $table = 'sports';
    protected $fillable = [
        'id', 'name',
    ];
    protected $casts = [

        'id' => 'string',
    ];
}
