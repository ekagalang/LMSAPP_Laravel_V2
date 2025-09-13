<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'type',
        'timestamp',
        'title',
        'description',
        'data',
        'position',
        'is_active',
        'order'
    ];

    protected $casts = [
        'data' => 'array',
        'position' => 'array',
        'is_active' => 'boolean',
        'timestamp' => 'decimal:2'
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    public function responses()
    {
        return $this->hasMany(VideoInteractionResponse::class);
    }

    public function userResponse($userId)
    {
        return $this->responses()->where('user_id', $userId)->first();
    }

    public function hasUserResponded($userId)
    {
        return $this->responses()->where('user_id', $userId)->exists();
    }

    public function getCorrectResponsesCount()
    {
        return $this->responses()->where('is_correct', true)->count();
    }

    public function getTotalResponsesCount()
    {
        return $this->responses()->count();
    }

    public function getSuccessRate()
    {
        $total = $this->getTotalResponsesCount();
        if ($total === 0) return 0;
        
        return ($this->getCorrectResponsesCount() / $total) * 100;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrderedByTimestamp($query)
    {
        return $query->orderBy('timestamp');
    }
}
