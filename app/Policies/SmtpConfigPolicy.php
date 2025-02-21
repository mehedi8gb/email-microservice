<?php

namespace App\Policies;

use App\Models\SmtpConfig;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SmtpConfigPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function show(User $user, SmtpConfig $smtpConfig): bool
    {
        return $user->id === $smtpConfig->company->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SmtpConfig $smtpConfig): bool
    {
        return $user->id === $smtpConfig->company->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function destroy(User $user, SmtpConfig $smtpConfig): bool
    {
        return $user->id === $smtpConfig->company->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SmtpConfig $smtpConfig): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SmtpConfig $smtpConfig): bool
    {
        return false;
    }
}
