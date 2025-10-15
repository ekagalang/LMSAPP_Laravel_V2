<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_id',
        'file_path',
        'original_name',
        'order',
    ];

    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}

