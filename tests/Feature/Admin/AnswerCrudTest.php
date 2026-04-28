<?php

namespace Tests\Feature\Admin;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnswerCrudTest extends TestCase
{
    use RefreshDatabase;

    private function ownedQuestion(?User $admin = null): Question
    {
        $admin ??= User::factory()->admin()->create();
        $test = Test::factory()->for($admin)->create();

        return Question::factory()->for($test)->create();
    }

    public function test_admin_can_create_extra_answer_for_own_question(): void
    {
        $admin = User::factory()->admin()->create();
        $question = $this->ownedQuestion($admin);

        $this->actingAs($admin)
            ->post(route('admin.questions.answers.store', $question), [
                'text' => 'Extra option',
                'is_correct' => '0',
            ])
            ->assertRedirect(route('admin.tests.show', $question->test_id));

        $this->assertDatabaseHas('answers', [
            'question_id' => $question->id,
            'text' => 'Extra option',
            'is_correct' => false,
        ]);
    }

    public function test_creating_correct_answer_unsets_others(): void
    {
        $admin = User::factory()->admin()->create();
        $question = $this->ownedQuestion($admin);
        $existing = Answer::factory()->for($question)->correct()->create();

        $this->actingAs($admin)
            ->post(route('admin.questions.answers.store', $question), [
                'text' => 'New correct one',
                'is_correct' => '1',
            ])
            ->assertRedirect();

        $this->assertFalse($existing->refresh()->is_correct);
        $this->assertSame(1, $question->answers()->where('is_correct', true)->count());
    }

    public function test_admin_can_edit_answer_text(): void
    {
        $admin = User::factory()->admin()->create();
        $question = $this->ownedQuestion($admin);
        $answer = Answer::factory()->for($question)->create(['text' => 'Old']);

        $this->actingAs($admin)
            ->put(route('admin.answers.update', $answer), [
                'text' => 'New text',
                'is_correct' => '0',
            ])
            ->assertRedirect(route('admin.tests.show', $question->test_id));

        $this->assertSame('New text', $answer->refresh()->text);
    }

    public function test_marking_answer_correct_unsets_other_correct_answer(): void
    {
        $admin = User::factory()->admin()->create();
        $question = $this->ownedQuestion($admin);
        $first = Answer::factory()->for($question)->correct()->create();
        $second = Answer::factory()->for($question)->create(['is_correct' => false]);

        $this->actingAs($admin)
            ->put(route('admin.answers.update', $second), [
                'text' => $second->text,
                'is_correct' => '1',
            ])
            ->assertRedirect();

        $this->assertFalse($first->refresh()->is_correct);
        $this->assertTrue($second->refresh()->is_correct);
        $this->assertSame(1, $question->answers()->where('is_correct', true)->count());
    }

    public function test_non_owner_admin_cannot_edit_answer(): void
    {
        $owner = User::factory()->admin()->create();
        $intruder = User::factory()->admin()->create();
        $question = $this->ownedQuestion($owner);
        $answer = Answer::factory()->for($question)->create();

        $this->actingAs($intruder)
            ->put(route('admin.answers.update', $answer), ['text' => 'hacked'])
            ->assertForbidden();
    }

    public function test_admin_can_delete_answer(): void
    {
        $admin = User::factory()->admin()->create();
        $question = $this->ownedQuestion($admin);
        $answer = Answer::factory()->for($question)->create();

        $this->actingAs($admin)
            ->delete(route('admin.answers.destroy', $answer))
            ->assertRedirect(route('admin.tests.show', $question->test_id));

        $this->assertDatabaseMissing('answers', ['id' => $answer->id]);
    }

    public function test_validation_requires_answer_text(): void
    {
        $admin = User::factory()->admin()->create();
        $question = $this->ownedQuestion($admin);

        $this->actingAs($admin)
            ->post(route('admin.questions.answers.store', $question), [
                'text' => '',
            ])
            ->assertSessionHasErrors('text');
    }
}
