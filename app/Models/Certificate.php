<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'course_id',
        'template_id',
        'certificate_code',
        'issued_at',
    ];

    /**
     * Get the user that owns the certificate.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course for this certificate.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the template used for this certificate.
     */
    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }
}