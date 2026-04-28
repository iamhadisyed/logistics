<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BulletinsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('bulletins')->delete();
        
        \DB::table('bulletins')->insert(array (
            0 => 
            array (
                'id' => 1,
                'heading' => 'Test Bulletin',
                'description' => 'This is **_test_** description
s
d
dd
',
                'date_created' => '2018-11-18 15:55:13',
                'date_submitted' => '2018-11-12 00:00:00',
                'created_by' => '148',
            ),
            1 => 
            array (
                'id' => 2,
                'heading' => 'test bulletins',
                'description' => 'test
asd

sdf
af
sfa',
                'date_created' => '2018-11-18 15:54:56',
                'date_submitted' => '1970-01-01 00:00:00',
                'created_by' => '148',
            ),
            2 => 
            array (
                'id' => 3,
                'heading' => 'sddsffs',
                'description' => 'asdasda',
                'date_created' => '2019-08-29 10:20:48',
                'date_submitted' => '1970-01-01 00:00:00',
                'created_by' => '',
            ),
            3 => 
            array (
                'id' => 1,
                'heading' => 'Test Bulletin',
                'description' => 'This is **_test_** description
s
d
dd
',
                'date_created' => '2018-11-18 15:55:13',
                'date_submitted' => '2018-11-12 00:00:00',
                'created_by' => '148',
            ),
            4 => 
            array (
                'id' => 2,
                'heading' => 'test bulletins',
                'description' => 'test
asd

sdf
af
sfa',
                'date_created' => '2018-11-18 15:54:56',
                'date_submitted' => '1970-01-01 00:00:00',
                'created_by' => '148',
            ),
            5 => 
            array (
                'id' => 3,
                'heading' => 'sddsffs',
                'description' => 'asdasda',
                'date_created' => '2019-08-29 10:20:48',
                'date_submitted' => '1970-01-01 00:00:00',
                'created_by' => '',
            ),
            6 => 
            array (
                'id' => 1,
                'heading' => 'Test Bulletin',
                'description' => 'This is **_test_** description
s
d
dd
',
                'date_created' => '2018-11-18 15:55:13',
                'date_submitted' => '2018-11-12 00:00:00',
                'created_by' => '148',
            ),
            7 => 
            array (
                'id' => 2,
                'heading' => 'test bulletins',
                'description' => 'test
asd

sdf
af
sfa',
                'date_created' => '2018-11-18 15:54:56',
                'date_submitted' => '1970-01-01 00:00:00',
                'created_by' => '148',
            ),
            8 => 
            array (
                'id' => 3,
                'heading' => 'sddsffs',
                'description' => 'asdasda',
                'date_created' => '2019-08-29 10:20:48',
                'date_submitted' => '1970-01-01 00:00:00',
                'created_by' => '',
            ),
            9 => 
            array (
                'id' => 1,
                'heading' => 'Test Bulletin',
                'description' => 'This is **_test_** description
s
d
dd
',
                'date_created' => '2018-11-18 15:55:13',
                'date_submitted' => '2018-11-12 00:00:00',
                'created_by' => '148',
            ),
            10 => 
            array (
                'id' => 2,
                'heading' => 'test bulletins',
                'description' => 'test
asd

sdf
af
sfa',
                'date_created' => '2018-11-18 15:54:56',
                'date_submitted' => '1970-01-01 00:00:00',
                'created_by' => '148',
            ),
            11 => 
            array (
                'id' => 3,
                'heading' => 'sddsffs',
                'description' => 'asdasda',
                'date_created' => '2019-08-29 10:20:48',
                'date_submitted' => '1970-01-01 00:00:00',
                'created_by' => '',
            ),
            12 => 
            array (
                'id' => 1,
                'heading' => 'Test Bulletin',
                'description' => 'This is **_test_** description
s
d
dd
',
                'date_created' => '2018-11-18 15:55:13',
                'date_submitted' => '2018-11-12 00:00:00',
                'created_by' => '148',
            ),
            13 => 
            array (
                'id' => 2,
                'heading' => 'test bulletins',
                'description' => 'test
asd

sdf
af
sfa',
                'date_created' => '2018-11-18 15:54:56',
                'date_submitted' => '1970-01-01 00:00:00',
                'created_by' => '148',
            ),
            14 => 
            array (
                'id' => 3,
                'heading' => 'sddsffs',
                'description' => 'asdasda',
                'date_created' => '2019-08-29 10:20:48',
                'date_submitted' => '1970-01-01 00:00:00',
                'created_by' => '',
            ),
        ));
        
        
    }
}