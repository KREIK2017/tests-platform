<?php

namespace App\Policies;

use App\Models\Test;
use App\Models\User;

class TestPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Test $test): bool
    {
        return $test->is_published || $test->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Test $test): bool
    {
        return $user->isAdmin() && $test->user_id === $user->id;
    }

    public function delete(User $user, Test $test): bool
    {
        return $user->isAdmin() && $test->user_id === $user->id;
    }

    public function restore(User $user, Test $test): bool
    {
        return $user->isAdmin() && $test->user_id === $user->id;
    }

    public function forceDelete(User $user, Test $test): bool
    {
        return $user->isAdmin() && $test->user_id === $user->id;
    }
}
