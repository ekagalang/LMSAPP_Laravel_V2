<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'file_path',
        'order',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}

