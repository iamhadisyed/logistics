<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AutoTrackingTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('auto_tracking')->delete();
        
        
        
    }
}