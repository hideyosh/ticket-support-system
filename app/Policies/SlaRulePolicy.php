<?php

namespace App\Policies;

use App\Models\SlaRule;
use App\Models\User;
// use Illuminate\Auth\Access\Response;

class SlaRulePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $roleName = $user->role?->role_name;

        if ($roleName === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SlaRule $SlaRule): bool
    {
        $roleName = $user->role?->role_name;

        if ($roleName === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $roleName = $user->role?->role_name;

        if ($roleName === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SlaRule $SlaRule): bool
    {
        $roleName = $user->role?->role_name;

        if ($roleName === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SlaRule $SlaRule): bool
    {
        $roleName = $user->role?->role_name;

        if ($roleName === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SlaRule $SlaRule): bool
    {
        $roleName = $user->role?->role_name;

        if ($roleName === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SlaRule $SlaRule): bool
    {
        $roleName = $user->role?->role_name;

        if ($roleName === 'admin') {
            return true;
        }

        return false;
    }
}
