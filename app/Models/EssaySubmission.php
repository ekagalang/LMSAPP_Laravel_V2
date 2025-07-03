<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EssaySubmission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'content_id',
        'answer',
        'score',
        'feedback',
        'graded_at',
    ];

    /**
     * Mendapatkan pengguna yang mengirimkan esai.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan konten esai yang terkait.
     */
    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}