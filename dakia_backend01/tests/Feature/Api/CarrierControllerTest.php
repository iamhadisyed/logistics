<?php

namespace Tests\Feature\Api;

use App\Models\Carrier;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarrierControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_list_carriers()
    {
        Carrier::factory()->count(5)->create(['status' => Carrier::STATUS_ACTIVE]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/carriers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'carrier', 'status', 'services']
            ]);
    }

    /** @test */
    public function it_can_filter_active_carriers_only()
    {
        Carrier::factory()->count(3)->create(['status' => Carrier::STATUS_ACTIVE]);
        Carrier::factory()->count(2)->create(['status' => Carrier::STATUS_INACTIVE]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/carriers?active_only=1');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json());
    }

    /** @test */
    public function it_can_show_a_carrier_with_services()
    {
        $carrier = Carrier::factory()->create();
        Service::factory()->count(3)->create([
            'carrier_id' => $carrier->id,
            'active' => true
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/carriers/{$carrier->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $carrier->id,
                'carrier' => $carrier->carrier,
            ])
            ->assertJsonCount(3, 'services');
    }

    /** @test */
    public function it_can_create_a_carrier()
    {
        $data = [
            'carrier' => 'DHL',
            'carrier_display_name' => 'DHL Express',
            'country_id' => 1,
            'currency_code' => 'GBP',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/carriers', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('carrier', [
            'carrier' => 'DHL',
            'status' => Carrier::STATUS_ACTIVE,
        ]);
    }

    /** @test */
    public function it_can_update_a_carrier()
    {
        $carrier = Carrier::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/carriers/{$carrier->id}", [
                'carrier_display_name' => 'Updated Name',
                'cut_off_time' => '17:00',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('carrier', [
            'id' => $carrier->id,
            'carrier_display_name' => 'Updated Name',
            'cut_off_time' => '17:00',
        ]);
    }

    /** @test */
    public function it_can_activate_a_carrier()
    {
        $carrier = Carrier::factory()->create(['status' => Carrier::STATUS_INACTIVE]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/carriers/{$carrier->id}/status", [
                'status' => 'active'
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('carrier', [
            'id' => $carrier->id,
            'status' => Carrier::STATUS_ACTIVE,
        ]);
    }

    /** @test */
    public function it_can_deactivate_a_carrier_and_cascade_to_services()
    {
        $carrier = Carrier::factory()->create(['status' => Carrier::STATUS_ACTIVE]);
        
        $service1 = Service::factory()->create([
            'carrier_id' => $carrier->id,
            'active' => true
        ]);
        
        $service2 = Service::factory()->create([
            'carrier_id' => $carrier->id,
            'active' => true
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/carriers/{$carrier->id}/status", [
                'status' => 'inactive'
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Carrier and all its services deactivated successfully'
            ]);

        $this->assertDatabaseHas('carrier', [
            'id' => $carrier->id,
            'status' => Carrier::STATUS_INACTIVE,
        ]);

        $this->assertDatabaseHas('services', [
            'id' => $service1->id,
            'active' => false,
        ]);

        $this->assertDatabaseHas('services', [
            'id' => $service2->id,
            'active' => false,
        ]);
    }

    /** @test */
    public function it_can_delete_a_carrier()
    {
        $carrier = Carrier::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/carriers/{$carrier->id}/status", [
                'status' => 'delete'
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('carrier', [
            'id' => $carrier->id,
            'status' => Carrier::STATUS_DELETED,
        ]);
    }
}
