<?php

namespace Tests\Feature\Admin;

use App\Models\Test;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestCrudTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function student(): User
    {
        return User::factory()->create(['role' => 'student']);
    }

    public function test_guest_cannot_access_admin_tests(): void
    {
        $this->get(route('admin.tests.index'))->assertRedirect(route('login'));
    }

    public function test_student_cannot_access_admin_tests(): void
    {
        $this->actingAs($this->student())
            ->get(route('admin.tests.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_index(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.tests.index'))
            ->assertOk()
            ->assertSee(__('tests.admin.index_title'));
    }

    public function test_admin_can_create_test(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->post(route('admin.tests.store'), [
            'title' => 'My new test',
            'description' => 'Some description',
            'is_published' => '1',
        ]);

        $test = Test::where('title', 'My new test')->first();

        $this->assertNotNull($test);
        $this->assertSame($admin->id, $test->user_id);
        $this->assertTrue($test->is_published);
        $response->assertRedirect(route('admin.tests.show', $test));
        $response->assertSessionHas('success');
    }

    public function test_store_validation_rejects_empty_title(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.tests.store'), ['title' => '', 'description' => ''])
            ->assertSessionHasErrors('title');
    }

    public function test_admin_can_update_their_own_test(): void
    {
        $admin = $this->admin();
        $test = Test::factory()->for($admin)->create(['title' => 'Old', 'is_published' => false]);

        $this->actingAs($admin)
            ->put(route('admin.tests.update', $test), [
                'title' => 'New title',
                'description' => 'updated',
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.tests.show', $test));

        $test->refresh();
        $this->assertSame('New title', $test->title);
        $this->assertTrue($test->is_published);
    }

    public function test_admin_cannot_update_other_admins_test(): void
    {
        $owner = $this->admin();
        $intruder = $this->admin();
        $test = Test::factory()->for($owner)->create();

        $this->actingAs($intruder)
            ->put(route('admin.tests.update', $test), ['title' => 'Hacked'])
            ->assertForbidden();
    }

    public function test_admin_can_delete_their_own_test(): void
    {
        $admin = $this->admin();
        $test = Test::factory()->for($admin)->create();

        $this->actingAs($admin)
            ->delete(route('admin.tests.destroy', $test))
            ->assertRedirect(route('admin.tests.index'));

        $this->assertDatabaseMissing('tests', ['id' => $test->id]);
    }

    public function test_admin_cannot_delete_other_admins_test(): void
    {
        $owner = $this->admin();
        $intruder = $this->admin();
        $test = Test::factory()->for($owner)->create();

        $this->actingAs($intruder)
            ->delete(route('admin.tests.destroy', $test))
            ->assertForbidden();

        $this->assertDatabaseHas('tests', ['id' => $test->id]);
    }

    public function test_show_allowed_for_owner_even_when_draft(): void
    {
        $admin = $this->admin();
        $test = Test::factory()->for($admin)->draft()->create();

        $this->actingAs($admin)
            ->get(route('admin.tests.show', $test))
            ->assertOk()
            ->assertSee($test->title);
    }

    public function test_show_denied_for_non_owner_admin_on_draft(): void
    {
        $owner = $this->admin();
        $other = $this->admin();
        $test = Test::factory()->for($owner)->draft()->create();

        $this->actingAs($other)
            ->get(route('admin.tests.show', $test))
            ->assertForbidden();
    }

    public function test_show_allowed_for_any_admin_on_published(): void
    {
        $owner = $this->admin();
        $other = $this->admin();
        $test = Test::factory()->for($owner)->published()->create();

        $this->actingAs($other)
            ->get(route('admin.tests.show', $test))
            ->assertOk();
    }
}
