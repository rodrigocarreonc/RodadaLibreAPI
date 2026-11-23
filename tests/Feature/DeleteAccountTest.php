<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteAccountTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['password' => 'secret']);
        // A real token without logging in: logout() needs one to invalidate, actingAs() doesn't provide it
        $this->token = JWTAuth::fromUser($this->user);
    }

    private function deleteAccount(array $data)
    {
        return $this->withToken($this->token)->deleteJson('/api/v1/auth/me', $data);
    }

    public function test_deletes_account_with_correct_password(): void
    {
        $this->deleteAccount(['current_password' => 'secret'])->assertOk();

        $this->assertModelMissing($this->user);
        $this->withToken($this->token)->postJson('/api/v1/auth/me')->assertUnauthorized();
    }

    public function test_rejects_wrong_or_missing_password(): void
    {
        $this->deleteAccount(['current_password' => 'wrong'])
            ->assertStatus(422)->assertJsonValidationErrors('current_password');
        $this->deleteAccount([])
            ->assertStatus(422)->assertJsonValidationErrors('current_password');

        $this->assertModelExists($this->user);
    }

    public function test_requires_authentication(): void
    {
        $this->deleteJson('/api/v1/auth/me', ['current_password' => 'secret'])->assertUnauthorized();

        $this->assertModelExists($this->user);
    }
}
