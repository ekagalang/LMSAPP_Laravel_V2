<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoInteractionResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_interaction_id',
        'user_id',
        'response_data',
        'is_correct',
        'attempts',
        'answered_at'
    ];

    protected $casts = [
        'response_data' => 'array',
        'is_correct' => 'boolean',
        'answered_at' => 'datetime'
    ];

    public function videoInteraction()
    {
        return $this->belongsTo(VideoInteraction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function incrementAttempts()
    {
        $this->increment('attempts');
        return $this;
    }
}
