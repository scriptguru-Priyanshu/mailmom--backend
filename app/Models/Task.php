<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //
    protected $fillable = [
        'meeting_id',
        'owner_id',
        'task',
        'deadline',
        'status'
    ];
    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class);
    }
}
