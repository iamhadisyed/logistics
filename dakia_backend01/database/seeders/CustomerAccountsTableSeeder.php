<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CustomerAccountsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('customer_accounts')->delete();
        
        
        
    }
}