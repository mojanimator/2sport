<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'tables';
    protected $fillable = [
        'id', 'title', 'type_id', 'tournament', 'active', 'content', 'updated_at',
    ];
    protected $casts = [
        'active' => 'boolean'

    ];
}
