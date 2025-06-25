<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'objectives',
        'thumbnail',
        'status',
    ];

    // Relasi ke User (instruktur yang membuat kursus)
    public function instructor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

     // Relasi ke Lesson (satu kursus punya banyak pelajaran)
    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    // Relasi ke User (peserta yang enroll kursus ini)
    public function participants()
    {
        return $this->belongsToMany(User::class, 'course_user');
    }

    // Atribut untuk status
    // public function getStatusColorAttribute()
    // {
    //     return [
    //         'draft' => 'bg-gray-300 text-gray-800',
    //         'published' => 'bg-green-500 text-white',
    //     ][$this->status] ?? 'bg-gray-200';
    // }
}