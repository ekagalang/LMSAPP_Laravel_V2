<?php
// app/Models/Message.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'user_id',
        'content',
        'type',
        'metadata',
        'is_edited',
        'edited_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime'
    ];

    // ========================================
    // RELATIONSHIPS
    // ========================================

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========================================
    // SCOPES
    // ========================================

    public function scopeRecent($query, $limit = 50)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    public function scopeOlderThan($query, $messageId)
    {
        return $query->where('id', '<', $messageId);
    }

    // ========================================
    // METHODS
    // ========================================

    public function markAsEdited(): void
    {
        $this->update([
            'is_edited' => true,
            'edited_at' => now()
        ]);
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['user'] = $this->user->only(['id', 'name']);
        $array['formatted_time'] = $this->created_at->diffForHumans();
        $array['chat_context'] = $this->chat->getContextInfo();
        return $array;
    }
}
