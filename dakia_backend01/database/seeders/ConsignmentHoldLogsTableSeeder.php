<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConsignmentHoldLogsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('consignment_hold_logs')->delete();
        
        
        
    }
}