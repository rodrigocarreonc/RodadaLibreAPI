<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ChangeRequest;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ChangeRequestModerationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        $this->user = User::factory()->create();
    }

    private function createRequest(array $photoIds): ChangeRequest
    {
        $category = Category::create(['name' => 'Parque', 'slug' => 'parque']);

        return ChangeRequest::create([
            'user_id' => $this->user->id,
            'action_type' => 'create',
            'payload' => [
                'category_id' => $category->id,
                'name' => 'Lugar',
                'latitude' => 1,
                'longitude' => 2,
                'photo_ids' => $photoIds,
            ],
        ]);
    }

    public function test_approve_creates_place_and_attaches_photos(): void
    {
        $photo = Photo::create(['url_source' => 'x', 'status' => 'pending']);
        $request = $this->createRequest([$photo->id]);

        $this->actingAs($this->admin, 'api')
            ->postJson("/api/v1/moderation/requests/{$request->id}/approve")
            ->assertOk();

        $photo = Photo::withoutGlobalScope('approved')->find($photo->id);
        $this->assertNotNull($photo->place_id);
        $this->assertSame('approved', $photo->status);
    }

    public function test_reject_deletes_pending_photos(): void
    {
        $photo = Photo::create(['url_source' => 'x', 'status' => 'pending']);
        $request = $this->createRequest([$photo->id]);

        $this->actingAs($this->admin, 'api')
            ->postJson("/api/v1/moderation/requests/{$request->id}/reject")
            ->assertOk();

        $this->assertNull(Photo::withoutGlobalScope('approved')->find($photo->id));
        $this->assertSame('rejected', $request->fresh()->status);
    }
}
