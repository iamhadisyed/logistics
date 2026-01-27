<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TrackingDataTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('tracking_data')->delete();
        
        
        
    }
}