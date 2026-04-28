<?php

namespace App\Policies;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;

class QuestionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Question $question): bool
    {
        return $user->isAdmin() && $question->test->user_id === $user->id;
    }

    public function create(User $user, Test $test): bool
    {
        return $user->isAdmin() && $test->user_id === $user->id;
    }

    public function update(User $user, Question $question): bool
    {
        return $user->isAdmin() && $question->test->user_id === $user->id;
    }

    public function delete(User $user, Question $question): bool
    {
        return $user->isAdmin() && $question->test->user_id === $user->id;
    }

    public function restore(User $user, Question $question): bool
    {
        return $this->delete($user, $question);
    }

    public function forceDelete(User $user, Question $question): bool
    {
        return $this->delete($user, $question);
    }
}
