<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Task extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tasks';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'date',
        'time',
        'status',
        'notification_dismissed_at',
        'last_state_notified',
    ];

    protected $attributes = [
        'status' => 'pending',
    ];

    protected $casts = [
        'notification_dismissed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
