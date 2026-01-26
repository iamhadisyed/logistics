<?php

namespace Tests\Unit\Services;

use App\Services\PricingEngine;
use App\Models\Consignment;
use App\Models\Service;
use App\Models\ConsignmentCharge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingEngineTest extends TestCase
{
    use RefreshDatabase;

    protected PricingEngine $pricingEngine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pricingEngine = new PricingEngine();
    }

    /** @test */
    public function it_calculates_base_price()
    {
        $service = Service::factory()->create([
            'fuel_surcharge' => 0
        ]);

        $consignment = Consignment::factory()->create([
            'service_id' => $service->id,
            'weight' => 5.0,
            'vol_weight' => 3.0,
            'remote_charges' => false,
            'is_insured' => false,
        ]);

        $result = $this->pricingEngine->calculatePrice($consignment);

        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('breakdown', $result);
        $this->assertIsArray($result['breakdown']);
    }

    /** @test */
    public function it_applies_fuel_surcharge()
    {
        $service = Service::factory()->create([
            'fuel_surcharge' => 15.0 // 15%
        ]);

        $consignment = Consignment::factory()->create([
            'service_id' => $service->id,
            'weight' => 5.0,
            'remote_charges' => false,
            'is_insured' => false,
        ]);

        $result = $this->pricingEngine->calculatePrice($consignment);

        $fuelCharge = collect($result['breakdown'])->firstWhere('type', ConsignmentCharge::TYPE_FUEL_SURCHARGE);

        $this->assertNotNull($fuelCharge);
        $this->assertStringContainsString('15%', $fuelCharge['description']);
    }

    /** @test */
    public function it_applies_remote_area_surcharge()
    {
        $service = Service::factory()->create();

        $consignment = Consignment::factory()->create([
            'service_id' => $service->id,
            'weight' => 5.0,
            'remote_charges' => true,
            'is_insured' => false,
        ]);

        $result = $this->pricingEngine->calculatePrice($consignment);

        $remoteCharge = collect($result['breakdown'])->firstWhere('type', ConsignmentCharge::TYPE_REMOTE_AREA);

        $this->assertNotNull($remoteCharge);
    }

    /** @test */
    public function it_applies_insurance_fee()
    {
        $service = Service::factory()->create();

        $consignment = Consignment::factory()->create([
            'service_id' => $service->id,
            'weight' => 5.0,
            'value' => 1000.00,
            'remote_charges' => false,
            'is_insured' => true,
        ]);

        $result = $this->pricingEngine->calculatePrice($consignment);

        $insuranceCharge = collect($result['breakdown'])->firstWhere('type', ConsignmentCharge::TYPE_INSURANCE);

        $this->assertNotNull($insuranceCharge);
        // 2% of 1000 = 20
        $this->assertEquals(20.0, $insuranceCharge['amount']);
    }

    /** @test */
    public function it_saves_charges_to_database()
    {
        $service = Service::factory()->create([
            'fuel_surcharge' => 10.0
        ]);

        $consignment = Consignment::factory()->create([
            'service_id' => $service->id,
            'weight' => 5.0,
        ]);

        $breakdown = [
            [
                'type' => ConsignmentCharge::TYPE_BASE_RATE,
                'amount' => 10.00,
                'description' => 'Base rate'
            ],
            [
                'type' => ConsignmentCharge::TYPE_FUEL_SURCHARGE,
                'amount' => 1.00,
                'description' => 'Fuel surcharge'
            ]
        ];

        $this->pricingEngine->saveCharges($consignment, $breakdown);

        $this->assertDatabaseHas('consignment_charges', [
            'consignment_id' => $consignment->id,
            'charge_type' => ConsignmentCharge::TYPE_BASE_RATE,
            'amount' => 10.00,
        ]);

        $this->assertDatabaseHas('consignment_charges', [
            'consignment_id' => $consignment->id,
            'charge_type' => ConsignmentCharge::TYPE_FUEL_SURCHARGE,
            'amount' => 1.00,
        ]);
    }

    /** @test */
    public function it_deletes_existing_charges_before_saving_new_ones()
    {
        $consignment = Consignment::factory()->create();

        // Create existing charges
        ConsignmentCharge::create([
            'consignment_id' => $consignment->id,
            'charge_type' => ConsignmentCharge::TYPE_BASE_RATE,
            'amount' => 5.00,
            'currency' => 'GBP',
        ]);

        $breakdown = [
            [
                'type' => ConsignmentCharge::TYPE_BASE_RATE,
                'amount' => 10.00,
                'description' => 'New base rate'
            ]
        ];

        $this->pricingEngine->saveCharges($consignment, $breakdown);

        $charges = ConsignmentCharge::where('consignment_id', $consignment->id)->get();

        $this->assertCount(1, $charges);
        $this->assertEquals(10.00, $charges->first()->amount);
    }
}
