<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasAuthenticatedUser
{
    /**
     * Attempt to get the authenticated user across multiple guards.
     *
     * @return mixed The authenticated user, or null if no user is authenticated.
     */
    public function getAuthenticatedUser()
    {
        $guards = ['admin', 'team', 'vendor', 'manager', 'nonvenue', 'bdm', 'vendormanager', 'seomanager']; // List all your guards

        foreach ($guards as $guard) {
            if ($user = Auth::guard($guard)->user()) {
                return $user;
            }
        }

        return null; // No authenticated user found across all guards
    }

    /**
     * Get the ID of the authenticated user, or a default value if not authenticated.
     *
     * @return mixed The authenticated user's ID, or a default value.
     */
    public function getAuthenticatedUserId($default = 'Unknown User')
    {
        if ($user = $this->getAuthenticatedUser()) {
            return $user->id;
        }

        return $default;
    }
}
