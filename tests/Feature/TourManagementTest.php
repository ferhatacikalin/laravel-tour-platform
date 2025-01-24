<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $tourOperator;
    private User $anotherTourOperator;
    private Tour $tour;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with different roles
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->tourOperator = User::factory()->create(['role' => 'tour_operator']);
        $this->anotherTourOperator = User::factory()->create(['role' => 'tour_operator']);

        // Create a tour for testing
        $this->tour = Tour::factory()->create([
            'user_id' => $this->tourOperator->id
        ]);
    }

    /** @test */
    public function tour_operator_can_create_tour()
    {
        $response = $this->actingAs($this->tourOperator)
            ->postJson('/api/v1/tours', [
                'name' => 'Test Tour',
                'description' => 'Test Description',
                'location' => 'Test Location',
                'start_date' => now()->addDays(1),
                'end_date' => now()->addDays(2),
                'price' => 99.99
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Tour created successfully'
            ]);

        $this->assertDatabaseHas('tours', [
            'name' => 'Test Tour',
            'user_id' => $this->tourOperator->id
        ]);
    }

    /** @test */
    public function tour_operator_cannot_create_tour_with_invalid_data()
    {
        $response = $this->actingAs($this->tourOperator)
            ->postJson('/api/v1/tours', [
                'name' => '',
                'description' => '',
                'location' => '',
                'start_date' => now()->subDay(),
                'end_date' => now()->subDays(2),
                'price' => -1
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function tour_operator_can_update_own_tour()
    {
        $response = $this->actingAs($this->tourOperator)
            ->putJson("/api/v1/tours/{$this->tour->id}", [
                'name' => 'Updated Tour Name',
                'description' => 'Updated Description',
                'location' => 'Updated Location',
                'start_date' => now()->addDays(3),
                'end_date' => now()->addDays(4),
                'price' => 149.99
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Tour updated successfully'
            ]);

        $this->assertDatabaseHas('tours', [
            'id' => $this->tour->id,
            'name' => 'Updated Tour Name'
        ]);
    }

    /** @test */
    public function tour_operator_cannot_update_others_tour()
    {
        $response = $this->actingAs($this->anotherTourOperator)
            ->putJson("/api/v1/tours/{$this->tour->id}", [
                'name' => 'Updated Tour Name'
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_any_tour()
    {
        $response = $this->actingAs($this->admin)
            ->putJson("/api/v1/tours/{$this->tour->id}", [
                'name' => 'Admin Updated Tour'
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Tour updated successfully'
            ]);
    }

    /** @test */
    public function tour_operator_can_delete_own_tour()
    {
        $response = $this->actingAs($this->tourOperator)
            ->deleteJson("/api/v1/tours/{$this->tour->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Tour deleted successfully'
            ]);

        $this->assertDatabaseMissing('tours', [
            'id' => $this->tour->id
        ]);
    }



    /** @test */
    public function admin_can_delete_any_tour()
    {
        $response = $this->actingAs($this->admin)
            ->deleteJson("/api/v1/tours/{$this->tour->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Tour deleted successfully'
            ]);
    }

}
