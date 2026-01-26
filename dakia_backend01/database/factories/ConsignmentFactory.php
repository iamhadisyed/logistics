<?php

namespace Database\Factories;

use App\Models\Consignment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsignmentFactory extends Factory
{
    protected $model = Consignment::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'service_id' => Service::factory(),
            'warehouse_id' => 1,
            'agent_id' => 1,
            'hawb' => 'HAWB' . $this->faker->unique()->numberBetween(100000, 999999),
            'awb' => 'AWB' . $this->faker->unique()->numberBetween(100000, 999999),
            'shipment_status' => Consignment::STATUS_NEW,
            'shipment_type' => 'PARCEL',
            'reference' => $this->faker->word(),
            'date_created' => now(),
            
            // Receiver address
            'company' => $this->faker->company(),
            'contact' => $this->faker->name(),
            'address_line_1' => $this->faker->streetAddress(),
            'address_line_2' => $this->faker->secondaryAddress(),
            'city' => $this->faker->city(),
            'state' => $this->faker->state(),
            'postcode' => $this->faker->postcode(),
            'country_id' => 1,
            'telephone' => $this->faker->phoneNumber(),
            'email' => $this->faker->safeEmail(),
            
            // Sender address
            'sender_company' => $this->faker->company(),
            'sender_contact' => $this->faker->name(),
            'sender_address_line_1' => $this->faker->streetAddress(),
            'sender_city' => $this->faker->city(),
            'sender_postcode' => $this->faker->postcode(),
            'sender_country_id' => 1,
            'sender_telephone' => $this->faker->phoneNumber(),
            'sender_email' => $this->faker->safeEmail(),
            
            // Parcel details
            'number_pieces' => $this->faker->numberBetween(1, 5),
            'weight' => $this->faker->randomFloat(2, 0.5, 30),
            'vol_weight' => $this->faker->randomFloat(2, 0.5, 30),
            'charge_weight' => $this->faker->randomFloat(2, 0.5, 30),
            'description' => $this->faker->sentence(),
            'value' => $this->faker->randomFloat(2, 10, 1000),
            'currency' => 'GBP',
            'is_doc' => false,
            
            // Special fields
            'remote_charges' => false,
            'is_insured' => false,
        ];
    }
}
