<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ShipmentParcelsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('shipment_parcels')->delete();
        
        \DB::table('shipment_parcels')->insert(array (
            0 => 
            array (
                'id' => 8,
                'shipment_id' => 8,
                'weight' => '1.000',
                'length' => '1.00',
                'width' => '1.00',
                'height' => '1.00',
                'notes' => 'gggg',
                'created_at' => '2026-01-25 19:16:03',
                'updated_at' => '2026-01-25 19:16:03',
            ),
        ));
        
        
    }
}