<?php

namespace Tests\Unit\Models;

use App\Models\Carrier;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarrierTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_carrier()
    {
        $carrier = Carrier::create([
            'carrier' => 'DHL',
            'carrier_display_name' => 'DHL Express',
            'status' => Carrier::STATUS_ACTIVE,
        ]);

        $this->assertDatabaseHas('carrier', [
            'carrier' => 'DHL',
            'status' => Carrier::STATUS_ACTIVE,
        ]);
    }

    /** @test */
    public function it_can_deactivate_carrier_and_cascade_to_services()
    {
        $carrier = Carrier::factory()->create([
            'status' => Carrier::STATUS_ACTIVE
        ]);

        $service1 = Service::factory()->create([
            'carrier_id' => $carrier->id,
            'active' => true
        ]);

        $service2 = Service::factory()->create([
            'carrier_id' => $carrier->id,
            'active' => true
        ]);

        $carrier->deactivate();

        $this->assertEquals(Carrier::STATUS_INACTIVE, $carrier->status);
        $this->assertFalse($service1->fresh()->active);
        $this->assertFalse($service2->fresh()->active);
    }

    /** @test */
    public function it_can_activate_carrier()
    {
        $carrier = Carrier::factory()->create([
            'status' => Carrier::STATUS_INACTIVE
        ]);

        $carrier->activate();

        $this->assertEquals(Carrier::STATUS_ACTIVE, $carrier->status);
    }

    /** @test */
    public function it_can_scope_active_carriers()
    {
        Carrier::factory()->create(['status' => Carrier::STATUS_ACTIVE]);
        Carrier::factory()->create(['status' => Carrier::STATUS_INACTIVE]);
        Carrier::factory()->create(['status' => Carrier::STATUS_DELETED]);

        $activeCarriers = Carrier::active()->get();

        $this->assertCount(1, $activeCarriers);
    }

    /** @test */
    public function it_can_scope_not_deleted_carriers()
    {
        Carrier::factory()->create(['status' => Carrier::STATUS_ACTIVE]);
        Carrier::factory()->create(['status' => Carrier::STATUS_INACTIVE]);
        Carrier::factory()->create(['status' => Carrier::STATUS_DELETED]);

        $notDeleted = Carrier::notDeleted()->get();

        $this->assertCount(2, $notDeleted);
    }

    /** @test */
    public function it_has_many_services()
    {
        $carrier = Carrier::factory()->create();
        Service::factory()->count(3)->create(['carrier_id' => $carrier->id]);

        $this->assertCount(3, $carrier->services);
    }

    /** @test */
    public function it_can_have_parent_carrier()
    {
        $parent = Carrier::factory()->create(['carrier' => 'DHL']);
        $child = Carrier::factory()->create([
            'carrier' => 'DHL UK',
            'carrier_id' => $parent->id
        ]);

        $this->assertEquals($parent->id, $child->parentCarrier->id);
    }

    /** @test */
    public function it_can_have_sub_carriers()
    {
        $parent = Carrier::factory()->create();
        Carrier::factory()->count(2)->create(['carrier_id' => $parent->id]);

        $this->assertCount(2, $parent->subCarriers);
    }
}
