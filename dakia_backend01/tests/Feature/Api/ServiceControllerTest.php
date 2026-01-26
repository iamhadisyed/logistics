<?php

namespace Tests\Feature\Api;

use App\Models\Service;
use App\Models\Carrier;
use App\Models\CustomizedServicesRouting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_list_services()
    {
        Service::factory()->count(5)->create(['active' => true]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/services');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'code', 'carrier']
            ]);
    }

    /** @test */
    public function it_can_filter_services_by_carrier()
    {
        $carrier = Carrier::factory()->create();
        Service::factory()->count(3)->create(['carrier_id' => $carrier->id, 'active' => true]);
        Service::factory()->count(2)->create(['active' => true]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/services?carrier_id={$carrier->id}");

        $response->assertStatus(200);
        $this->assertCount(3, $response->json());
    }

    /** @test */
    public function it_can_get_available_services_for_destination_and_weight()
    {
        $service = Service::factory()->create(['active' => true]);
        $carrier = Carrier::factory()->create();
        $service->carrier_id = $carrier->id;
        $service->save();

        // Create routing
        CustomizedServicesRouting::create([
            'customize_service_id' => $service->id,
            'service_id' => $service->id,
            'country_id' => 1,
            'from_weight' => 0,
            'to_weight' => 10,
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/services/available?origin_country=1&destination_country=1&weight=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'code', 'carrier_name']
            ]);
    }

    /** @test */
    public function it_can_show_a_service()
    {
        $service = Service::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/services/{$service->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $service->id,
                'name' => $service->name,
            ]);
    }

    /** @test */
    public function it_can_create_a_service()
    {
        $carrier = Carrier::factory()->create();

        $data = [
            'carrier_id' => $carrier->id,
            'name' => 'Test Service',
            'code' => 'TEST001',
            'label_class_name' => 'TestLabel',
            'type' => 'D',
            'from_weight' => 0,
            'to_weight' => 30,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/services', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('services', [
            'name' => 'Test Service',
            'code' => 'TEST001',
        ]);
    }

    /** @test */
    public function it_validates_unique_service_code()
    {
        $carrier = Carrier::factory()->create();
        Service::factory()->create(['code' => 'DUPLICATE']);

        $data = [
            'carrier_id' => $carrier->id,
            'name' => 'Test Service',
            'code' => 'DUPLICATE',
            'label_class_name' => 'TestLabel',
            'type' => 'D',
            'from_weight' => 0,
            'to_weight' => 30,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/services', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    /** @test */
    public function it_can_update_a_service()
    {
        $service = Service::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/services/{$service->id}", [
                'name' => 'Updated Service Name',
                'active' => false,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Updated Service Name',
            'active' => false,
        ]);
    }
}
