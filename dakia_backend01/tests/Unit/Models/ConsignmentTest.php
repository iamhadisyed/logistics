<?php

namespace Tests\Unit\Models;

use App\Models\Consignment;
use App\Models\Service;
use App\Models\Country;
use App\Models\Parcel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsignmentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_consignment()
    {
        $consignment = Consignment::create([
            'user_id' => 1,
            'service_id' => 1,
            'contact' => 'John Doe',
            'address_line_1' => '123 Test Street',
            'city' => 'London',
            'postcode' => 'SW1A 1AA',
            'country_id' => 1,
            'weight' => 2.5,
            'description' => 'Test package',
            'number_pieces' => 1,
            'shipment_status' => Consignment::STATUS_NEW,
        ]);

        $this->assertDatabaseHas('consignment', [
            'contact' => 'John Doe',
            'weight' => 2.5,
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $consignment = new Consignment([
            'user_id' => 1,
            'service_id' => 1,
            'weight' => 0,
            'number_pieces' => 1,
        ]);

        $errors = $consignment->isValid();

        $this->assertContains('Receiver address line 1 is required', $errors);
        $this->assertContains('Receiver city is required', $errors);
        $this->assertContains('Receiver contact is required', $errors);
        $this->assertContains('Weight must be greater than 0', $errors);
        $this->assertContains('Description is required', $errors);
    }

    /** @test */
    public function it_validates_contact_length()
    {
        $consignment = new Consignment([
            'contact' => str_repeat('a', 36), // 36 characters
            'address_line_1' => '123 Test',
            'city' => 'London',
            'weight' => 1,
            'description' => 'Test',
            'number_pieces' => 1,
        ]);

        $errors = $consignment->isValid();

        $this->assertContains('Receiver contact must be 35 characters or less', $errors);
    }

    /** @test */
    public function it_validates_number_of_pieces()
    {
        $consignment = new Consignment([
            'contact' => 'John Doe',
            'address_line_1' => '123 Test',
            'city' => 'London',
            'weight' => 1,
            'description' => 'Test',
            'number_pieces' => 100,
        ]);

        $errors = $consignment->isValid();

        $this->assertContains('Number of pieces must be less than 100', $errors);
    }

    /** @test */
    public function it_calculates_volumetric_weight()
    {
        $service = Service::factory()->create([
            'volumetric_denominator' => 5000
        ]);

        $consignment = Consignment::factory()->create([
            'service_id' => $service->id
        ]);

        // Create parcels
        Parcel::create([
            'consignment_id' => $consignment->id,
            'length' => 50,
            'width' => 40,
            'height' => 30,
            'weight' => 2,
        ]);

        $volWeight = $consignment->calculateVolumetricWeight();

        // (50 * 40 * 30) / 5000 = 12
        $this->assertEquals(12.0, $volWeight);
    }

    /** @test */
    public function it_returns_chargeable_weight_as_max_of_actual_and_volumetric()
    {
        $service = Service::factory()->create([
            'volumetric_denominator' => 5000
        ]);

        $consignment = Consignment::factory()->create([
            'service_id' => $service->id,
            'weight' => 5.0,
            'vol_weight' => 12.0,
        ]);

        $chargeableWeight = $consignment->getChargeableWeight();

        $this->assertEquals(12.0, $chargeableWeight);
    }

    /** @test */
    public function it_has_status_constants()
    {
        $this->assertEquals(10, Consignment::STATUS_NEW);
        $this->assertEquals(12, Consignment::STATUS_READY_TO_PRINT);
        $this->assertEquals(13, Consignment::STATUS_LABEL_CREATED);
        $this->assertEquals(19, Consignment::STATUS_DELIVERED);
        $this->assertEquals(22, Consignment::STATUS_RECYCLED);
    }

    /** @test */
    public function it_can_scope_by_status()
    {
        Consignment::factory()->create(['shipment_status' => Consignment::STATUS_NEW]);
        Consignment::factory()->create(['shipment_status' => Consignment::STATUS_READY_TO_PRINT]);
        Consignment::factory()->create(['shipment_status' => Consignment::STATUS_READY_TO_PRINT]);

        $readyToPrint = Consignment::byStatus(Consignment::STATUS_READY_TO_PRINT)->get();

        $this->assertCount(2, $readyToPrint);
    }

    /** @test */
    public function it_can_scope_ready_to_print()
    {
        Consignment::factory()->create(['shipment_status' => Consignment::STATUS_NEW]);
        Consignment::factory()->create(['shipment_status' => Consignment::STATUS_READY_TO_PRINT]);

        $readyToPrint = Consignment::readyToPrint()->get();

        $this->assertCount(1, $readyToPrint);
    }
}
