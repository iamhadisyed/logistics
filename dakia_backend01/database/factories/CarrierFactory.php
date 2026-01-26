<?php

namespace Database\Factories;

use App\Models\Carrier;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarrierFactory extends Factory
{
    protected $model = Carrier::class;

    public function definition(): array
    {
        return [
            'carrier' => $this->faker->company(),
            'carrier_display_name' => $this->faker->company() . ' Express',
            'logo' => $this->faker->word() . '.png',
            'status' => Carrier::STATUS_ACTIVE,
            'country_id' => 1,
            'currency_code' => 'GBP',
            'cut_off_time' => '17:00',
            'zone_base' => 0,
            'zone_type' => 'country',
            'remotearea_check' => 's',
            'is_gazetteer' => false,
            'is_reconcile' => false,
            'on_contract' => true,
            'is_pallet' => false,
        ];
    }
}
