<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ShipmentItemsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shipment_items')->delete();
        
        \DB::table('shipment_items')->insert(array (
            0 => 
            array (
                'id' => 8,
                'parcel_id' => 8,
                'description' => 'ttttt',
                'quantity' => 1,
                'weight' => '1.000',
                'value' => '2.00',
                'created_at' => '2026-01-25 19:16:03',
                'updated_at' => '2026-01-25 19:16:03',
            ),
        ));
        
        
    }
}