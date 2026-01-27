<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CsvTrackingTemplatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('csv_tracking_templates')->delete();
        
        \DB::table('csv_tracking_templates')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 2324,
                'user_account_id' => 2357,
                'template_name' => 'testtest',
                'template' => '{"Data Received":"Data Received","Arrived at Sort Facility Hayes - GBR":"Arrived at Sort Facility Hayes - GBR","Arrived at Sort Facility Hamburg - GBR":"Arrived at Sort Facility Hamburg - GBR","Departed Facility in Hamburg - GBR":"Departed Facility in Hamburg - GBR"}',
                'added_by' => 2324,
                'added_date' => '2020-03-17 16:05:04',
                'update_by' => 0,
                'update_date' => NULL,
                'service_id' => 226,
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 2329,
                'user_account_id' => 2361,
                'template_name' => 'TEST',
                'template' => '{"Data Received":"Track Point 1","Arrived at Sort Facility Hayes - GBR":"Track Point 2","Arrived at Sort Facility Birmingham3 - GBR":"Track Point 3","Arrived at Sort Facility Hamburg - GBR":"Track Point 4","Departed Facility in Hamburg - GBR":"Track Point 5","Returned at Sort Facility Hayes - GBR":"ZE3 9JX"}',
                'added_by' => 2329,
                'added_date' => '2020-03-17 16:30:37',
                'update_by' => 0,
                'update_date' => NULL,
                'service_id' => 2,
            ),
            2 => 
            array (
                'id' => 1,
                'user_id' => 2324,
                'user_account_id' => 2357,
                'template_name' => 'testtest',
                'template' => '{"Data Received":"Data Received","Arrived at Sort Facility Hayes - GBR":"Arrived at Sort Facility Hayes - GBR","Arrived at Sort Facility Hamburg - GBR":"Arrived at Sort Facility Hamburg - GBR","Departed Facility in Hamburg - GBR":"Departed Facility in Hamburg - GBR"}',
                'added_by' => 2324,
                'added_date' => '2020-03-17 16:05:04',
                'update_by' => 0,
                'update_date' => NULL,
                'service_id' => 226,
            ),
            3 => 
            array (
                'id' => 2,
                'user_id' => 2329,
                'user_account_id' => 2361,
                'template_name' => 'TEST',
                'template' => '{"Data Received":"Track Point 1","Arrived at Sort Facility Hayes - GBR":"Track Point 2","Arrived at Sort Facility Birmingham3 - GBR":"Track Point 3","Arrived at Sort Facility Hamburg - GBR":"Track Point 4","Departed Facility in Hamburg - GBR":"Track Point 5","Returned at Sort Facility Hayes - GBR":"ZE3 9JX"}',
                'added_by' => 2329,
                'added_date' => '2020-03-17 16:30:37',
                'update_by' => 0,
                'update_date' => NULL,
                'service_id' => 2,
            ),
            4 => 
            array (
                'id' => 1,
                'user_id' => 2324,
                'user_account_id' => 2357,
                'template_name' => 'testtest',
                'template' => '{"Data Received":"Data Received","Arrived at Sort Facility Hayes - GBR":"Arrived at Sort Facility Hayes - GBR","Arrived at Sort Facility Hamburg - GBR":"Arrived at Sort Facility Hamburg - GBR","Departed Facility in Hamburg - GBR":"Departed Facility in Hamburg - GBR"}',
                'added_by' => 2324,
                'added_date' => '2020-03-17 16:05:04',
                'update_by' => 0,
                'update_date' => NULL,
                'service_id' => 226,
            ),
            5 => 
            array (
                'id' => 2,
                'user_id' => 2329,
                'user_account_id' => 2361,
                'template_name' => 'TEST',
                'template' => '{"Data Received":"Track Point 1","Arrived at Sort Facility Hayes - GBR":"Track Point 2","Arrived at Sort Facility Birmingham3 - GBR":"Track Point 3","Arrived at Sort Facility Hamburg - GBR":"Track Point 4","Departed Facility in Hamburg - GBR":"Track Point 5","Returned at Sort Facility Hayes - GBR":"ZE3 9JX"}',
                'added_by' => 2329,
                'added_date' => '2020-03-17 16:30:37',
                'update_by' => 0,
                'update_date' => NULL,
                'service_id' => 2,
            ),
            6 => 
            array (
                'id' => 1,
                'user_id' => 2324,
                'user_account_id' => 2357,
                'template_name' => 'testtest',
                'template' => '{"Data Received":"Data Received","Arrived at Sort Facility Hayes - GBR":"Arrived at Sort Facility Hayes - GBR","Arrived at Sort Facility Hamburg - GBR":"Arrived at Sort Facility Hamburg - GBR","Departed Facility in Hamburg - GBR":"Departed Facility in Hamburg - GBR"}',
                'added_by' => 2324,
                'added_date' => '2020-03-17 16:05:04',
                'update_by' => 0,
                'update_date' => NULL,
                'service_id' => 226,
            ),
            7 => 
            array (
                'id' => 2,
                'user_id' => 2329,
                'user_account_id' => 2361,
                'template_name' => 'TEST',
                'template' => '{"Data Received":"Track Point 1","Arrived at Sort Facility Hayes - GBR":"Track Point 2","Arrived at Sort Facility Birmingham3 - GBR":"Track Point 3","Arrived at Sort Facility Hamburg - GBR":"Track Point 4","Departed Facility in Hamburg - GBR":"Track Point 5","Returned at Sort Facility Hayes - GBR":"ZE3 9JX"}',
                'added_by' => 2329,
                'added_date' => '2020-03-17 16:30:37',
                'update_by' => 0,
                'update_date' => NULL,
                'service_id' => 2,
            ),
            8 => 
            array (
                'id' => 1,
                'user_id' => 2324,
                'user_account_id' => 2357,
                'template_name' => 'testtest',
                'template' => '{"Data Received":"Data Received","Arrived at Sort Facility Hayes - GBR":"Arrived at Sort Facility Hayes - GBR","Arrived at Sort Facility Hamburg - GBR":"Arrived at Sort Facility Hamburg - GBR","Departed Facility in Hamburg - GBR":"Departed Facility in Hamburg - GBR"}',
                'added_by' => 2324,
                'added_date' => '2020-03-17 16:05:04',
                'update_by' => 0,
                'update_date' => NULL,
                'service_id' => 226,
            ),
            9 => 
            array (
                'id' => 2,
                'user_id' => 2329,
                'user_account_id' => 2361,
                'template_name' => 'TEST',
                'template' => '{"Data Received":"Track Point 1","Arrived at Sort Facility Hayes - GBR":"Track Point 2","Arrived at Sort Facility Birmingham3 - GBR":"Track Point 3","Arrived at Sort Facility Hamburg - GBR":"Track Point 4","Departed Facility in Hamburg - GBR":"Track Point 5","Returned at Sort Facility Hayes - GBR":"ZE3 9JX"}',
                'added_by' => 2329,
                'added_date' => '2020-03-17 16:30:37',
                'update_by' => 0,
                'update_date' => NULL,
                'service_id' => 2,
            ),
        ));
        
        
    }
}