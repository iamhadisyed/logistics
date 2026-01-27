<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserDepartmentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_departments')->delete();
        
        \DB::table('user_departments')->insert(array (
            0 => 
            array (
                'id' => 3,
                'user_id' => 2141,
                'department_id' => 1,
            ),
            1 => 
            array (
                'id' => 4,
                'user_id' => 2141,
                'department_id' => 3,
            ),
            2 => 
            array (
                'id' => 6,
                'user_id' => 2112,
                'department_id' => 1,
            ),
            3 => 
            array (
                'id' => 8,
                'user_id' => 2313,
                'department_id' => 1,
            ),
            4 => 
            array (
                'id' => 3,
                'user_id' => 2141,
                'department_id' => 1,
            ),
            5 => 
            array (
                'id' => 4,
                'user_id' => 2141,
                'department_id' => 3,
            ),
            6 => 
            array (
                'id' => 6,
                'user_id' => 2112,
                'department_id' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'user_id' => 2313,
                'department_id' => 1,
            ),
            8 => 
            array (
                'id' => 3,
                'user_id' => 2141,
                'department_id' => 1,
            ),
            9 => 
            array (
                'id' => 4,
                'user_id' => 2141,
                'department_id' => 3,
            ),
            10 => 
            array (
                'id' => 6,
                'user_id' => 2112,
                'department_id' => 1,
            ),
            11 => 
            array (
                'id' => 8,
                'user_id' => 2313,
                'department_id' => 1,
            ),
            12 => 
            array (
                'id' => 3,
                'user_id' => 2141,
                'department_id' => 1,
            ),
            13 => 
            array (
                'id' => 4,
                'user_id' => 2141,
                'department_id' => 3,
            ),
            14 => 
            array (
                'id' => 6,
                'user_id' => 2112,
                'department_id' => 1,
            ),
            15 => 
            array (
                'id' => 8,
                'user_id' => 2313,
                'department_id' => 1,
            ),
            16 => 
            array (
                'id' => 3,
                'user_id' => 2141,
                'department_id' => 1,
            ),
            17 => 
            array (
                'id' => 4,
                'user_id' => 2141,
                'department_id' => 3,
            ),
            18 => 
            array (
                'id' => 6,
                'user_id' => 2112,
                'department_id' => 1,
            ),
            19 => 
            array (
                'id' => 8,
                'user_id' => 2313,
                'department_id' => 1,
            ),
        ));
        
        
    }
}