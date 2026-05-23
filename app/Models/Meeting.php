<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    //
    protected $fillable = [
        'title',
        'summary',
        'transcript',
        'action_items',
        'participants',
        'key_points',
        'decisions',
        'user_id',
        'scheduled_at'
    ];

    protected $casts = [
        'action_items' => 'array',
        'participants' => 'array',
        'key_points' => 'array',
        'decisions'=> 'array',
        'scheduled_at' => 'datetime'
    ];


    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
