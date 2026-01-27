<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RemoteareaChargesCarriersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('remotearea_charges_carriers')->delete();
        
        \DB::table('remotearea_charges_carriers')->insert(array (
            0 => 
            array (
                'id' => 1,
                'remotearea_group_id' => NULL,
                'remotearea_charges' => NULL,
                'is_deleted' => 'N',
                'added_by' => 58,
                'added_date' => '2019-09-02 16:05:55',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            1 => 
            array (
                'id' => 1,
                'remotearea_group_id' => NULL,
                'remotearea_charges' => NULL,
                'is_deleted' => 'N',
                'added_by' => 58,
                'added_date' => '2019-09-02 16:05:55',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            2 => 
            array (
                'id' => 1,
                'remotearea_group_id' => NULL,
                'remotearea_charges' => NULL,
                'is_deleted' => 'N',
                'added_by' => 58,
                'added_date' => '2019-09-02 16:05:55',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            3 => 
            array (
                'id' => 1,
                'remotearea_group_id' => NULL,
                'remotearea_charges' => NULL,
                'is_deleted' => 'N',
                'added_by' => 58,
                'added_date' => '2019-09-02 16:05:55',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            4 => 
            array (
                'id' => 1,
                'remotearea_group_id' => NULL,
                'remotearea_charges' => NULL,
                'is_deleted' => 'N',
                'added_by' => 58,
                'added_date' => '2019-09-02 16:05:55',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
        ));
        
        
    }
}