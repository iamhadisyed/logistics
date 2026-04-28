<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RemoteareaChargesServicesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('remotearea_charges_services')->delete();
        
        \DB::table('remotearea_charges_services')->insert(array (
            0 => 
            array (
                'id' => 3,
                'remotearea_group_id' => 16,
                'service_id' => 252,
                'remotearea_charges' => '5.00',
                'from_weight' => '0.00',
                'to_weight' => '30.00',
                'formulla' => NULL,
                'is_deleted' => 'N',
                'added_by' => 58,
                'added_date' => '2018-01-18 12:24:45',
                'updated_by' => NULL,
                'updated_date' => NULL,
            ),
            1 => 
            array (
                'id' => 3,
                'remotearea_group_id' => 16,
                'service_id' => 252,
                'remotearea_charges' => '5.00',
                'from_weight' => '0.00',
                'to_weight' => '30.00',
                'formulla' => NULL,
                'is_deleted' => 'N',
                'added_by' => 58,
                'added_date' => '2018-01-18 12:24:45',
                'updated_by' => NULL,
                'updated_date' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'remotearea_group_id' => 16,
                'service_id' => 252,
                'remotearea_charges' => '5.00',
                'from_weight' => '0.00',
                'to_weight' => '30.00',
                'formulla' => NULL,
                'is_deleted' => 'N',
                'added_by' => 58,
                'added_date' => '2018-01-18 12:24:45',
                'updated_by' => NULL,
                'updated_date' => NULL,
            ),
            3 => 
            array (
                'id' => 3,
                'remotearea_group_id' => 16,
                'service_id' => 252,
                'remotearea_charges' => '5.00',
                'from_weight' => '0.00',
                'to_weight' => '30.00',
                'formulla' => NULL,
                'is_deleted' => 'N',
                'added_by' => 58,
                'added_date' => '2018-01-18 12:24:45',
                'updated_by' => NULL,
                'updated_date' => NULL,
            ),
            4 => 
            array (
                'id' => 3,
                'remotearea_group_id' => 16,
                'service_id' => 252,
                'remotearea_charges' => '5.00',
                'from_weight' => '0.00',
                'to_weight' => '30.00',
                'formulla' => NULL,
                'is_deleted' => 'N',
                'added_by' => 58,
                'added_date' => '2018-01-18 12:24:45',
                'updated_by' => NULL,
                'updated_date' => NULL,
            ),
        ));
        
        
    }
}