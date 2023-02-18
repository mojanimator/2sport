<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $appends = [];
    public $timestamps = false;
    protected $table = 'settings';
    protected $fillable = [
        'id', 'name', 'key', 'value',
    ];
}
