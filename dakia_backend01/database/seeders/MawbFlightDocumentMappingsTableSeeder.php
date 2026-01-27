<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MawbFlightDocumentMappingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('mawb_flight_document_mappings')->delete();
        
        \DB::table('mawb_flight_document_mappings')->insert(array (
            0 => 
            array (
                'id' => 1,
                'country_id' => 197,
                'document_id' => 1,
                'template_id' => 1,
                'added_by' => 148,
                'added_date' => '2020-06-18 21:49:51',
            ),
            1 => 
            array (
                'id' => 1,
                'country_id' => 197,
                'document_id' => 1,
                'template_id' => 1,
                'added_by' => 148,
                'added_date' => '2020-06-18 21:49:51',
            ),
            2 => 
            array (
                'id' => 1,
                'country_id' => 197,
                'document_id' => 1,
                'template_id' => 1,
                'added_by' => 148,
                'added_date' => '2020-06-18 21:49:51',
            ),
            3 => 
            array (
                'id' => 1,
                'country_id' => 197,
                'document_id' => 1,
                'template_id' => 1,
                'added_by' => 148,
                'added_date' => '2020-06-18 21:49:51',
            ),
            4 => 
            array (
                'id' => 1,
                'country_id' => 197,
                'document_id' => 1,
                'template_id' => 1,
                'added_by' => 148,
                'added_date' => '2020-06-18 21:49:51',
            ),
        ));
        
        
    }
}