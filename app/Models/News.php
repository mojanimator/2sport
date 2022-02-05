<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $appends = [];
//    public $timestamps = false;
    protected $table = 'news';
    protected $fillable = [
        'id', 'subject', 'text', 'tags', 'created_at', 'updated_at'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(NewsTypes::class);
    }

}
