<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CsNotesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cs_notes')->delete();
        
        \DB::table('cs_notes')->insert(array (
            0 => 
            array (
                'id' => 1,
                'notes' => 'Test Notes',
                'created_by' => 2234,
                'date_created' => '2018-11-12 17:12:49',
            ),
            1 => 
            array (
                'id' => 2,
                'notes' => 'test notes',
                'created_by' => 148,
                'date_created' => '2019-08-29 10:21:08',
            ),
            2 => 
            array (
                'id' => 3,
                'notes' => 'The system needs some maintenance',
                'created_by' => 2349,
                'date_created' => '2019-12-11 13:40:07',
            ),
            3 => 
            array (
                'id' => 4,
                'notes' => '',
                'created_by' => 2326,
                'date_created' => '2020-01-27 17:59:31',
            ),
            4 => 
            array (
                'id' => 1,
                'notes' => 'Test Notes',
                'created_by' => 2234,
                'date_created' => '2018-11-12 17:12:49',
            ),
            5 => 
            array (
                'id' => 2,
                'notes' => 'test notes',
                'created_by' => 148,
                'date_created' => '2019-08-29 10:21:08',
            ),
            6 => 
            array (
                'id' => 3,
                'notes' => 'The system needs some maintenance',
                'created_by' => 2349,
                'date_created' => '2019-12-11 13:40:07',
            ),
            7 => 
            array (
                'id' => 4,
                'notes' => '',
                'created_by' => 2326,
                'date_created' => '2020-01-27 17:59:31',
            ),
            8 => 
            array (
                'id' => 1,
                'notes' => 'Test Notes',
                'created_by' => 2234,
                'date_created' => '2018-11-12 17:12:49',
            ),
            9 => 
            array (
                'id' => 2,
                'notes' => 'test notes',
                'created_by' => 148,
                'date_created' => '2019-08-29 10:21:08',
            ),
            10 => 
            array (
                'id' => 3,
                'notes' => 'The system needs some maintenance',
                'created_by' => 2349,
                'date_created' => '2019-12-11 13:40:07',
            ),
            11 => 
            array (
                'id' => 4,
                'notes' => '',
                'created_by' => 2326,
                'date_created' => '2020-01-27 17:59:31',
            ),
            12 => 
            array (
                'id' => 1,
                'notes' => 'Test Notes',
                'created_by' => 2234,
                'date_created' => '2018-11-12 17:12:49',
            ),
            13 => 
            array (
                'id' => 2,
                'notes' => 'test notes',
                'created_by' => 148,
                'date_created' => '2019-08-29 10:21:08',
            ),
            14 => 
            array (
                'id' => 3,
                'notes' => 'The system needs some maintenance',
                'created_by' => 2349,
                'date_created' => '2019-12-11 13:40:07',
            ),
            15 => 
            array (
                'id' => 4,
                'notes' => '',
                'created_by' => 2326,
                'date_created' => '2020-01-27 17:59:31',
            ),
            16 => 
            array (
                'id' => 1,
                'notes' => 'Test Notes',
                'created_by' => 2234,
                'date_created' => '2018-11-12 17:12:49',
            ),
            17 => 
            array (
                'id' => 2,
                'notes' => 'test notes',
                'created_by' => 148,
                'date_created' => '2019-08-29 10:21:08',
            ),
            18 => 
            array (
                'id' => 3,
                'notes' => 'The system needs some maintenance',
                'created_by' => 2349,
                'date_created' => '2019-12-11 13:40:07',
            ),
            19 => 
            array (
                'id' => 4,
                'notes' => '',
                'created_by' => 2326,
                'date_created' => '2020-01-27 17:59:31',
            ),
        ));
        
        
    }
}