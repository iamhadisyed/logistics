<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TariffDetailsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tariff_details')->delete();
        
        \DB::table('tariff_details')->insert(array (
            0 => 
            array (
                'id' => 8,
                'tariff_name' => 'jaimin test',
                'status' => 0,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            1 => 
            array (
                'id' => 9,
                'tariff_name' => 'YODELMINIOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            2 => 
            array (
                'id' => 10,
                'tariff_name' => 'YODEL1HOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            3 => 
            array (
                'id' => 11,
                'tariff_name' => 'YODEL3HOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            4 => 
            array (
                'id' => 12,
                'tariff_name' => 'YODEL24M',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-08 18:01:28',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            5 => 
            array (
                'id' => 13,
                'tariff_name' => 'OWE_DHL_DOM',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-01-01 00:00:00',
                'end_date' => '2017-09-30 00:00:00',
                'date_created' => '2017-02-08 23:33:20',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            6 => 
            array (
                'id' => 14,
                'tariff_name' => 'TEST_TAHIR',
                'status' => 0,
                'tariff_type' => 'COST',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-10 17:04:31',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            7 => 
            array (
                'id' => 15,
                'tariff_name' => 'OWECH_YODEL_SEP2016',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-03-03 21:45:16',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            8 => 
            array (
                'id' => 8,
                'tariff_name' => 'jaimin test',
                'status' => 0,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            9 => 
            array (
                'id' => 9,
                'tariff_name' => 'YODELMINIOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            10 => 
            array (
                'id' => 10,
                'tariff_name' => 'YODEL1HOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            11 => 
            array (
                'id' => 11,
                'tariff_name' => 'YODEL3HOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            12 => 
            array (
                'id' => 12,
                'tariff_name' => 'YODEL24M',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-08 18:01:28',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            13 => 
            array (
                'id' => 13,
                'tariff_name' => 'OWE_DHL_DOM',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-01-01 00:00:00',
                'end_date' => '2017-09-30 00:00:00',
                'date_created' => '2017-02-08 23:33:20',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            14 => 
            array (
                'id' => 14,
                'tariff_name' => 'TEST_TAHIR',
                'status' => 0,
                'tariff_type' => 'COST',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-10 17:04:31',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            15 => 
            array (
                'id' => 15,
                'tariff_name' => 'OWECH_YODEL_SEP2016',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-03-03 21:45:16',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            16 => 
            array (
                'id' => 8,
                'tariff_name' => 'jaimin test',
                'status' => 0,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            17 => 
            array (
                'id' => 9,
                'tariff_name' => 'YODELMINIOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            18 => 
            array (
                'id' => 10,
                'tariff_name' => 'YODEL1HOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            19 => 
            array (
                'id' => 11,
                'tariff_name' => 'YODEL3HOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            20 => 
            array (
                'id' => 12,
                'tariff_name' => 'YODEL24M',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-08 18:01:28',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            21 => 
            array (
                'id' => 13,
                'tariff_name' => 'OWE_DHL_DOM',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-01-01 00:00:00',
                'end_date' => '2017-09-30 00:00:00',
                'date_created' => '2017-02-08 23:33:20',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            22 => 
            array (
                'id' => 14,
                'tariff_name' => 'TEST_TAHIR',
                'status' => 0,
                'tariff_type' => 'COST',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-10 17:04:31',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            23 => 
            array (
                'id' => 15,
                'tariff_name' => 'OWECH_YODEL_SEP2016',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-03-03 21:45:16',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            24 => 
            array (
                'id' => 8,
                'tariff_name' => 'jaimin test',
                'status' => 0,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            25 => 
            array (
                'id' => 9,
                'tariff_name' => 'YODELMINIOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            26 => 
            array (
                'id' => 10,
                'tariff_name' => 'YODEL1HOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            27 => 
            array (
                'id' => 11,
                'tariff_name' => 'YODEL3HOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            28 => 
            array (
                'id' => 12,
                'tariff_name' => 'YODEL24M',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-08 18:01:28',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            29 => 
            array (
                'id' => 13,
                'tariff_name' => 'OWE_DHL_DOM',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-01-01 00:00:00',
                'end_date' => '2017-09-30 00:00:00',
                'date_created' => '2017-02-08 23:33:20',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            30 => 
            array (
                'id' => 14,
                'tariff_name' => 'TEST_TAHIR',
                'status' => 0,
                'tariff_type' => 'COST',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-10 17:04:31',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            31 => 
            array (
                'id' => 15,
                'tariff_name' => 'OWECH_YODEL_SEP2016',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-03-03 21:45:16',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            32 => 
            array (
                'id' => 8,
                'tariff_name' => 'jaimin test',
                'status' => 0,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            33 => 
            array (
                'id' => 9,
                'tariff_name' => 'YODELMINIOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            34 => 
            array (
                'id' => 10,
                'tariff_name' => 'YODEL1HOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            35 => 
            array (
                'id' => 11,
                'tariff_name' => 'YODEL3HOWECHINA',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-07 16:31:41',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            36 => 
            array (
                'id' => 12,
                'tariff_name' => 'YODEL24M',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-08 18:01:28',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            37 => 
            array (
                'id' => 13,
                'tariff_name' => 'OWE_DHL_DOM',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-01-01 00:00:00',
                'end_date' => '2017-09-30 00:00:00',
                'date_created' => '2017-02-08 23:33:20',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            38 => 
            array (
                'id' => 14,
                'tariff_name' => 'TEST_TAHIR',
                'status' => 0,
                'tariff_type' => 'COST',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-02-10 17:04:31',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
            39 => 
            array (
                'id' => 15,
                'tariff_name' => 'OWECH_YODEL_SEP2016',
                'status' => 1,
                'tariff_type' => 'CHARGE',
                'start_date' => '2017-02-01 00:00:00',
                'end_date' => '2017-02-28 00:00:00',
                'date_created' => '2017-03-03 21:45:16',
                'added_by' => 0,
                'currency' => 'GBP',
            ),
        ));
        
        
    }
}