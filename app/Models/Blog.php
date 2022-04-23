<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $appends = [];
    public $timestamps = true;
    protected $table = 'blogs';
    protected $fillable = [
        'id', 'user_id', 'category_id', 'title', 'summary', 'content', 'tags', 'is_draft', 'active', 'published_at', 'created_at', 'updated_at'
    ];
    protected $casts = [
        'is_draft' => 'boolean',
//        'published_at' => 'timestamp'

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function docs()
    {
        return $this->hasMany(BlogDoc::class, 'docable_id');
    }
}
