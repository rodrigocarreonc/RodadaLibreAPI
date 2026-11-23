<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['password' => 'old-secret']);
    }

    private function changePassword(array $data)
    {
        return $this->actingAs($this->user, 'api')->putJson('/api/v1/auth/me/password', $data);
    }

    public function test_changes_password_with_correct_current_password(): void
    {
        $this->changePassword([
            'current_password' => 'old-secret',
            'password' => 'new-secret',
            'password_confirmation' => 'new-secret',
        ])->assertOk();

        $this->assertTrue(Hash::check('new-secret', $this->user->fresh()->password));
    }

    public function test_rejects_wrong_current_password(): void
    {
        $this->changePassword([
            'current_password' => 'wrong',
            'password' => 'new-secret',
            'password_confirmation' => 'new-secret',
        ])->assertStatus(422)->assertJsonValidationErrors('current_password');

        $this->assertTrue(Hash::check('old-secret', $this->user->fresh()->password));
    }

    public function test_rejects_wrong_current_password_without_json_accept_header(): void
    {
        // The Android app sends plain requests; a redirect here would look like a success
        $this->actingAs($this->user, 'api')->put('/api/v1/auth/me/password', [
            'current_password' => 'wrong',
            'password' => 'new-secret',
            'password_confirmation' => 'new-secret',
        ])->assertStatus(422)->assertJsonValidationErrors('current_password');
    }

    public function test_rejects_invalid_new_password(): void
    {
        $this->changePassword([
            'current_password' => 'old-secret',
            'password' => 'new-secret',
            'password_confirmation' => 'other',
        ])->assertStatus(422)->assertJsonValidationErrors('password');

        $this->changePassword([
            'current_password' => 'old-secret',
            'password' => 'old-secret',
            'password_confirmation' => 'old-secret',
        ])->assertStatus(422)->assertJsonValidationErrors('password');
    }

    public function test_profile_update_ignores_password(): void
    {
        $this->actingAs($this->user, 'api')->putJson('/api/v1/auth/me', [
            'first_name' => 'Ana',
            'password' => 'hijacked',
            'password_confirmation' => 'hijacked',
        ])->assertOk()->assertJsonPath('first_name', 'Ana');

        $this->assertTrue(Hash::check('old-secret', $this->user->fresh()->password));
    }

    public function test_requires_authentication(): void
    {
        $this->putJson('/api/v1/auth/me/password', [])->assertUnauthorized();
    }
}
