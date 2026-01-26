<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\Carrier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'carrier_id' => Carrier::factory(),
            'name' => $this->faker->words(3, true),
            'code' => strtoupper($this->faker->unique()->lexify('???###')),
            'carrier_service_code' => $this->faker->lexify('???###'),
            'type' => $this->faker->randomElement(['D', 'I', 'E', 'R']),
            'active' => true,
            'from_weight' => 0,
            'to_weight' => 30,
            'origin_country' => 1,
            'delivery_mode' => 'Standard',
            'label_class_name' => 'TestLabel',
            'wieght_type' => 1,
            'is_customized' => false,
            'is_remotearea' => 'N',
            'volumetric_denominator' => 5000,
            'fuel_surcharge' => $this->faker->randomFloat(2, 0, 20),
            'validation_type' => 'courier',
            'zone_type' => 'country',
            'tariff_type' => 'single',
            'pre_sort' => 'NO',
            'is_untrack' => false,
            'is_eori_required' => false,
            'delivery_type' => 'all',
            'max_weight' => 30,
            'tracking_flag' => true,
            'insurance_available' => true,
        ];
    }
}
