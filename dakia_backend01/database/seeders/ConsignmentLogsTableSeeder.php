<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConsignmentLogsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('consignment_logs')->delete();
        
        
        
    }
}