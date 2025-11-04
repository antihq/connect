<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

class OrganizationPolicy
{
    public function __construct()
    {
        //
    }

    /**
     * Create a new policy instance.
     */
    /**
     * Determine whether the user can create organizations.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the organization.
     */
    public function update(User $user, Organization $organization): bool
    {
        return $organization->user->is($user);
    }

    /**
     * Determine whether the user can switch to the given organization.
     */
    public function switch(User $user, Organization $organization): bool
    {
        return $user->organizations->contains($organization)
            || $organization->members->contains($user);
    }

    /**
     * Determine whether the user can manage members of the organization.
     */
    public function manageMembers(User $user, Organization $organization): bool
    {
        return $organization->user->is($user);
    }
}
