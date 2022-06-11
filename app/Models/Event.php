<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'events';
    protected $fillable = [
        'id', 'user_id', 'sport_id', 'title', 'team1', 'team2', 'score1', 'score2', 'status', 'link', 'source', 'time', 'updated_at', 'details',
    ];
    protected $casts = [
        'time' => 'timestamp',
        'sport_id' => 'string',
    ];
}
