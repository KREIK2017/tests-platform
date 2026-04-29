<?php

namespace Tests\Feature\Api;

use App\Models\Answer;
use App\Models\Attempt;
use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttemptApiTest extends TestCase
{
    use RefreshDatabase;

    private function publishedTest(int $questionCount = 3): Test
    {
        $admin = User::factory()->admin()->create();
        $test = Test::factory()->for($admin)->published()->create();

        for ($i = 0; $i < $questionCount; $i++) {
            $q = Question::factory()->for($test)->create(['order' => $i]);
            Answer::factory()->for($q)->correct()->create(['text' => "correct-{$i}"]);
            for ($j = 1; $j < 4; $j++) {
                Answer::factory()->for($q)->create(['text' => "wrong-{$i}-{$j}"]);
            }
        }

        return $test;
    }

    public function test_start_creates_attempt_returns_201(): void
    {
        $student = User::factory()->student()->create();
        $test = $this->publishedTest(2);

        $response = $this->actingAs($student, 'sanctum')
            ->postJson("/api/v1/tests/{$test->id}/attempts")
            ->assertCreated()
            ->assertJsonPath('data.user_id', $student->id)
            ->assertJsonPath('data.test_id', $test->id)
            ->assertJsonPath('data.completed_at', null);

        $this->assertDatabaseHas('attempts', [
            'id' => $response->json('data.id'),
            'user_id' => $student->id,
            'completed_at' => null,
        ]);
    }

    public function test_start_resumes_in_progress_attempt(): void
    {
        $student = User::factory()->student()->create();
        $test = $this->publishedTest(1);
        $existing = Attempt::factory()->for($student)->for($test)->create();

        $this->actingAs($student, 'sanctum')
            ->postJson("/api/v1/tests/{$test->id}/attempts")
            ->assertOk()
            ->assertJsonPath('data.id', $existing->id);

        $this->assertSame(1, Attempt::where('user_id', $student->id)->count());
    }

    public function test_start_on_unpublished_test_returns_404(): void
    {
        $student = User::factory()->student()->create();
        $admin = User::factory()->admin()->create();
        $draft = Test::factory()->for($admin)->draft()->create();

        $this->actingAs($student, 'sanctum')
            ->postJson("/api/v1/tests/{$draft->id}/attempts")
            ->assertNotFound();
    }

    public function test_finish_records_score_and_marks_completed(): void
    {
        $student = User::factory()->student()->create();
        $test = $this->publishedTest(3);
        $attempt = Attempt::factory()->for($student)->for($test)->create();

        $payload = ['answers' => []];
        $expectedScore = 0;
        foreach ($test->questions as $i => $question) {
            $pickCorrect = $i !== 1;
            $answer = $pickCorrect
                ? $question->answers->firstWhere('is_correct', true)
                : $question->answers->firstWhere('is_correct', false);

            $payload['answers'][$question->id] = $answer->id;
            if ($pickCorrect) {
                $expectedScore++;
            }
        }

        $this->actingAs($student, 'sanctum')
            ->postJson("/api/v1/attempts/{$attempt->id}/finish", $payload)
            ->assertOk()
            ->assertJsonPath('data.score', $expectedScore)
            ->assertJsonPath('data.total_questions', 3);

        $attempt->refresh();
        $this->assertNotNull($attempt->completed_at);
        $this->assertSame(3, $attempt->attemptAnswers()->count());
    }

    public function test_finish_rejects_unanswered_question_with_422(): void
    {
        $student = User::factory()->student()->create();
        $test = $this->publishedTest(2);
        $attempt = Attempt::factory()->for($student)->for($test)->create();

        $first = $test->questions->first();

        $this->actingAs($student, 'sanctum')
            ->postJson("/api/v1/attempts/{$attempt->id}/finish", [
                'answers' => [$first->id => $first->answers->first()->id],
            ])
            ->assertUnprocessable();
    }

    public function test_finish_other_user_returns_403(): void
    {
        $owner = User::factory()->student()->create();
        $other = User::factory()->student()->create();
        $test = $this->publishedTest(1);
        $attempt = Attempt::factory()->for($owner)->for($test)->create();

        $payload = [
            'answers' => [
                $test->questions->first()->id => $test->questions->first()->answers->first()->id,
            ],
        ];

        $this->actingAs($other, 'sanctum')
            ->postJson("/api/v1/attempts/{$attempt->id}/finish", $payload)
            ->assertForbidden();
    }

    public function test_index_for_student_returns_only_own(): void
    {
        $alice = User::factory()->student()->create();
        $bob = User::factory()->student()->create();
        $test = $this->publishedTest(1);

        $aliceAttempt = Attempt::factory()->for($alice)->for($test)->completed(1, 1)->create();
        Attempt::factory()->for($bob)->for($test)->completed(0, 1)->create();

        $response = $this->actingAs($alice, 'sanctum')
            ->getJson('/api/v1/attempts')
            ->assertOk();

        $ids = collect($response->json('data'))->pluck('id');
        $this->assertCount(1, $ids);
        $this->assertSame($aliceAttempt->id, $ids->first());
    }

    public function test_index_for_admin_returns_all(): void
    {
        $admin = User::factory()->admin()->create();
        $alice = User::factory()->student()->create();
        $bob = User::factory()->student()->create();
        $test = $this->publishedTest(1);

        Attempt::factory()->for($alice)->for($test)->completed(1, 1)->create();
        Attempt::factory()->for($bob)->for($test)->completed(0, 1)->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/attempts')
            ->assertOk();

        $this->assertGreaterThanOrEqual(2, count($response->json('data')));
    }

    public function test_show_attempt_other_student_returns_403(): void
    {
        $owner = User::factory()->student()->create();
        $intruder = User::factory()->student()->create();
        $test = $this->publishedTest(1);
        $attempt = Attempt::factory()->for($owner)->for($test)->completed(1, 1)->create();

        $this->actingAs($intruder, 'sanctum')
            ->getJson("/api/v1/attempts/{$attempt->id}")
            ->assertForbidden();
    }
}
