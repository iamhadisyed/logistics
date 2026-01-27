<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConsignmentHoldsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('consignment_holds')->delete();
        
        \DB::table('consignment_holds')->insert(array (
            0 => 
            array (
                'id' => 2,
                'userid' => NULL,
                'comments' => NULL,
                'tracking_number' => NULL,
                'date_created' => NULL,
                'action' => NULL,
                'reason_tag' => NULL,
                'weight' => NULL,
                'width' => NULL,
                'height' => NULL,
                'length' => NULL,
                'volume' => NULL,
                'image' => NULL,
                'account' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'userid' => NULL,
                'comments' => NULL,
                'tracking_number' => NULL,
                'date_created' => NULL,
                'action' => NULL,
                'reason_tag' => NULL,
                'weight' => NULL,
                'width' => NULL,
                'height' => NULL,
                'length' => NULL,
                'volume' => NULL,
                'image' => NULL,
                'account' => NULL,
            ),
            2 => 
            array (
                'id' => 2,
                'userid' => NULL,
                'comments' => NULL,
                'tracking_number' => NULL,
                'date_created' => NULL,
                'action' => NULL,
                'reason_tag' => NULL,
                'weight' => NULL,
                'width' => NULL,
                'height' => NULL,
                'length' => NULL,
                'volume' => NULL,
                'image' => NULL,
                'account' => NULL,
            ),
            3 => 
            array (
                'id' => 2,
                'userid' => NULL,
                'comments' => NULL,
                'tracking_number' => NULL,
                'date_created' => NULL,
                'action' => NULL,
                'reason_tag' => NULL,
                'weight' => NULL,
                'width' => NULL,
                'height' => NULL,
                'length' => NULL,
                'volume' => NULL,
                'image' => NULL,
                'account' => NULL,
            ),
            4 => 
            array (
                'id' => 2,
                'userid' => NULL,
                'comments' => NULL,
                'tracking_number' => NULL,
                'date_created' => NULL,
                'action' => NULL,
                'reason_tag' => NULL,
                'weight' => NULL,
                'width' => NULL,
                'height' => NULL,
                'length' => NULL,
                'volume' => NULL,
                'image' => NULL,
                'account' => NULL,
            ),
        ));
        
        
    }
}