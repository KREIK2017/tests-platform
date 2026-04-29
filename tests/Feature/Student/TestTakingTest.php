<?php

namespace Tests\Feature\Student;

use App\Models\Answer;
use App\Models\Attempt;
use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestTakingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Build a published test with N questions, each having 4 answers
     * (the 1st answer is the correct one).
     */
    private function publishedTestWithQuestions(int $count = 3, ?User $author = null): Test
    {
        $author ??= User::factory()->admin()->create();
        $test = Test::factory()->for($author)->published()->create();

        for ($i = 0; $i < $count; $i++) {
            $question = Question::factory()->for($test)->create(['order' => $i]);
            Answer::factory()->for($question)->correct()->create(['text' => "correct-{$i}"]);
            for ($j = 1; $j < 4; $j++) {
                Answer::factory()->for($question)->create(['text' => "wrong-{$i}-{$j}"]);
            }
        }

        return $test;
    }

    public function test_index_lists_only_published_tests(): void
    {
        $student = User::factory()->student()->create();
        $author = User::factory()->admin()->create();

        $published = Test::factory()->for($author)->published()->create(['title' => 'PublishedTest']);
        $draft = Test::factory()->for($author)->draft()->create(['title' => 'DraftTest']);

        $this->actingAs($student)
            ->get(route('tests.index'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($draft->title);
    }

    public function test_show_404s_for_unpublished_test(): void
    {
        $student = User::factory()->student()->create();
        $draft = Test::factory()->draft()->create();

        $this->actingAs($student)
            ->get(route('tests.show', $draft))
            ->assertNotFound();
    }

    public function test_start_creates_attempt_and_redirects_to_take(): void
    {
        $student = User::factory()->student()->create();
        $test = $this->publishedTestWithQuestions(3);

        $response = $this->actingAs($student)->post(route('tests.start', $test));

        $attempt = Attempt::where('user_id', $student->id)->where('test_id', $test->id)->first();
        $this->assertNotNull($attempt);
        $this->assertNull($attempt->completed_at);
        $this->assertSame(3, $attempt->total_questions);

        $response->assertRedirect(route('attempts.take', $attempt));
    }

    public function test_start_resumes_existing_in_progress_attempt(): void
    {
        $student = User::factory()->student()->create();
        $test = $this->publishedTestWithQuestions(2);

        $existing = Attempt::factory()->for($student)->for($test)->create();

        $this->actingAs($student)
            ->post(route('tests.start', $test))
            ->assertRedirect(route('attempts.take', $existing));

        $this->assertSame(1, Attempt::where('user_id', $student->id)->count());
    }

    public function test_take_redirects_to_show_when_attempt_already_completed(): void
    {
        $student = User::factory()->student()->create();
        $test = $this->publishedTestWithQuestions(2);
        $attempt = Attempt::factory()->for($student)->for($test)->completed(2, 2)->create();

        $this->actingAs($student)
            ->get(route('attempts.take', $attempt))
            ->assertRedirect(route('attempts.show', $attempt));
    }

    public function test_other_user_cannot_take_someones_attempt(): void
    {
        $owner = User::factory()->student()->create();
        $other = User::factory()->student()->create();
        $test = $this->publishedTestWithQuestions(1);
        $attempt = Attempt::factory()->for($owner)->for($test)->create();

        $this->actingAs($other)
            ->get(route('attempts.take', $attempt))
            ->assertForbidden();
    }

    public function test_finish_records_attempt_answers_computes_score_and_marks_completed(): void
    {
        $student = User::factory()->student()->create();
        $test = $this->publishedTestWithQuestions(3);
        $attempt = Attempt::factory()->for($student)->for($test)->create([
            'total_questions' => 3,
        ]);

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

        $this->actingAs($student)
            ->post(route('attempts.finish', $attempt), $payload)
            ->assertRedirect(route('attempts.show', $attempt));

        $attempt->refresh();
        $this->assertNotNull($attempt->completed_at);
        $this->assertSame($expectedScore, $attempt->score);
        $this->assertSame(3, $attempt->total_questions);
        $this->assertSame(3, $attempt->attemptAnswers()->count());
    }

    public function test_finish_rejects_unanswered_questions(): void
    {
        $student = User::factory()->student()->create();
        $test = $this->publishedTestWithQuestions(2);
        $attempt = Attempt::factory()->for($student)->for($test)->create();

        $firstQuestion = $test->questions->first();
        $payload = [
            'answers' => [
                $firstQuestion->id => $firstQuestion->answers->first()->id,
            ],
        ];

        $this->actingAs($student)
            ->post(route('attempts.finish', $attempt), $payload)
            ->assertSessionHasErrors();

        $attempt->refresh();
        $this->assertNull($attempt->completed_at);
        $this->assertSame(0, $attempt->attemptAnswers()->count());
    }

    public function test_finish_rejects_answer_id_that_does_not_belong_to_question(): void
    {
        $student = User::factory()->student()->create();
        $test = $this->publishedTestWithQuestions(2);
        $attempt = Attempt::factory()->for($student)->for($test)->create();

        $other = $this->publishedTestWithQuestions(1);
        $foreignAnswer = $other->questions->first()->answers->first();

        $payload = ['answers' => []];
        foreach ($test->questions as $question) {
            $payload['answers'][$question->id] = $foreignAnswer->id;
        }

        $this->actingAs($student)
            ->post(route('attempts.finish', $attempt), $payload)
            ->assertSessionHasErrors();
    }

    public function test_my_attempts_for_student_shows_only_own(): void
    {
        $alice = User::factory()->student()->create();
        $bob = User::factory()->student()->create();
        $test = $this->publishedTestWithQuestions(1);

        $aliceAttempt = Attempt::factory()->for($alice)->for($test)->completed(1, 1)->create();
        $bobAttempt = Attempt::factory()->for($bob)->for($test)->completed(0, 1)->create();

        $this->actingAs($alice)
            ->get(route('attempts.index'))
            ->assertOk()
            ->assertSee($test->title)
            ->assertDontSee($bob->name);
    }

    public function test_my_attempts_for_admin_shows_all(): void
    {
        $admin = User::factory()->admin()->create();
        $alice = User::factory()->student()->create();
        $bob = User::factory()->student()->create();
        $test = $this->publishedTestWithQuestions(1);

        Attempt::factory()->for($alice)->for($test)->completed(1, 1)->create();
        Attempt::factory()->for($bob)->for($test)->completed(0, 1)->create();

        $this->actingAs($admin)
            ->get(route('attempts.index'))
            ->assertOk()
            ->assertSee($alice->name)
            ->assertSee($bob->name);
    }

    public function test_show_attempt_owner_can_see(): void
    {
        $student = User::factory()->student()->create();
        $test = $this->publishedTestWithQuestions(2);
        $attempt = Attempt::factory()->for($student)->for($test)->completed(1, 2)->create();

        $this->actingAs($student)
            ->get(route('attempts.show', $attempt))
            ->assertOk()
            ->assertSee($test->title);
    }

    public function test_show_attempt_other_student_cannot_see(): void
    {
        $owner = User::factory()->student()->create();
        $intruder = User::factory()->student()->create();
        $test = $this->publishedTestWithQuestions(1);
        $attempt = Attempt::factory()->for($owner)->for($test)->completed(1, 1)->create();

        $this->actingAs($intruder)
            ->get(route('attempts.show', $attempt))
            ->assertForbidden();
    }

    public function test_show_attempt_admin_can_see_anyone_else(): void
    {
        $admin = User::factory()->admin()->create();
        $owner = User::factory()->student()->create();
        $test = $this->publishedTestWithQuestions(1);
        $attempt = Attempt::factory()->for($owner)->for($test)->completed(1, 1)->create();

        $this->actingAs($admin)
            ->get(route('attempts.show', $attempt))
            ->assertOk();
    }
}
