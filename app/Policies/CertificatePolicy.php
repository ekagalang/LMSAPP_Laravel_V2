<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    /**
     * Determine whether the user can view any certificates.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'instructor', 'event-organizer', 'participant']);
    }

    /**
     * Determine whether the user can view the certificate.
     */
    public function view(User $user, Certificate $certificate): bool
    {
        // Super admin can view all certificates
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Users can view their own certificates
        if ($certificate->user_id === $user->id) {
            return true;
        }

        // Instructors can view certificates for courses they teach
        if ($user->hasRole('instructor')) {
            return $certificate->course->instructors()->where('user_id', $user->id)->exists();
        }

        // Event organizers can view certificates for courses they organize
        if ($user->hasRole('event-organizer')) {
            return $certificate->course->eventOrganizers()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create certificates.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['super-admin', 'instructor', 'event-organizer']);
    }

    /**
     * Determine whether the user can update the certificate.
     */
    public function update(User $user, Certificate $certificate): bool
    {
        // Super admin can update all certificates
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Instructors can update certificates for courses they teach
        if ($user->hasRole('instructor')) {
            return $certificate->course->instructors()->where('user_id', $user->id)->exists();
        }

        // Event organizers can update certificates for courses they organize
        if ($user->hasRole('event-organizer')) {
            return $certificate->course->eventOrganizers()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the certificate.
     */
    public function delete(User $user, Certificate $certificate): bool
    {
        // Super admin can delete all certificates
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Instructors can delete certificates for courses they teach
        if ($user->hasRole('instructor')) {
            return $certificate->course->instructors()->where('user_id', $user->id)->exists();
        }

        // Event organizers can delete certificates for courses they organize
        if ($user->hasRole('event-organizer')) {
            return $certificate->course->eventOrganizers()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can restore the certificate.
     */
    public function restore(User $user, Certificate $certificate): bool
    {
        return $this->delete($user, $certificate);
    }

    /**
     * Determine whether the user can permanently delete the certificate.
     */
    public function forceDelete(User $user, Certificate $certificate): bool
    {
        return $user->hasRole('super-admin');
    }
}
