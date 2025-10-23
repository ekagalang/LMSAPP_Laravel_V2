<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'ip_address',
        'user_agent',
        'description',
        'metadata',
        'status',
        'error_message',
    ];

    protected $casts = [
        'metadata' => 'array',
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the activity
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for filtering by action
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $bytes = $this->file_size;
        if ($bytes === 0) return '0 B';

        $k = 1024;
        $sizes = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Get action badge color
     */
    public function getActionBadgeColorAttribute()
    {
        return match($this->action) {
            'upload' => 'blue',
            'delete' => 'red',
            'view' => 'green',
            'copy_link' => 'purple',
            'download' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Get action icon
     */
    public function getActionIconAttribute()
    {
        return match($this->action) {
            'upload' => 'upload',
            'delete' => 'trash',
            'view' => 'eye',
            'copy_link' => 'link',
            'download' => 'download',
            default => 'file',
        };
    }

    /**
     * Static method to log activity
     * Only logs for Admin, EO, and Instructor roles (not Participant)
     */
    public static function log($action, $data = [])
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return null;
        }

        // Get user
        $user = auth()->user();

        // Log all roles except pure Participant
        try {
            $roles = $user->getRoleNames();
            // If user has only the 'participant' role (and no others), skip logging
            if ($roles->count() === 1 && $roles->contains('participant')) {
                return null;
            }
            // If user has no roles assigned, treat as elevated (log) to avoid missing admin-like users
            // Otherwise (any role other than only participant), log
        } catch (\Throwable $e) {
            // If roles not available for any reason, default to logging
        }

        // Create log entry
        return static::create([
            'user_id' => $user->id,
            'action' => $action,
            'file_name' => $data['file_name'] ?? null,
            'file_path' => $data['file_path'] ?? null,
            'file_type' => $data['file_type'] ?? null,
            'file_size' => $data['file_size'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $data['description'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'status' => $data['status'] ?? 'success',
            'error_message' => $data['error_message'] ?? null,
        ]);
    }
}
