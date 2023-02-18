<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $appends = [];
    public $timestamps = false;
    protected $table = 'categories';
    protected $fillable = [
        'id', 'name', 'type_id'
    ];
    protected $casts = [
        'id' => 'string'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
