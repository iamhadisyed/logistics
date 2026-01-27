<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DeutschepostdhlCargoCodesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('deutschepostdhl_cargo_codes')->delete();
        
        \DB::table('deutschepostdhl_cargo_codes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'start_postcode' => 1000,
                'end_postcode' => 1999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
            1 => 
            array (
                'id' => 2,
                'start_postcode' => 2000,
                'end_postcode' => 2999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
            2 => 
            array (
                'id' => 3,
                'start_postcode' => 3000,
                'end_postcode' => 3999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
            3 => 
            array (
                'id' => 1,
                'start_postcode' => 1000,
                'end_postcode' => 1999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
            4 => 
            array (
                'id' => 2,
                'start_postcode' => 2000,
                'end_postcode' => 2999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
            5 => 
            array (
                'id' => 3,
                'start_postcode' => 3000,
                'end_postcode' => 3999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
            6 => 
            array (
                'id' => 1,
                'start_postcode' => 1000,
                'end_postcode' => 1999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
            7 => 
            array (
                'id' => 2,
                'start_postcode' => 2000,
                'end_postcode' => 2999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
            8 => 
            array (
                'id' => 3,
                'start_postcode' => 3000,
                'end_postcode' => 3999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
            9 => 
            array (
                'id' => 1,
                'start_postcode' => 1000,
                'end_postcode' => 1999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
            10 => 
            array (
                'id' => 2,
                'start_postcode' => 2000,
                'end_postcode' => 2999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
            11 => 
            array (
                'id' => 3,
                'start_postcode' => 3000,
                'end_postcode' => 3999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
            12 => 
            array (
                'id' => 1,
                'start_postcode' => 1000,
                'end_postcode' => 1999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
            13 => 
            array (
                'id' => 2,
                'start_postcode' => 2000,
                'end_postcode' => 2999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
            14 => 
            array (
                'id' => 3,
                'start_postcode' => 3000,
                'end_postcode' => 3999,
                'cargo_code' => 1,
                'municipality_name' => 'Ottendorf-Okrilla',
            ),
        ));
        
        
    }
}