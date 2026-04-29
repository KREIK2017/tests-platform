<?php

namespace App\Policies;

use App\Models\Attempt;
use App\Models\User;

class AttemptPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Attempt $attempt): bool
    {
        return $user->isAdmin() || $attempt->user_id === $user->id;
    }

    public function take(User $user, Attempt $attempt): bool
    {
        return $attempt->user_id === $user->id;
    }

    public function delete(User $user, Attempt $attempt): bool
    {
        return $user->isAdmin() || $attempt->user_id === $user->id;
    }
}
