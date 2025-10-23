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
        return $user->can('view certificates')
            || $user->can('download certificates')
            || $user->can('view certificate management')
            || $user->can('view certificate analytics');
    }

    /**
     * Determine whether the user can view the certificate.
     */
    public function view(User $user, Certificate $certificate): bool
    {
        // Users with full course management can view all certificates
        if ($user->can('manage all courses')) {
            return true;
        }

        // Users can view their own certificates
        if ($certificate->user_id === $user->id) {
            return true;
        }

        // Instructors can view certificates for courses they teach
        if ($user->can('view certificates')) {
            return $certificate->course->instructors()->where('user_id', $user->id)->exists();
        }

        // Event organizers can view certificates for courses they organize
        if ($user->can('view certificate management') || $user->can('view certificate analytics')) {
            return $certificate->course->eventOrganizers()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create certificates.
     */
    public function create(User $user): bool
    {
        return $user->can('issue certificates') || $user->can('bulk issue certificates');
    }

    /**
     * Determine whether the user can update the certificate.
     */
    public function update(User $user, Certificate $certificate): bool
    {
        // Users with full course management can update all certificates
        if ($user->can('manage all courses')) {
            return true;
        }

        // Instructors/EOs with cert capabilities
        if ($user->can('regenerate certificates') || $user->can('issue certificates')) {
            return $certificate->course->instructors()->where('user_id', $user->id)->exists() ||
                   $certificate->course->eventOrganizers()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the certificate.
     */
    public function delete(User $user, Certificate $certificate): bool
    {
        // Users with full course management can delete all certificates
        if ($user->can('manage all courses')) {
            return true;
        }

        // Instructors/EOs with delete capability
        if ($user->can('delete certificates')) {
            return $certificate->course->instructors()->where('user_id', $user->id)->exists() ||
                   $certificate->course->eventOrganizers()->where('user_id', $user->id)->exists();
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
        return $user->can('delete certificates');
    }
}
