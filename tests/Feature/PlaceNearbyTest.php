<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlaceNearbyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $category = Category::create(['name' => 'Parque', 'slug' => 'parque']);
        $place = fn ($name, $lat, $lng) => Place::create([
            'name' => $name, 'latitude' => $lat, 'longitude' => $lng, 'category_id' => $category->id,
        ]);

        $place('Centro', 21.8818, -102.2915);      // Aguascalientes centro
        $place('Jesus Maria', 21.9614, -102.3436); // ~10 km
        $place('Box corner', 22.1500, -102.0000);  // inside the bounding box, ~42 km away
        $place('Zacatecas', 22.7709, -102.5832);   // ~100 km
    }

    public function test_returns_only_places_within_30_km(): void
    {
        $names = $this->getJson('/api/v1/places?lat=21.8818&lng=-102.2915')
            ->assertOk()
            ->json('data.*.name');

        $this->assertEqualsCanonicalizing(['Centro', 'Jesus Maria'], $names);
    }

    public function test_without_location_returns_all_places(): void
    {
        $this->getJson('/api/v1/places')->assertOk()->assertJsonCount(4, 'data');
    }

    public function test_rejects_invalid_coordinates(): void
    {
        $this->getJson('/api/v1/places?lat=200&lng=-102')->assertStatus(422);
        $this->getJson('/api/v1/places?lat=21.88')->assertStatus(422);
    }
}
