<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConsignmentStatusLogsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('consignment_status_logs')->delete();
        
        
        
    }
}