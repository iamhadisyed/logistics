<?php

namespace Tests\Feature\Api;

use App\Models\Carrier;
use App\Models\Country;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShipmentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Country $country;
    protected Carrier $carrier;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->user = User::factory()->create();
        // Country's $fillable doesn't cover all NOT NULL legacy columns
        // (e.g. printable_name), so forceFill to satisfy the real schema.
        $this->country = new Country();
        $this->country->forceFill([
            'iso' => 'GB',
            'name' => 'United Kingdom',
            'printable_name' => 'United Kingdom',
            'has_postcodeq' => 'Y',
            'has_subzonesq' => 'N',
            'orderq' => 1,
            'added_on' => now(),
            'added_by' => 'test',
            'changed_on' => now(),
            'changed_by' => 'test',
            'currency_id' => 1,
        ])->save();

        $this->carrier = Carrier::factory()->create();

        // Service::factory() hits the known-unresolved max_weight/tracking_flag
        // schema gap (see MODULE_COMPLETION_TRACKER.md) — forceFill only the
        // columns confirmed to exist in the real services migration instead
        // of guessing at the ambiguous ones.
        $this->service = new Service();
        $this->service->forceFill([
            'carrier_id' => $this->carrier->id,
            'name' => 'Standard',
            'code' => 'STD',
            'type' => 'D',
            'from_weight' => 0,
            'to_weight' => 30,
            'fuel_surcharge' => 0,
            'fuel_surcharge_type' => 'p',
            'max_length' => 100,
            'max_width' => 100,
            'max_height' => 100,
        ])->save();
    }

    protected function validPayload(): array
    {
        return [
            'consignment' => [
                'customer_id' => 148,
                'service_type' => 'Standard',
                'carrier_id' => $this->carrier->id,
                'service_id' => $this->service->id,
                'warehouse_id' => 1,
                'reference' => 'HAWB-TEST-001',
                'notes' => 'Test shipment',
                'company' => 'Acme Ltd',
                'contact' => 'Jane Doe',
                'email' => 'jane@example.com',
                'telephone' => '01234567890',
                'address_line_1' => '1 Test Street',
                'city' => 'London',
                'postcode' => 'SW1A 1AA',
                'country_id' => $this->country->id,
            ],
            'parcels' => [
                [
                    'weight' => 2.5,
                    'length' => 20,
                    'width' => 15,
                    'height' => 10,
                    'items' => [
                        ['description' => 'Widget', 'quantity' => 2, 'weight' => 1.0, 'value' => 25.00],
                    ],
                ],
            ],
        ];
    }

    /** @test */
    public function it_can_create_a_shipment_with_parcels_and_items()
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/shipments', $this->validPayload());

        $response->assertStatus(201)
            ->assertJsonPath('data.reference', 'HAWB-TEST-001')
            ->assertJsonPath('data.status', 'booked')
            ->assertJsonPath('data.label_generated', false);

        $this->assertDatabaseHas('shipments', ['reference' => 'HAWB-TEST-001']);
        $shipmentId = $response->json('data.id');
        $this->assertDatabaseHas('shipment_parcels', ['shipment_id' => $shipmentId]);
        $this->assertDatabaseHas('shipment_items', ['description' => 'Widget', 'quantity' => 2]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $payload = $this->validPayload();
        unset($payload['consignment']['reference']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/shipments', $payload);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_can_list_shipments()
    {
        $this->actingAs($this->user, 'sanctum')->postJson('/api/shipments', $this->validPayload());

        $response = $this->actingAs($this->user, 'sanctum')->getJson('/api/shipments');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function it_can_search_shipments_by_reference()
    {
        $this->actingAs($this->user, 'sanctum')->postJson('/api/shipments', $this->validPayload());

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/shipments?search=HAWB-TEST-001');

        $response->assertStatus(200)->assertJsonCount(1, 'data');

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/shipments?search=NO-MATCH');

        $response->assertStatus(200)->assertJsonCount(0, 'data');
    }

    /** @test */
    public function it_can_view_a_shipment_with_its_parcels_and_items()
    {
        $createResponse = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/shipments', $this->validPayload());
        $shipmentId = $createResponse->json('data.id');

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/shipments/{$shipmentId}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $shipmentId)
            ->assertJsonPath('data.reference', 'HAWB-TEST-001');
    }

    /** @test */
    public function it_can_generate_a_label_for_a_booked_shipment()
    {
        $createResponse = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/shipments', $this->validPayload());
        $shipmentId = $createResponse->json('data.id');

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/shipments/{$shipmentId}/generate-label");

        $response->assertStatus(200)
            ->assertJsonPath('data.label_generated', true)
            ->assertJsonStructure(['label_url']);

        $this->assertDatabaseHas('shipments', [
            'id' => $shipmentId,
            'label_generated' => true,
            'status' => 'label_generated',
        ]);

        // The label must be a real, non-empty PDF file on disk — not just a
        // boolean flip. Confirms the fake stub was actually replaced.
        $shipment = Shipment::find($shipmentId);
        $this->assertNotNull($shipment->label_path);
        Storage::disk('local')->assertExists($shipment->label_path);
        $contents = Storage::disk('local')->get($shipment->label_path);
        $this->assertStringStartsWith('%PDF', $contents);
    }

    /** @test */
    public function generated_label_can_be_downloaded()
    {
        $createResponse = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/shipments', $this->validPayload());
        $shipmentId = $createResponse->json('data.id');

        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/shipments/{$shipmentId}/generate-label")
            ->assertStatus(200);

        $response = $this->actingAs($this->user, 'sanctum')
            ->get("/api/shipments/{$shipmentId}/label");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    /** @test */
    public function it_refuses_to_generate_a_label_twice()
    {
        $createResponse = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/shipments', $this->validPayload());
        $shipmentId = $createResponse->json('data.id');

        $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/shipments/{$shipmentId}/generate-label")
            ->assertStatus(200);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/shipments/{$shipmentId}/generate-label");

        $response->assertStatus(422);
    }

    /** @test */
    public function full_booking_to_label_flow_works_end_to_end()
    {
        // 1. Save Booking (no label yet)
        $createResponse = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/shipments', $this->validPayload());
        $createResponse->assertStatus(201)->assertJsonPath('data.label_generated', false);
        $shipmentId = $createResponse->json('data.id');

        // 2. It shows up in the list, not yet label-generated
        $listResponse = $this->actingAs($this->user, 'sanctum')->getJson('/api/shipments');
        $listResponse->assertStatus(200)->assertJsonPath('data.0.label_generated', false);

        // 3. Generate Label
        $labelResponse = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/shipments/{$shipmentId}/generate-label");
        $labelResponse->assertStatus(200);

        // 4. Details reflect the generated label
        $showResponse = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/shipments/{$shipmentId}");
        $showResponse->assertStatus(200)
            ->assertJsonPath('data.label_generated', true)
            ->assertJsonPath('data.status', 'label_generated');
    }
}
