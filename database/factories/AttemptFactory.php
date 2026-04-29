<?php

namespace Database\Factories;

use App\Models\Attempt;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attempt>
 */
class AttemptFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->student(),
            'test_id' => Test::factory(),
            'score' => 0,
            'total_questions' => 0,
            'completed_at' => null,
        ];
    }

    public function completed(int $score = 0, int $total = 5): static
    {
        return $this->state([
            'score' => $score,
            'total_questions' => $total,
            'completed_at' => now(),
        ]);
    }
}
