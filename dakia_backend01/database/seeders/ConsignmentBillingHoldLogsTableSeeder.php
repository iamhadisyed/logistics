<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ConsignmentBillingHoldLogsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('consignment_billing_hold_logs')->delete();
        
        
        
    }
}