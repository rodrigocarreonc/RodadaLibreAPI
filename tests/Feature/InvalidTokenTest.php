<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class InvalidTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_route_returns_401_without_json_accept_header(): void
    {
        // Plain post(): before, guests were redirected to the missing login route (500)
        $this->withToken('abc.def.ghi')->post('/api/v1/auth/me')
            ->assertUnauthorized()->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_refresh_returns_401_with_invalid_or_missing_token(): void
    {
        $this->withToken('abc.def.ghi')->postJson('/api/v1/auth/refresh')->assertUnauthorized();
        $this->withToken('abc.def.ghi')->post('/api/v1/auth/refresh')->assertUnauthorized();
        $this->withoutToken()->postJson('/api/v1/auth/refresh')->assertUnauthorized();
    }

    public function test_refresh_returns_401_with_invalidated_token(): void
    {
        $token = JWTAuth::fromUser(User::factory()->create());
        JWTAuth::setToken($token)->invalidate();

        $this->withToken($token)->postJson('/api/v1/auth/refresh')->assertUnauthorized();
    }

    public function test_refresh_still_works_with_valid_token(): void
    {
        $token = JWTAuth::fromUser(User::factory()->create());

        $this->withToken($token)->postJson('/api/v1/auth/refresh')
            ->assertOk()->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }
}
