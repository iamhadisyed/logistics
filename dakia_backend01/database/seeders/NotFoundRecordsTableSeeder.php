<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class NotFoundRecordsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('not_found_records')->delete();
        
        
        
    }
}