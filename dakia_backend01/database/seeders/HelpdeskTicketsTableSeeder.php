<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HelpdeskTicketsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('helpdesk_tickets')->delete();
        
        
        
    }
}