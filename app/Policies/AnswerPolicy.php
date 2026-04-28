<?php

namespace App\Policies;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;

class AnswerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Answer $answer): bool
    {
        return $user->isAdmin() && $answer->question->test->user_id === $user->id;
    }

    public function create(User $user, Question $question): bool
    {
        return $user->isAdmin() && $question->test->user_id === $user->id;
    }

    public function update(User $user, Answer $answer): bool
    {
        return $user->isAdmin() && $answer->question->test->user_id === $user->id;
    }

    public function delete(User $user, Answer $answer): bool
    {
        return $user->isAdmin() && $answer->question->test->user_id === $user->id;
    }

    public function restore(User $user, Answer $answer): bool
    {
        return $this->delete($user, $answer);
    }

    public function forceDelete(User $user, Answer $answer): bool
    {
        return $this->delete($user, $answer);
    }
}
