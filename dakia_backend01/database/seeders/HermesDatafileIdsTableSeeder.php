<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class HermesDatafileIdsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('hermes_datafile_ids')->delete();
        
        
        
    }
}