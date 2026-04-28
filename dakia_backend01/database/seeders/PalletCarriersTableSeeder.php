<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PalletCarriersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('pallet_carriers')->delete();
        
        \DB::table('pallet_carriers')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'ROYAL MAIL TRACKED 48',
                'service_id' => '12',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'ROYAL MAIL UNTRACKED',
                'service_id' => '14, 15',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'YODEL',
                'service_id' => '1, 2, 3, 4, 38',
            ),
            3 => 
            array (
                'id' => 1,
                'name' => 'ROYAL MAIL TRACKED 48',
                'service_id' => '12',
            ),
            4 => 
            array (
                'id' => 2,
                'name' => 'ROYAL MAIL UNTRACKED',
                'service_id' => '14, 15',
            ),
            5 => 
            array (
                'id' => 3,
                'name' => 'YODEL',
                'service_id' => '1, 2, 3, 4, 38',
            ),
            6 => 
            array (
                'id' => 1,
                'name' => 'ROYAL MAIL TRACKED 48',
                'service_id' => '12',
            ),
            7 => 
            array (
                'id' => 2,
                'name' => 'ROYAL MAIL UNTRACKED',
                'service_id' => '14, 15',
            ),
            8 => 
            array (
                'id' => 3,
                'name' => 'YODEL',
                'service_id' => '1, 2, 3, 4, 38',
            ),
            9 => 
            array (
                'id' => 1,
                'name' => 'ROYAL MAIL TRACKED 48',
                'service_id' => '12',
            ),
            10 => 
            array (
                'id' => 2,
                'name' => 'ROYAL MAIL UNTRACKED',
                'service_id' => '14, 15',
            ),
            11 => 
            array (
                'id' => 3,
                'name' => 'YODEL',
                'service_id' => '1, 2, 3, 4, 38',
            ),
            12 => 
            array (
                'id' => 1,
                'name' => 'ROYAL MAIL TRACKED 48',
                'service_id' => '12',
            ),
            13 => 
            array (
                'id' => 2,
                'name' => 'ROYAL MAIL UNTRACKED',
                'service_id' => '14, 15',
            ),
            14 => 
            array (
                'id' => 3,
                'name' => 'YODEL',
                'service_id' => '1, 2, 3, 4, 38',
            ),
        ));
        
        
    }
}