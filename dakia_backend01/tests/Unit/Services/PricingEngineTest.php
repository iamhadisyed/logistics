<?php

namespace Tests\Unit\Services;

use App\Exceptions\TariffNotConfiguredException;
use App\Models\Carrier;
use App\Models\CarrierZone;
use App\Models\CarrierZoneCountry;
use App\Models\Country;
use App\Models\Service;
use App\Models\Shipment;
use App\Models\ShipmentCharge;
use App\Models\Tariff;
use App\Models\TariffDetail;
use App\Services\PricingEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingEngineTest extends TestCase
{
    use RefreshDatabase;

    protected PricingEngine $pricingEngine;
    protected Carrier $carrier;
    protected Service $service;
    protected Country $country;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pricingEngine = new PricingEngine();

        $this->carrier = Carrier::factory()->create();

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
            'volumetric_denominator' => 5000,
        ])->save();

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
    }

    /**
     * Wire up a full zone -> tariff -> weight-banded rate chain, matching
     * how the real legacy schema links these tables.
     */
    protected function seedTariff(int $weightFrom, int $weightTo, float $weightCost, int $zoneId = 1, int $tariffId = 1): void
    {
        $zone = new CarrierZone();
        $zone->forceFill([
            'id' => $zoneId,
            'carrier_id' => $this->carrier->id,
            'name' => 'Zone A',
            'status' => true,
        ])->save();

        $zoneCountry = new CarrierZoneCountry();
        $zoneCountry->forceFill([
            'id' => $zoneId,
            'country_id' => $this->country->id,
            'carrier_zone_id' => $zone->id,
        ])->save();

        $tariff = new Tariff();
        $tariff->forceFill([
            'id' => $tariffId,
            'user_account_id' => 148,
            'carrier_id' => $this->carrier->id,
            'service_id' => $this->service->id,
            'tariff_type' => 'customer',
            'status' => true,
        ])->save();

        $detail = new TariffDetail();
        $detail->forceFill([
            'id' => $weightFrom + $weightTo, // arbitrary unique-ish id
            'tariffs_id' => $tariff->id,
            'from_zone_id' => 0,
            'to_zone_id' => $zone->id,
            'weight_from' => $weightFrom,
            'weight_to' => $weightTo,
            'weight_cost' => $weightCost,
            'piece_cost' => 0,
        ])->save();
    }

    protected function makeShipment(float $parcelWeight = 5.0, array $overrides = []): Shipment
    {
        $shipment = Shipment::create(array_merge([
            'customer_id' => 148,
            'service_type' => $this->service->name,
            'carrier_id' => $this->carrier->id,
            'service_id' => $this->service->id,
            'reference' => 'HAWB-PRICE-TEST',
            'company' => 'Acme Ltd',
            'address_line_1' => '1 Test Street',
            'city' => 'London',
            'postcode' => 'SW1A 1AA',
            'country_id' => $this->country->id,
        ], $overrides));

        $shipment->parcels()->create([
            'weight' => $parcelWeight,
            'length' => 10,
            'width' => 10,
            'height' => 10,
        ]);

        return $shipment->fresh(['parcels', 'service', 'carrier']);
    }

    /** @test */
    public function it_calculates_chargeable_weight_as_max_of_actual_and_volumetric()
    {
        // Volumetric = 50*50*50 / 5000 = 25kg > actual 5kg
        $shipment = $this->makeShipment(5.0);
        $shipment->parcels->first()->update(['length' => 50, 'width' => 50, 'height' => 50]);
        $shipment->refresh()->load('parcels');

        $this->assertEquals(25.0, $shipment->getChargeableWeight());
    }

    /** @test */
    public function it_throws_when_no_tariff_is_configured()
    {
        $shipment = $this->makeShipment(5.0);

        $this->expectException(TariffNotConfiguredException::class);
        $this->pricingEngine->calculatePrice($shipment);
    }

    /** @test */
    public function it_calculates_a_real_base_price_from_a_weight_banded_tariff()
    {
        $this->seedTariff(weightFrom: 0, weightTo: 10, weightCost: 12.50);
        $shipment = $this->makeShipment(5.0);

        $result = $this->pricingEngine->calculatePrice($shipment);

        $this->assertEquals(12.50, $result['total']);
        $baseCharge = collect($result['breakdown'])->firstWhere('type', ShipmentCharge::TYPE_BASE_RATE);
        $this->assertEquals(12.50, $baseCharge['amount']);
    }

    /** @test */
    public function it_picks_the_correct_weight_band()
    {
        $this->seedTariff(weightFrom: 0, weightTo: 10, weightCost: 12.50, zoneId: 1, tariffId: 1);
        // Add a second, heavier band on the SAME tariff/zone.
        $heavyDetail = new TariffDetail();
        $heavyDetail->forceFill([
            'id' => 999,
            'tariffs_id' => 1,
            'from_zone_id' => 0,
            'to_zone_id' => 1,
            'weight_from' => 10.01,
            'weight_to' => 30,
            'weight_cost' => 22.00,
            'piece_cost' => 0,
        ])->save();

        $lightShipment = $this->makeShipment(5.0);
        $heavyShipment = $this->makeShipment(15.0, ['reference' => 'HAWB-PRICE-TEST-2']);

        $this->assertEquals(12.50, $this->pricingEngine->calculatePrice($lightShipment)['total']);
        $this->assertEquals(22.00, $this->pricingEngine->calculatePrice($heavyShipment)['total']);
    }

    /** @test */
    public function it_applies_fuel_surcharge_on_top_of_base_rate()
    {
        $this->seedTariff(weightFrom: 0, weightTo: 10, weightCost: 100.00);
        $this->service->update(['fuel_surcharge' => 15.0]); // 15%

        $shipment = $this->makeShipment(5.0);
        $result = $this->pricingEngine->calculatePrice($shipment);

        $fuelCharge = collect($result['breakdown'])->firstWhere('type', ShipmentCharge::TYPE_FUEL_SURCHARGE);
        $this->assertNotNull($fuelCharge);
        $this->assertEquals(15.00, $fuelCharge['amount']); // 15% of 100
        $this->assertEquals(115.00, $result['total']);
    }

    /** @test */
    public function it_saves_charges_to_the_database_replacing_previous_ones()
    {
        $this->seedTariff(weightFrom: 0, weightTo: 10, weightCost: 12.50);
        $shipment = $this->makeShipment(5.0);

        $result = $this->pricingEngine->calculatePrice($shipment);
        $this->pricingEngine->saveCharges($shipment, $result['breakdown']);

        $this->assertDatabaseHas('shipment_charges', [
            'shipment_id' => $shipment->id,
            'charge_type' => ShipmentCharge::TYPE_BASE_RATE,
            'amount' => 12.50,
        ]);

        // Re-saving replaces rather than duplicates.
        $this->pricingEngine->saveCharges($shipment, $result['breakdown']);
        $this->assertEquals(1, ShipmentCharge::where('shipment_id', $shipment->id)->count());
    }
}
