<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HawbLogsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('hawb_logs')->delete();
        
        
        
    }
}