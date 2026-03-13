<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return (bool) $user->id;
    }

    public function view(User $user, Application $application): bool
    {
        return $this->owns($user, $application);
    }

    public function create(User $user): bool
    {
        return (bool) $user->id;
    }

    public function update(User $user, Application $application): bool
    {
        return $this->owns($user, $application);
    }

    public function delete(User $user, Application $application): bool
    {
        return $this->owns($user, $application);
    }

    public function move(User $user, Application $application): bool
    {
        return $this->owns($user, $application);
    }

    public function scheduleInterview(User $user, Application $application): bool
    {
        return $this->owns($user, $application);
    }

    public function toggleFavorite(User $user, Application $application): bool
    {
        return $this->owns($user, $application);
    }

    private function owns(User $user, Application $application): bool
    {
        return $application->user_id === $user->id;
    }
}
