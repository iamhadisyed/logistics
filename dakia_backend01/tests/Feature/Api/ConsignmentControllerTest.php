<?php

namespace Tests\Feature\Api;

use App\Models\Consignment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsignmentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_list_consignments()
    {
        Consignment::factory()->count(5)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/consignments');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'hawb', 'contact', 'weight']
                ]
            ]);
    }

    /** @test */
    public function it_can_filter_consignments_by_status()
    {
        Consignment::factory()->create([
            'user_id' => $this->user->id,
            'shipment_status' => Consignment::STATUS_NEW
        ]);

        Consignment::factory()->create([
            'user_id' => $this->user->id,
            'shipment_status' => Consignment::STATUS_READY_TO_PRINT
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/consignments?status=' . Consignment::STATUS_READY_TO_PRINT);

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    /** @test */
    public function it_can_create_a_consignment()
    {
        $service = Service::factory()->create();

        $data = [
            'service_id' => $service->id,
            'contact' => 'John Doe',
            'address_line_1' => '123 Test Street',
            'city' => 'London',
            'postcode' => 'SW1A 1AA',
            'country_id' => 1,
            'weight' => 2.5,
            'description' => 'Test package',
            'number_pieces' => 1,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/consignments', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => ['id', 'contact', 'weight']
            ]);

        $this->assertDatabaseHas('consignment', [
            'contact' => 'John Doe',
            'weight' => 2.5,
            'shipment_status' => Consignment::STATUS_READY_TO_PRINT,
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/consignments', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'service_id',
                'contact',
                'address_line_1',
                'city',
                'country_id',
                'weight',
                'description',
                'number_pieces'
            ]);
    }

    /** @test */
    public function it_can_show_a_consignment()
    {
        $consignment = Consignment::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/consignments/{$consignment->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $consignment->id,
                'contact' => $consignment->contact,
            ]);
    }

    /** @test */
    public function it_can_update_a_consignment()
    {
        $consignment = Consignment::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/consignments/{$consignment->id}", [
                'contact' => 'Updated Name',
                'weight' => 5.0,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('consignment', [
            'id' => $consignment->id,
            'contact' => 'Updated Name',
            'weight' => 5.0,
        ]);
    }

    /** @test */
    public function it_can_delete_a_consignment()
    {
        $consignment = Consignment::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/consignments/{$consignment->id}");

        $response->assertStatus(200);

        $this->assertDatabaseHas('consignment', [
            'id' => $consignment->id,
            'shipment_status' => Consignment::STATUS_RECYCLED,
        ]);
    }

    /** @test */
    public function it_can_bulk_update_consignments()
    {
        $consignments = Consignment::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'shipment_status' => Consignment::STATUS_NEW
        ]);

        $ids = $consignments->pluck('id')->toArray();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/consignments/bulk-update', [
                'consignment_ids' => $ids,
                'status' => Consignment::STATUS_READY_TO_PRINT
            ]);

        $response->assertStatus(200);

        foreach ($ids as $id) {
            $this->assertDatabaseHas('consignment', [
                'id' => $id,
                'shipment_status' => Consignment::STATUS_READY_TO_PRINT,
            ]);
        }
    }
}
