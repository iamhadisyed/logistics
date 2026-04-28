<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LanguagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('languages')->delete();
        
        \DB::table('languages')->insert(array (
            0 => 
            array (
                'id' => 1,
                'language' => 'en-GB',
                'date_created' => '2016-07-12 00:00:00',
                'created_by' => NULL,
                'is_active' => 'Y',
            ),
            1 => 
            array (
                'id' => 2,
                'language' => 'de-DE',
                'date_created' => '2016-07-12 00:00:00',
                'created_by' => NULL,
                'is_active' => 'Y',
            ),
            2 => 
            array (
                'id' => 3,
                'language' => 'en-US',
                'date_created' => '2016-07-12 23:57:28',
                'created_by' => 189,
                'is_active' => 'Y',
            ),
            3 => 
            array (
                'id' => 4,
                'language' => 'fr-FR',
                'date_created' => '2016-07-12 23:58:38',
                'created_by' => 189,
                'is_active' => 'Y',
            ),
            4 => 
            array (
                'id' => 1,
                'language' => 'en-GB',
                'date_created' => '2016-07-12 00:00:00',
                'created_by' => NULL,
                'is_active' => 'Y',
            ),
            5 => 
            array (
                'id' => 2,
                'language' => 'de-DE',
                'date_created' => '2016-07-12 00:00:00',
                'created_by' => NULL,
                'is_active' => 'Y',
            ),
            6 => 
            array (
                'id' => 3,
                'language' => 'en-US',
                'date_created' => '2016-07-12 23:57:28',
                'created_by' => 189,
                'is_active' => 'Y',
            ),
            7 => 
            array (
                'id' => 4,
                'language' => 'fr-FR',
                'date_created' => '2016-07-12 23:58:38',
                'created_by' => 189,
                'is_active' => 'Y',
            ),
            8 => 
            array (
                'id' => 1,
                'language' => 'en-GB',
                'date_created' => '2016-07-12 00:00:00',
                'created_by' => NULL,
                'is_active' => 'Y',
            ),
            9 => 
            array (
                'id' => 2,
                'language' => 'de-DE',
                'date_created' => '2016-07-12 00:00:00',
                'created_by' => NULL,
                'is_active' => 'Y',
            ),
            10 => 
            array (
                'id' => 3,
                'language' => 'en-US',
                'date_created' => '2016-07-12 23:57:28',
                'created_by' => 189,
                'is_active' => 'Y',
            ),
            11 => 
            array (
                'id' => 4,
                'language' => 'fr-FR',
                'date_created' => '2016-07-12 23:58:38',
                'created_by' => 189,
                'is_active' => 'Y',
            ),
            12 => 
            array (
                'id' => 1,
                'language' => 'en-GB',
                'date_created' => '2016-07-12 00:00:00',
                'created_by' => NULL,
                'is_active' => 'Y',
            ),
            13 => 
            array (
                'id' => 2,
                'language' => 'de-DE',
                'date_created' => '2016-07-12 00:00:00',
                'created_by' => NULL,
                'is_active' => 'Y',
            ),
            14 => 
            array (
                'id' => 3,
                'language' => 'en-US',
                'date_created' => '2016-07-12 23:57:28',
                'created_by' => 189,
                'is_active' => 'Y',
            ),
            15 => 
            array (
                'id' => 4,
                'language' => 'fr-FR',
                'date_created' => '2016-07-12 23:58:38',
                'created_by' => 189,
                'is_active' => 'Y',
            ),
            16 => 
            array (
                'id' => 1,
                'language' => 'en-GB',
                'date_created' => '2016-07-12 00:00:00',
                'created_by' => NULL,
                'is_active' => 'Y',
            ),
            17 => 
            array (
                'id' => 2,
                'language' => 'de-DE',
                'date_created' => '2016-07-12 00:00:00',
                'created_by' => NULL,
                'is_active' => 'Y',
            ),
            18 => 
            array (
                'id' => 3,
                'language' => 'en-US',
                'date_created' => '2016-07-12 23:57:28',
                'created_by' => 189,
                'is_active' => 'Y',
            ),
            19 => 
            array (
                'id' => 4,
                'language' => 'fr-FR',
                'date_created' => '2016-07-12 23:58:38',
                'created_by' => 189,
                'is_active' => 'Y',
            ),
        ));
        
        
    }
}