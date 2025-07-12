<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'content_id', 'title', 'body'];

    // Relasi: Sebuah diskusi dimiliki oleh satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function content()
    {
        return $this->belongsTo(Content::class);
    }

    // Relasi: Sebuah diskusi memiliki banyak balasan
    public function replies()
    {
        return $this->hasMany(DiscussionReply::class)->orderBy('created_at', 'asc');
    }
}