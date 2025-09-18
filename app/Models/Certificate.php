<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

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
        'certificate_template_id',
        'certificate_code',
        'path',
        'issued_at',
        // PENAMBAHAN: Kolom untuk data diri yang akan diisi peserta
        'place_of_birth',
        'date_of_birth',
        'identity_number',
        'institution_name',
        // New participant data fields
        'gender',
        'email',
        'occupation',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issued_at' => 'datetime',
        'date_of_birth' => 'date', // PENAMBAHAN: Pastikan kolom tanggal lahir di-cast sebagai tanggal
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
    public function certificateTemplate()
    {
        return $this->belongsTo(CertificateTemplate::class);
    }

    /**
     * Alias for certificateTemplate for backward compatibility
     */
    public function template()
    {
        return $this->certificateTemplate();
    }

    /**
     * Get the full URL to the certificate PDF
     */
    public function getPdfUrlAttribute()
    {
        if ($this->path) {
            // Try multiple methods for URL generation
            try {
                // Method 1: Try Storage::url()
                return Storage::disk('public')->url($this->path);
            } catch (\Exception $e) {
                // Method 2: Use asset() as fallback
                return asset('storage/' . $this->path);
            }
        }
        return null;
    }

    /**
     * Get the verification URL for this certificate
     */
    public function getVerificationUrlAttribute()
    {
        return route('certificates.verify', $this->certificate_code);
    }

    /**
     * Check if the certificate file exists
     */
    public function fileExists()
    {
        if (!$this->path) {
            return false;
        }

        // Cek dengan berbagai metode
        if (\Storage::disk('public')->exists($this->path)) {
            return true;
        }

        // Cek dengan path absolut
        $absolutePath = storage_path('app/public/' . $this->path);
        if (file_exists($absolutePath)) {
            return true;
        }

        // Cek dengan fallback ke certificate_code
        $fallbackPath = 'certificates/' . $this->certificate_code . '.pdf';
        if (\Storage::disk('public')->exists($fallbackPath)) {
            // Update path di database
            $this->update(['path' => $fallbackPath]);
            return true;
        }

        return false;
    }

    /**
     * Generate a unique certificate code
     */
    public static function generateCertificateCode()
    {
        do {
            $code = 'CERT-' . strtoupper(\Illuminate\Support\Str::random(12));
        } while (self::where('certificate_code', $code)->exists());

        return $code;
    }

    /**
     * Get download URL for the certificate
     */
    public function getDownloadUrlAttribute()
    {
        return route('certificates.download', $this);
    }

    /**
     * Get public download URL (for verification page)
     */
    public function getPublicDownloadUrlAttribute()
    {
        return route('certificates.public-download', $this->certificate_code);
    }

    /**
     * Get the storage path for the certificate
     */
    public function getStoragePathAttribute()
    {
        return storage_path('app/public/' . $this->path);
    }
}
