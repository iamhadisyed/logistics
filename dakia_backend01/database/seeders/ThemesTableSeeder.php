<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ThemesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('themes')->delete();
        
        \DB::table('themes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Handlerbund',
                'slug' => 'hnd',
                'style_sheet' => '',
                'dashboard_template' => '',
                'is_active' => 0,
                'created_by' => 58,
                'created_at' => '2020-01-10 01:00:02',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Ukmail',
                'slug' => 'ukmail',
                'style_sheet' => NULL,
                'dashboard_template' => 'ukm',
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:48:48',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Deutshce Post',
                'slug' => 'deutchepost',
                'style_sheet' => 'custom.ukmail.min.css',
                'dashboard_template' => 'ukm',
                'is_active' => 1,
                'created_by' => 58,
                'created_at' => '2020-01-09 23:48:50',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Viva Express',
                'slug' => 'viva',
                'style_sheet' => NULL,
                'dashboard_template' => NULL,
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:49:01',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Spar',
                'slug' => 'spar',
                'style_sheet' => NULL,
                'dashboard_template' => NULL,
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:49:09',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'RTClear',
                'slug' => 'rtcl',
                'style_sheet' => '',
                'dashboard_template' => '',
                'is_active' => 1,
                'created_by' => 58,
                'created_at' => '2020-01-10 00:59:21',
            ),
            6 => 
            array (
                'id' => 1,
                'name' => 'Handlerbund',
                'slug' => 'hnd',
                'style_sheet' => '',
                'dashboard_template' => '',
                'is_active' => 0,
                'created_by' => 58,
                'created_at' => '2020-01-10 01:00:02',
            ),
            7 => 
            array (
                'id' => 2,
                'name' => 'Ukmail',
                'slug' => 'ukmail',
                'style_sheet' => NULL,
                'dashboard_template' => 'ukm',
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:48:48',
            ),
            8 => 
            array (
                'id' => 3,
                'name' => 'Deutshce Post',
                'slug' => 'deutchepost',
                'style_sheet' => 'custom.ukmail.min.css',
                'dashboard_template' => 'ukm',
                'is_active' => 1,
                'created_by' => 58,
                'created_at' => '2020-01-09 23:48:50',
            ),
            9 => 
            array (
                'id' => 4,
                'name' => 'Viva Express',
                'slug' => 'viva',
                'style_sheet' => NULL,
                'dashboard_template' => NULL,
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:49:01',
            ),
            10 => 
            array (
                'id' => 5,
                'name' => 'Spar',
                'slug' => 'spar',
                'style_sheet' => NULL,
                'dashboard_template' => NULL,
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:49:09',
            ),
            11 => 
            array (
                'id' => 6,
                'name' => 'RTClear',
                'slug' => 'rtcl',
                'style_sheet' => '',
                'dashboard_template' => '',
                'is_active' => 1,
                'created_by' => 58,
                'created_at' => '2020-01-10 00:59:21',
            ),
            12 => 
            array (
                'id' => 1,
                'name' => 'Handlerbund',
                'slug' => 'hnd',
                'style_sheet' => '',
                'dashboard_template' => '',
                'is_active' => 0,
                'created_by' => 58,
                'created_at' => '2020-01-10 01:00:02',
            ),
            13 => 
            array (
                'id' => 2,
                'name' => 'Ukmail',
                'slug' => 'ukmail',
                'style_sheet' => NULL,
                'dashboard_template' => 'ukm',
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:48:48',
            ),
            14 => 
            array (
                'id' => 3,
                'name' => 'Deutshce Post',
                'slug' => 'deutchepost',
                'style_sheet' => 'custom.ukmail.min.css',
                'dashboard_template' => 'ukm',
                'is_active' => 1,
                'created_by' => 58,
                'created_at' => '2020-01-09 23:48:50',
            ),
            15 => 
            array (
                'id' => 4,
                'name' => 'Viva Express',
                'slug' => 'viva',
                'style_sheet' => NULL,
                'dashboard_template' => NULL,
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:49:01',
            ),
            16 => 
            array (
                'id' => 5,
                'name' => 'Spar',
                'slug' => 'spar',
                'style_sheet' => NULL,
                'dashboard_template' => NULL,
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:49:09',
            ),
            17 => 
            array (
                'id' => 6,
                'name' => 'RTClear',
                'slug' => 'rtcl',
                'style_sheet' => '',
                'dashboard_template' => '',
                'is_active' => 1,
                'created_by' => 58,
                'created_at' => '2020-01-10 00:59:21',
            ),
            18 => 
            array (
                'id' => 1,
                'name' => 'Handlerbund',
                'slug' => 'hnd',
                'style_sheet' => '',
                'dashboard_template' => '',
                'is_active' => 0,
                'created_by' => 58,
                'created_at' => '2020-01-10 01:00:02',
            ),
            19 => 
            array (
                'id' => 2,
                'name' => 'Ukmail',
                'slug' => 'ukmail',
                'style_sheet' => NULL,
                'dashboard_template' => 'ukm',
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:48:48',
            ),
            20 => 
            array (
                'id' => 3,
                'name' => 'Deutshce Post',
                'slug' => 'deutchepost',
                'style_sheet' => 'custom.ukmail.min.css',
                'dashboard_template' => 'ukm',
                'is_active' => 1,
                'created_by' => 58,
                'created_at' => '2020-01-09 23:48:50',
            ),
            21 => 
            array (
                'id' => 4,
                'name' => 'Viva Express',
                'slug' => 'viva',
                'style_sheet' => NULL,
                'dashboard_template' => NULL,
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:49:01',
            ),
            22 => 
            array (
                'id' => 5,
                'name' => 'Spar',
                'slug' => 'spar',
                'style_sheet' => NULL,
                'dashboard_template' => NULL,
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:49:09',
            ),
            23 => 
            array (
                'id' => 6,
                'name' => 'RTClear',
                'slug' => 'rtcl',
                'style_sheet' => '',
                'dashboard_template' => '',
                'is_active' => 1,
                'created_by' => 58,
                'created_at' => '2020-01-10 00:59:21',
            ),
            24 => 
            array (
                'id' => 1,
                'name' => 'Handlerbund',
                'slug' => 'hnd',
                'style_sheet' => '',
                'dashboard_template' => '',
                'is_active' => 0,
                'created_by' => 58,
                'created_at' => '2020-01-10 01:00:02',
            ),
            25 => 
            array (
                'id' => 2,
                'name' => 'Ukmail',
                'slug' => 'ukmail',
                'style_sheet' => NULL,
                'dashboard_template' => 'ukm',
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:48:48',
            ),
            26 => 
            array (
                'id' => 3,
                'name' => 'Deutshce Post',
                'slug' => 'deutchepost',
                'style_sheet' => 'custom.ukmail.min.css',
                'dashboard_template' => 'ukm',
                'is_active' => 1,
                'created_by' => 58,
                'created_at' => '2020-01-09 23:48:50',
            ),
            27 => 
            array (
                'id' => 4,
                'name' => 'Viva Express',
                'slug' => 'viva',
                'style_sheet' => NULL,
                'dashboard_template' => NULL,
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:49:01',
            ),
            28 => 
            array (
                'id' => 5,
                'name' => 'Spar',
                'slug' => 'spar',
                'style_sheet' => NULL,
                'dashboard_template' => NULL,
                'is_active' => 1,
                'created_by' => 148,
                'created_at' => '2020-01-09 23:49:09',
            ),
            29 => 
            array (
                'id' => 6,
                'name' => 'RTClear',
                'slug' => 'rtcl',
                'style_sheet' => '',
                'dashboard_template' => '',
                'is_active' => 1,
                'created_by' => 58,
                'created_at' => '2020-01-10 00:59:21',
            ),
        ));
        
        
    }
}