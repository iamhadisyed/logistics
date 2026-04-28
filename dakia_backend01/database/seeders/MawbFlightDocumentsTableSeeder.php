<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MawbFlightDocumentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('mawb_flight_documents')->delete();
        
        \DB::table('mawb_flight_documents')->insert(array (
            0 => 
            array (
                'id' => 1,
                'document_name' => 'ExportSAManifest',
            ),
            1 => 
            array (
                'id' => 1,
                'document_name' => 'ExportSAManifest',
            ),
            2 => 
            array (
                'id' => 1,
                'document_name' => 'ExportSAManifest',
            ),
            3 => 
            array (
                'id' => 1,
                'document_name' => 'ExportSAManifest',
            ),
            4 => 
            array (
                'id' => 1,
                'document_name' => 'ExportSAManifest',
            ),
        ));
        
        
    }
}