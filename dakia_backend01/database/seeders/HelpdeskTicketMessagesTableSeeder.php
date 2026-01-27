<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HelpdeskTicketMessagesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('helpdesk_ticket_messages')->delete();
        
        
        
    }
}