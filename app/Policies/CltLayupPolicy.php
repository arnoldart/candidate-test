<?php

namespace App\Policies;

use App\Models\CltLayup;
use App\Models\User;

class CltLayupPolicy
{
    /**
     * Determine whether the user can view the listing.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CltLayup $layup): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CltLayup $layup): bool
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CltLayup $layup): bool
    {
        return true;
    }

    /**
     * Determine whether the user can duplicate the model.
     */
    public function duplicate(User $user, CltLayup $layup): bool
    {
        return true;
    }
}
