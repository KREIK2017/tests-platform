<?php

namespace Tests\Feature\Admin;

use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionCrudTest extends TestCase
{
    use RefreshDatabase;

    private function ownedTest(?User $admin = null): Test
    {
        $admin ??= User::factory()->admin()->create();

        return Test::factory()->for($admin)->create();
    }

    private function fourAnswers(int $correctIndex = 0): array
    {
        $payload = [];
        foreach (range(0, 3) as $i) {
            $payload[$i] = ['text' => "Option #{$i}"];
        }

        return ['answers' => $payload, 'correct_answer' => $correctIndex];
    }

    public function test_admin_can_open_create_form_for_own_test(): void
    {
        $admin = User::factory()->admin()->create();
        $test = $this->ownedTest($admin);

        $this->actingAs($admin)
            ->get(route('admin.tests.questions.create', $test))
            ->assertOk();
    }

    public function test_non_owner_admin_cannot_create_question(): void
    {
        $owner = User::factory()->admin()->create();
        $intruder = User::factory()->admin()->create();
        $test = Test::factory()->for($owner)->create();

        $this->actingAs($intruder)
            ->post(route('admin.tests.questions.store', $test), [
                'text' => 'A new question?',
                ...$this->fourAnswers(),
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('questions', 0);
    }

    public function test_store_creates_question_with_exactly_4_answers_and_one_correct(): void
    {
        $admin = User::factory()->admin()->create();
        $test = $this->ownedTest($admin);

        $this->actingAs($admin)
            ->post(route('admin.tests.questions.store', $test), [
                'text' => 'What is 2 + 2?',
                ...$this->fourAnswers(2),
            ])
            ->assertRedirect(route('admin.tests.show', $test));

        $question = Question::where('text', 'What is 2 + 2?')->firstOrFail();
        $this->assertCount(4, $question->answers);
        $this->assertSame(1, $question->answers->where('is_correct', true)->count());
        $this->assertTrue($question->answers->sortBy('id')->values()[2]->is_correct);
    }

    public function test_store_validates_question_text_min_5(): void
    {
        $admin = User::factory()->admin()->create();
        $test = $this->ownedTest($admin);

        $this->actingAs($admin)
            ->post(route('admin.tests.questions.store', $test), [
                'text' => 'no',
                ...$this->fourAnswers(),
            ])
            ->assertSessionHasErrors('text');
    }

    public function test_store_requires_exactly_4_answers(): void
    {
        $admin = User::factory()->admin()->create();
        $test = $this->ownedTest($admin);

        $tooFew = [
            'answers' => [['text' => 'a'], ['text' => 'b'], ['text' => 'c']],
            'correct_answer' => 0,
        ];

        $this->actingAs($admin)
            ->post(route('admin.tests.questions.store', $test), [
                'text' => 'A valid question?',
                ...$tooFew,
            ])
            ->assertSessionHasErrors('answers');
    }

    public function test_store_requires_correct_answer_index_in_range(): void
    {
        $admin = User::factory()->admin()->create();
        $test = $this->ownedTest($admin);

        $this->actingAs($admin)
            ->post(route('admin.tests.questions.store', $test), [
                'text' => 'A valid question?',
                ...$this->fourAnswers(7),
            ])
            ->assertSessionHasErrors('correct_answer');
    }

    public function test_admin_can_update_question_text(): void
    {
        $admin = User::factory()->admin()->create();
        $test = $this->ownedTest($admin);
        $question = Question::factory()->for($test)->create(['text' => 'Old text']);

        $this->actingAs($admin)
            ->put(route('admin.questions.update', $question), [
                'text' => 'Updated question text',
                'order' => 5,
            ])
            ->assertRedirect(route('admin.tests.show', $test));

        $question->refresh();
        $this->assertSame('Updated question text', $question->text);
        $this->assertSame(5, $question->order);
    }

    public function test_admin_cannot_update_other_admins_question(): void
    {
        $owner = User::factory()->admin()->create();
        $intruder = User::factory()->admin()->create();
        $test = Test::factory()->for($owner)->create();
        $question = Question::factory()->for($test)->create();

        $this->actingAs($intruder)
            ->put(route('admin.questions.update', $question), ['text' => 'Hacked text'])
            ->assertForbidden();
    }

    public function test_admin_can_delete_question_cascades_answers(): void
    {
        $admin = User::factory()->admin()->create();
        $test = $this->ownedTest($admin);
        $question = Question::factory()->for($test)->hasAnswers(4)->create();

        $this->actingAs($admin)
            ->delete(route('admin.questions.destroy', $question))
            ->assertRedirect(route('admin.tests.show', $test));

        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
        $this->assertDatabaseMissing('answers', ['question_id' => $question->id]);
    }
}
