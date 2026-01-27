<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PalletBagRemoveReasonsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('pallet_bag_remove_reasons')->delete();
        
        \DB::table('pallet_bag_remove_reasons')->insert(array (
            0 => 
            array (
                'id' => 1,
                'pallet_id' => 3934,
                'bag_id' => 10263,
                'reason' => 'Testing',
            ),
            1 => 
            array (
                'id' => 1,
                'pallet_id' => 3934,
                'bag_id' => 10263,
                'reason' => 'Testing',
            ),
            2 => 
            array (
                'id' => 1,
                'pallet_id' => 3934,
                'bag_id' => 10263,
                'reason' => 'Testing',
            ),
            3 => 
            array (
                'id' => 1,
                'pallet_id' => 3934,
                'bag_id' => 10263,
                'reason' => 'Testing',
            ),
            4 => 
            array (
                'id' => 1,
                'pallet_id' => 3934,
                'bag_id' => 10263,
                'reason' => 'Testing',
            ),
        ));
        
        
    }
}