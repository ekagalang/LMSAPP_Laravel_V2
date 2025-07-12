<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionReply extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'discussion_id', 'body'];

    // Relasi: Sebuah balasan dimiliki oleh satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Sebuah balasan dimiliki oleh satu Diskusi
    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }
}