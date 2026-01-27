<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CarrierDocumentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('carrier_documents')->delete();
        
        \DB::table('carrier_documents')->insert(array (
            0 => 
            array (
                'id' => 3,
                'carrier_id' => 19,
                'document_id' => 7,
                'document_name' => '1508238498aramex.png',
                'added_by' => 148,
                'added_date' => '1970-01-01 00:00:17',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            1 => 
            array (
                'id' => 4,
                'carrier_id' => 19,
                'document_id' => 8,
                'document_name' => '1508238507ariborne.png',
                'added_by' => 148,
                'added_date' => '1970-01-01 00:00:17',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            2 => 
            array (
                'id' => 9,
                'carrier_id' => 175,
                'document_id' => 6,
                'document_name' => '15155078096744809.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 01:00:09',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            3 => 
            array (
                'id' => 10,
                'carrier_id' => 157,
                'document_id' => 6,
                'document_name' => '1562340375MDL_Booking_Platform_ITT_RFP_2019_V3.0_-_RELEASE.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:05',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            4 => 
            array (
                'id' => 11,
                'carrier_id' => 157,
                'document_id' => 7,
                'document_name' => '1562340521Account-Statement1551956409.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:05',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            5 => 
            array (
                'id' => 16,
                'carrier_id' => 16,
                'document_id' => 7,
                'document_name' => '1573579923nz-label.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            6 => 
            array (
                'id' => 18,
                'carrier_id' => 152,
                'document_id' => 7,
                'document_name' => '15735807511503_001.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            7 => 
            array (
                'id' => 19,
                'carrier_id' => 151,
                'document_id' => 6,
                'document_name' => '15735816031503_001.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            8 => 
            array (
                'id' => 20,
                'carrier_id' => 151,
                'document_id' => 7,
                'document_name' => '1573581631shipping-services-api-manual.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            9 => 
            array (
                'id' => 3,
                'carrier_id' => 19,
                'document_id' => 7,
                'document_name' => '1508238498aramex.png',
                'added_by' => 148,
                'added_date' => '1970-01-01 00:00:17',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            10 => 
            array (
                'id' => 4,
                'carrier_id' => 19,
                'document_id' => 8,
                'document_name' => '1508238507ariborne.png',
                'added_by' => 148,
                'added_date' => '1970-01-01 00:00:17',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            11 => 
            array (
                'id' => 9,
                'carrier_id' => 175,
                'document_id' => 6,
                'document_name' => '15155078096744809.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 01:00:09',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            12 => 
            array (
                'id' => 10,
                'carrier_id' => 157,
                'document_id' => 6,
                'document_name' => '1562340375MDL_Booking_Platform_ITT_RFP_2019_V3.0_-_RELEASE.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:05',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            13 => 
            array (
                'id' => 11,
                'carrier_id' => 157,
                'document_id' => 7,
                'document_name' => '1562340521Account-Statement1551956409.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:05',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            14 => 
            array (
                'id' => 16,
                'carrier_id' => 16,
                'document_id' => 7,
                'document_name' => '1573579923nz-label.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            15 => 
            array (
                'id' => 18,
                'carrier_id' => 152,
                'document_id' => 7,
                'document_name' => '15735807511503_001.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            16 => 
            array (
                'id' => 19,
                'carrier_id' => 151,
                'document_id' => 6,
                'document_name' => '15735816031503_001.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            17 => 
            array (
                'id' => 20,
                'carrier_id' => 151,
                'document_id' => 7,
                'document_name' => '1573581631shipping-services-api-manual.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            18 => 
            array (
                'id' => 3,
                'carrier_id' => 19,
                'document_id' => 7,
                'document_name' => '1508238498aramex.png',
                'added_by' => 148,
                'added_date' => '1970-01-01 00:00:17',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            19 => 
            array (
                'id' => 4,
                'carrier_id' => 19,
                'document_id' => 8,
                'document_name' => '1508238507ariborne.png',
                'added_by' => 148,
                'added_date' => '1970-01-01 00:00:17',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            20 => 
            array (
                'id' => 9,
                'carrier_id' => 175,
                'document_id' => 6,
                'document_name' => '15155078096744809.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 01:00:09',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            21 => 
            array (
                'id' => 10,
                'carrier_id' => 157,
                'document_id' => 6,
                'document_name' => '1562340375MDL_Booking_Platform_ITT_RFP_2019_V3.0_-_RELEASE.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:05',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            22 => 
            array (
                'id' => 11,
                'carrier_id' => 157,
                'document_id' => 7,
                'document_name' => '1562340521Account-Statement1551956409.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:05',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            23 => 
            array (
                'id' => 16,
                'carrier_id' => 16,
                'document_id' => 7,
                'document_name' => '1573579923nz-label.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            24 => 
            array (
                'id' => 18,
                'carrier_id' => 152,
                'document_id' => 7,
                'document_name' => '15735807511503_001.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            25 => 
            array (
                'id' => 19,
                'carrier_id' => 151,
                'document_id' => 6,
                'document_name' => '15735816031503_001.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            26 => 
            array (
                'id' => 20,
                'carrier_id' => 151,
                'document_id' => 7,
                'document_name' => '1573581631shipping-services-api-manual.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            27 => 
            array (
                'id' => 3,
                'carrier_id' => 19,
                'document_id' => 7,
                'document_name' => '1508238498aramex.png',
                'added_by' => 148,
                'added_date' => '1970-01-01 00:00:17',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            28 => 
            array (
                'id' => 4,
                'carrier_id' => 19,
                'document_id' => 8,
                'document_name' => '1508238507ariborne.png',
                'added_by' => 148,
                'added_date' => '1970-01-01 00:00:17',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            29 => 
            array (
                'id' => 9,
                'carrier_id' => 175,
                'document_id' => 6,
                'document_name' => '15155078096744809.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 01:00:09',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            30 => 
            array (
                'id' => 10,
                'carrier_id' => 157,
                'document_id' => 6,
                'document_name' => '1562340375MDL_Booking_Platform_ITT_RFP_2019_V3.0_-_RELEASE.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:05',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            31 => 
            array (
                'id' => 11,
                'carrier_id' => 157,
                'document_id' => 7,
                'document_name' => '1562340521Account-Statement1551956409.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:05',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            32 => 
            array (
                'id' => 16,
                'carrier_id' => 16,
                'document_id' => 7,
                'document_name' => '1573579923nz-label.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            33 => 
            array (
                'id' => 18,
                'carrier_id' => 152,
                'document_id' => 7,
                'document_name' => '15735807511503_001.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            34 => 
            array (
                'id' => 19,
                'carrier_id' => 151,
                'document_id' => 6,
                'document_name' => '15735816031503_001.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            35 => 
            array (
                'id' => 20,
                'carrier_id' => 151,
                'document_id' => 7,
                'document_name' => '1573581631shipping-services-api-manual.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            36 => 
            array (
                'id' => 3,
                'carrier_id' => 19,
                'document_id' => 7,
                'document_name' => '1508238498aramex.png',
                'added_by' => 148,
                'added_date' => '1970-01-01 00:00:17',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            37 => 
            array (
                'id' => 4,
                'carrier_id' => 19,
                'document_id' => 8,
                'document_name' => '1508238507ariborne.png',
                'added_by' => 148,
                'added_date' => '1970-01-01 00:00:17',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            38 => 
            array (
                'id' => 9,
                'carrier_id' => 175,
                'document_id' => 6,
                'document_name' => '15155078096744809.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 01:00:09',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            39 => 
            array (
                'id' => 10,
                'carrier_id' => 157,
                'document_id' => 6,
                'document_name' => '1562340375MDL_Booking_Platform_ITT_RFP_2019_V3.0_-_RELEASE.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:05',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            40 => 
            array (
                'id' => 11,
                'carrier_id' => 157,
                'document_id' => 7,
                'document_name' => '1562340521Account-Statement1551956409.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:05',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            41 => 
            array (
                'id' => 16,
                'carrier_id' => 16,
                'document_id' => 7,
                'document_name' => '1573579923nz-label.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            42 => 
            array (
                'id' => 18,
                'carrier_id' => 152,
                'document_id' => 7,
                'document_name' => '15735807511503_001.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            43 => 
            array (
                'id' => 19,
                'carrier_id' => 151,
                'document_id' => 6,
                'document_name' => '15735816031503_001.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
            44 => 
            array (
                'id' => 20,
                'carrier_id' => 151,
                'document_id' => 7,
                'document_name' => '1573581631shipping-services-api-manual.pdf',
                'added_by' => 58,
                'added_date' => '1970-01-01 00:00:12',
                'updated_by' => 0,
                'updated_date' => NULL,
            ),
        ));
        
        
    }
}