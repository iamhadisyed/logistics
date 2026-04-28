<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CarriersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('carriers')->delete();
        
        \DB::table('carriers')->insert(array (
            0 => 
            array (
                'id' => 16,
                'carrier' => 'Amazon',
                'logo' => 'amazon.png',
                'cut_off_time' => '18:00',
                'carrier_display_name' => 'Amazon',
                'status' => 1,
                'country_id' => 225,
                'carrier_id' => NULL,
                'currency_code' => 'GBP',
                'remotearea_check' => 'c',
                'zone_base' => 0,
                'zone_type' => 'country',
                'on_contract' => 1,
                'is_gazetteer' => 1,
                'is_reconcile' => 0,
            ),
            1 => 
            array (
                'id' => 17,
                'carrier' => 'yodel',
                'logo' => NULL,
                'cut_off_time' => '4',
                'carrier_display_name' => 'jjg',
                'status' => 1,
                'country_id' => 255,
                'carrier_id' => NULL,
                'currency_code' => 'GBP',
                'remotearea_check' => 'c',
                'zone_base' => 0,
                'zone_type' => 'country',
                'on_contract' => 0,
                'is_gazetteer' => 0,
                'is_reconcile' => 0,
            ),
        ));
        
        
    }
}