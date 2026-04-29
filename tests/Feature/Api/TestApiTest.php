<?php

namespace Tests\Feature\Api;

use App\Models\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_returns_401(): void
    {
        $this->getJson('/api/v1/tests')->assertUnauthorized();
    }

    public function test_unverified_user_blocked_with_403(): void
    {
        $user = User::factory()->student()->unverified()->create();

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/tests')
            ->assertForbidden();
    }

    public function test_student_index_lists_only_published_tests(): void
    {
        $student = User::factory()->student()->create();
        $author = User::factory()->admin()->create();

        Test::factory()->for($author)->published()->create(['title' => 'PublishedT']);
        Test::factory()->for($author)->draft()->create(['title' => 'DraftT']);

        $response = $this->actingAs($student, 'sanctum')
            ->getJson('/api/v1/tests')
            ->assertOk();

        $titles = collect($response->json('data'))->pluck('title');
        $this->assertContains('PublishedT', $titles);
        $this->assertNotContains('DraftT', $titles);
    }

    public function test_admin_index_lists_drafts_too(): void
    {
        $admin = User::factory()->admin()->create();
        Test::factory()->for($admin)->draft()->create(['title' => 'DraftT']);

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/tests')
            ->assertOk();

        $titles = collect($response->json('data'))->pluck('title');
        $this->assertContains('DraftT', $titles);
    }

    public function test_student_cannot_create_test_returns_403(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student, 'sanctum')
            ->postJson('/api/v1/tests', ['title' => 'Nope'])
            ->assertForbidden();
    }

    public function test_admin_can_create_test(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/tests', [
                'title' => 'API test',
                'description' => 'desc',
                'is_published' => true,
            ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'API test')
            ->assertJsonPath('data.is_published', true)
            ->assertJsonPath('data.user_id', $admin->id);
    }

    public function test_store_validation_returns_422(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/tests', ['title' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('title');
    }

    public function test_admin_can_update_own_test(): void
    {
        $admin = User::factory()->admin()->create();
        $test = Test::factory()->for($admin)->create(['title' => 'Old']);

        $this->actingAs($admin, 'sanctum')
            ->putJson("/api/v1/tests/{$test->id}", ['title' => 'New', 'description' => null])
            ->assertOk()
            ->assertJsonPath('data.title', 'New');
    }

    public function test_admin_cannot_update_other_admins_test_returns_403(): void
    {
        $owner = User::factory()->admin()->create();
        $intruder = User::factory()->admin()->create();
        $test = Test::factory()->for($owner)->create();

        $this->actingAs($intruder, 'sanctum')
            ->putJson("/api/v1/tests/{$test->id}", ['title' => 'Hacked'])
            ->assertForbidden();
    }

    public function test_admin_can_delete_own_test(): void
    {
        $admin = User::factory()->admin()->create();
        $test = Test::factory()->for($admin)->create();

        $this->actingAs($admin, 'sanctum')
            ->deleteJson("/api/v1/tests/{$test->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('tests', ['id' => $test->id]);
    }

    public function test_show_unknown_test_returns_404_json(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/tests/999999')
            ->assertNotFound()
            ->assertJsonStructure(['message']);
    }

    public function test_answer_is_correct_hidden_for_student(): void
    {
        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create();
        $test = Test::factory()->for($admin)->published()->create();
        $question = $test->questions()->create(['text' => 'Whats up?', 'order' => 1]);
        $question->answers()->create(['text' => 'Correct', 'is_correct' => true]);
        $question->answers()->create(['text' => 'Wrong', 'is_correct' => false]);

        $studentResponse = $this->actingAs($student, 'sanctum')
            ->getJson("/api/v1/tests/{$test->id}")
            ->assertOk();

        $answers = collect($studentResponse->json('data.questions.0.answers'));
        foreach ($answers as $a) {
            $this->assertArrayNotHasKey('is_correct', $a);
        }

        $adminResponse = $this->actingAs($admin, 'sanctum')
            ->getJson("/api/v1/tests/{$test->id}")
            ->assertOk();

        $answers = collect($adminResponse->json('data.questions.0.answers'));
        foreach ($answers as $a) {
            $this->assertArrayHasKey('is_correct', $a);
        }
    }
}
