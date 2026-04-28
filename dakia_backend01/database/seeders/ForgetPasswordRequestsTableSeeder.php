<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ForgetPasswordRequestsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('forget_password_requests')->delete();
        
        
        
    }
}